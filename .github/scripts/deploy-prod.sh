#!/usr/bin/env bash
# Code deploy only. Initial database/media migration is a separate operation.
set -Eeuo pipefail
umask 077

fail() { printf 'Deployment stopped: %s\n' "$*" >&2; exit 1; }
[[ ${GITHUB_REF:-} == refs/heads/prod ]] || fail 'Only the prod branch can deploy to this VPS.'
[[ ${PROD_SSH_HOST:-} =~ ^[a-zA-Z0-9.-]+$ ]] || fail 'Invalid SSH host.'
[[ ${PROD_SSH_PORT:-} =~ ^[0-9]+$ ]] && (( PROD_SSH_PORT > 0 && PROD_SSH_PORT < 65536 )) || fail 'Invalid SSH port.'
[[ ${PROD_SSH_USER:-} =~ ^[a-z_][a-z0-9_-]*$ ]] || fail 'Invalid SSH user.'
[[ ${PROD_WEB_ROOT:-} =~ ^/[a-zA-Z0-9_./-]+$ ]] || fail 'Invalid web root.'
[[ $PROD_WEB_ROOT != *..* && $PROD_WEB_ROOT != */ && $PROD_WEB_ROOT == /*/*/* ]] || fail 'Web root must be a specific absolute directory.'
[[ ${PROD_SITE_URL:-} =~ ^https://[a-zA-Z0-9.-]+$ ]] || fail 'Expected an HTTPS site origin without a trailing slash.'
[[ ${GITHUB_SHA:-} =~ ^[0-9a-f]{40}$ ]] || fail 'Missing commit SHA.'
[[ ${GITHUB_RUN_ID:-} =~ ^[0-9]+$ && ${GITHUB_RUN_ATTEMPT:-} =~ ^[0-9]+$ ]] || fail 'Missing Actions run identifiers.'
prod_wp_version=$(awk -F "'" '$1 ~ /^\$wp_version = / { print $2; exit }' wp-includes/version.php)
[[ $prod_wp_version =~ ^[0-9.]+$ ]] || fail 'Could not read the source WordPress version.'
[[ -n ${PROD_SSH_PRIVATE_KEY:-} && -n ${PROD_SSH_KNOWN_HOSTS:-} ]] || fail 'Configure PROD_SSH_PRIVATE_KEY and PROD_SSH_KNOWN_HOSTS in GitHub secrets.'

prod_temp=$(mktemp -d "${RUNNER_TEMP:?}/lk-prod.XXXXXX")
trap 'rm -f -- "$prod_temp/id_ed25519" "$prod_temp/known_hosts" "$prod_temp/ssh_config"; rmdir -- "$prod_temp" 2>/dev/null || true' EXIT
printf '%s\n' "$PROD_SSH_PRIVATE_KEY" | tr -d '\r' > "$prod_temp/id_ed25519"
printf '%s\n' "$PROD_SSH_KNOWN_HOSTS" | tr -d '\r' > "$prod_temp/known_hosts"
cat > "$prod_temp/ssh_config" <<CONFIG
Host prod-vps
    HostName $PROD_SSH_HOST
    Port $PROD_SSH_PORT
    User $PROD_SSH_USER
    IdentityFile $prod_temp/id_ed25519
    UserKnownHostsFile $prod_temp/known_hosts
    IdentitiesOnly yes
    BatchMode yes
    StrictHostKeyChecking yes
    ConnectTimeout 20
CONFIG
unset PROD_SSH_PRIVATE_KEY PROD_SSH_KNOWN_HOSTS

prod_stage="/var/tmp/lk-prod-${GITHUB_RUN_ID}-${GITHUB_RUN_ATTEMPT}"
ssh -T -F "$prod_temp/ssh_config" prod-vps bash -s -- "$PROD_WEB_ROOT" "$PROD_SITE_URL" "$prod_stage" "$prod_wp_version" <<'REMOTE'
set -Eeuo pipefail
prod_root=$1; prod_site=$2; prod_stage=$3; prod_version=$4
test "$(readlink -f "$prod_root")" = "$prod_root"
test -f "$prod_root/wp-config.php"
test "$(head -n 1 "$prod_root/.lk-prod-deploy")" = "$prod_site"
test "$(wp --allow-root --path="$prod_root" core version)" = "$prod_version"
command -v rsync >/dev/null
command -v php >/dev/null
test ! -e "$prod_stage"
install -d -m 700 "$prod_stage"
REMOTE

# No vendor exclusion: WordPress React and Elementor vendor are required.
rsync -az --safe-links --exclude-from=.github/prod-excludes.txt \
  -e "ssh -F $prod_temp/ssh_config" ./ "prod-vps:$prod_stage/"

ssh -T -F "$prod_temp/ssh_config" prod-vps bash -s -- "$PROD_WEB_ROOT" "$PROD_SITE_URL" "$prod_stage" "$GITHUB_SHA" <<'REMOTE'
set -Eeuo pipefail
umask 077
prod_root=$1; prod_site=$2; prod_stage=$3; prod_sha=$4
test "$(readlink -f "$prod_root")" = "$prod_root"
test "$(head -n 1 "$prod_root/.lk-prod-deploy")" = "$prod_site"
test -s "$prod_stage/wp-includes/js/dist/vendor/react.min.js"
test -s "$prod_stage/wp-content/plugins/elementor/vendor/autoload.php"
find "$prod_stage/wp-content/themes/listingpro-longkhanh-child" "$prod_stage/wp-content/plugins/long-khanh-directory" \
  -name '*.php' -type f -print0 | while IFS= read -r -d '' prod_file; do php -l "$prod_file" >/dev/null; done

prod_backup="/root/longkhanh-prod-backups/code-$(date -u +%Y%m%dT%H%M%SZ)-${prod_sha:0:12}"
install -d -m 700 "$prod_backup"
wp --allow-root --path="$prod_root" db export "$prod_backup/database.sql" --add-drop-table
gzip "$prod_backup/database.sql"
tar --exclude='./wp-content/uploads' --exclude='./wp-content/cache' --exclude='./wp-content/debug.log' \
  -czf "$prod_backup/code.tar.gz" -C "$prod_root" .
printf 'Backup: %s\n' "$prod_backup"

# Preserve database configuration, uploads and server-generated files.
# No --delete: runtime/server files are never removed by a code push.
rsync -a --chown=www-data:www-data --delay-updates "$prod_stage/" "$prod_root/"
wp --allow-root --path="$prod_root" --url="$prod_site" cache flush
wp --allow-root --path="$prod_root" --url="$prod_site" eval 'if (function_exists("lk_invalidate_public_index")) { lk_invalidate_public_index(); }'
wp --allow-root --path="$prod_root" --url="$prod_site" elementor flush-css --regenerate
chown -R www-data:www-data "$prod_root/wp-content/uploads/elementor"
printf '%s\n' "$prod_sha" > "$prod_root/.lk-prod-commit"
curl --fail --silent --show-error --retry 3 --output /dev/null "$prod_site/?lk_deploy=$prod_sha"
case "$prod_stage" in /var/tmp/lk-prod-[0-9]*-[0-9]*) rm -rf -- "$prod_stage";; *) exit 1;; esac
printf 'Deployment healthy: %s (%s)\n' "$prod_site" "$prod_sha"
REMOTE
