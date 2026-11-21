"""Exercise the pure version guard in isolation; never run a deployment or SSH."""
import os
from pathlib import Path
import re
import shutil
import subprocess
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[1]
SOURCE = (ROOT / ".github/scripts/deploy-prod.sh").read_text(encoding="utf-8")
MATCH = re.search(r"^require_core_version\(\) \{\n.*?^\}", SOURCE, re.M | re.S)
if not MATCH:
    raise RuntimeError("The deployment core guard could not be found")
GIT_BASH = Path("C:/Program Files/Git/bin/bash.exe")
BASH = str(GIT_BASH) if GIT_BASH.is_file() else shutil.which("bash")
if not BASH:
    raise RuntimeError("Bash is required for isolated shell guard tests")


class CoreVersionGuardTests(unittest.TestCase):
    def run_guard(self, installed, expected):
        with tempfile.TemporaryDirectory(prefix="lk-core-guard-") as tmp:
            marker = Path(tmp) / "transfer-started"
            # Only the pure function is extracted, with a fail handler and a
            # simulated transfer marker. No SSH, backup or deployment entrypoint.
            program = (
                "set -Eeuo pipefail\n"
                "fail() { printf 'Deployment stopped: %s\\n' \"$*\" >&2; exit 1; }\n"
                + MATCH.group(0)
                + '\nrequire_core_version "$1" "$2"\n'
                + 'printf transfer > "$3"\n'
            )
            env = dict(os.environ, PROD_SSH_PRIVATE_KEY="fixture-secret-never-log")
            result = subprocess.run(
                [BASH, "--noprofile", "--norc", "-c", program, "guard-test",
                 installed, expected, marker.as_posix()],
                capture_output=True, text=True, env=env, timeout=15,
            )
            self.assertNotIn("fixture-secret-never-log", result.stdout + result.stderr)
            return result, marker.exists()

    def test_matching_core_allows_transfer(self):
        result, transferred = self.run_guard("7.1.5", "7.1.5")
        self.assertEqual(result.returncode, 0, result.stderr)
        self.assertTrue(transferred)

    def test_newer_destination_refuses_downgrade(self):
        result, transferred = self.run_guard("7.1.5", "7.1.4")
        self.assertNotEqual(result.returncode, 0)
        self.assertFalse(transferred)
        self.assertIn("source=7.1.4; destination=7.1.5", result.stderr)

    def test_older_destination_requires_explicit_core_alignment(self):
        result, transferred = self.run_guard("7.1.4", "7.1.5")
        self.assertNotEqual(result.returncode, 0)
        self.assertFalse(transferred)
        self.assertIn("WordPress core version mismatch", result.stderr)

    def test_major_version_difference_is_refused(self):
        result, transferred = self.run_guard("8.0.0", "7.1.5")
        self.assertNotEqual(result.returncode, 0)
        self.assertFalse(transferred)

    def test_missing_or_malformed_versions_are_refused(self):
        for installed, expected in [("", "7.1.5"), ("7.1.5", ""),
                                    ("unexpected", "7.1.5"), ("7.1.5", "7..1")]:
            with self.subTest(installed=installed, expected=expected):
                result, transferred = self.run_guard(installed, expected)
                self.assertNotEqual(result.returncode, 0)
                self.assertFalse(transferred)
                self.assertIn("valid WordPress core versions", result.stderr)


if __name__ == "__main__":
    unittest.main(verbosity=2)
