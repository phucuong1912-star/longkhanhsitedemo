# Quy tắc làm việc bắt buộc

Đọc [spec triển khai](docs/deployment-spec.md) trước mọi thay đổi có liên quan
đến deploy, Elementor, database hoặc môi trường master/prod.

## Local là nơi chỉnh sửa duy nhất

- Chỉ sửa code, giao diện và tài liệu Elementor trong workspace/local WordPress.
- Không sửa trực tiếp master hosting hoặc prod bằng SSH, SFTP, FTP, WP-CLI,
  trình quản trị WordPress hay công cụ trình duyệt. Không chạy các script cũ
  `.codex-tools/prod_*`, `deploy-*`, `upload-prod-*` để triển khai thủ công.
- Các script deploy có trong `.github/` chỉ do GitHub Actions thực thi.
- Được đọc log Actions và kiểm tra HTTP công khai để xác nhận trạng thái.
  Không tự đổi secrets, tài khoản, key hoặc cấu hình hosting.
- Mặc định chỉ chuẩn bị và kiểm tra commit local. Người dùng tự push; không
  push, merge remote hoặc bấm chạy workflow nếu chưa được yêu cầu rõ ràng.

## Luồng triển khai duy nhất

1. Sửa và kiểm tra local; xuất thay đổi Elementor vào gói versioned trong Git.
2. Commit/push `master` → workflow master tự deploy vào hosting master.
3. Chờ workflow master thành công và kiểm tra kết quả.
4. Merge **master vào prod**, giữ các cấu hình riêng của prod; push `prod` →
   workflow prod tự deploy vào prod. Không đảo thứ tự và không bỏ qua master.
5. Mọi bản sửa tiếp theo quay lại local/master; không hotfix trực tiếp prod.

## Dữ liệu phải được bảo toàn

- Không import database local lên server trong deploy thường ngày. Không dùng
  SQL search/replace toàn DB, reset database, restore DB cũ hoặc dựng lại demo.
- Không xóa/ghi đè bài viết (`post`), địa điểm (`listing`), review, comments,
  users, đơn hàng hay uploads đang có trên môi trường đích.
- Chỉ đồng bộ metadata Elementor của các page/template được liệt kê trong
  `wp-content/mu-plugins/lk-layout-sync/scopes.json`.
- Không đưa ID database và URL localhost vào môi trường khác. Dùng exporter
  và importer của dự án để ánh xạ document/media và URL theo từng môi trường.
- Không tự mở rộng allowlist, bỏ kiểm tra xung đột, bỏ backup hoặc bật force
  để làm cho deploy xanh. Giải quyết nguyên nhân ở local.
- Không commit secrets, `wp-config.php`, SQL dump hoặc cache Elementor.

## Trước khi bàn giao

- Chạy `php scripts/elementor/export.php --check` và các kiểm tra phù hợp.
- Commit cả manifest/assets Elementor nếu chúng thay đổi; Git hooks kiểm tra
  việc này. Không bỏ hook để push thiếu thay đổi Elementor.
- Báo rõ đã làm ở local hay đã deploy. Không nói đã lên server chỉ vì commit
  local hoặc push thành công; phải có workflow và kiểm tra môi trường đích.

Lịch sử script chuyển host/khôi phục có trong `.codex-tools/` chỉ là tài liệu
cũ, không phải quyền triển khai trực tiếp và không thay thế quy tắc trên.
