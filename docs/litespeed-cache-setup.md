# LiteSpeed Cache: cấu hình local và kiểm thử

Phạm vi ngày 25/11/2026: cài/cấu hình **ở local**, kiểm tra đăng nhập, tài khoản
và đánh giá, chuẩn bị commit. Không push, chạy workflow hoặc thay đổi prod.

## Gói plugin

LiteSpeed Cache **7.9.4**, gói chính thức:
`https://downloads.wordpress.org/plugin/litespeed-cache.7.9.4.zip`.
SHA-256 ZIP: `cc3aff716a9843356de06b34aeacbdcee97947217c4eed6cd2d8eb20171fe0a5`.
Gói có 321 file; giữ nguyên source, license và dependencies upstream.
Đã đối chiếu 321/321 file với ZIP tải về. API checksum WordPress.org có hai
giá trị khác cho readme.txt và lang/litespeed-cache.pot; hai file này vẫn khớp
ZIP và nội dung tag SVN chính thức được đọc lại, không sửa chúng để ép khớp API.

## Chính sách đầu tiên

MU-plugin `wp-content/mu-plugins/lk-cache-policy.php` đọc profile có phiên bản
`lk-cache-policy/settings.json`. Nó đặt các constant override chính thức của
LiteSpeed **trước** khi plugin thường được nạp. Chính sách đi cùng code trong
Git, không chép options database local, URL localhost, ID hoặc credential lên
môi trường khác. Giá trị trong profile được quản lý bằng code, không chỉnh
riêng trong UI rồi kỳ vọng ghi đè constant. Tắt plugin vẫn là cách dừng plugin;
thay đổi chính sách cần sửa/kiểm tra local theo quy trình dự án.

- Public HTML TTL 300 giây. Chỉ trang chủ, trang blog/giới thiệu/chính sách đã
  ánh xạ, post, listing công khai không mật khẩu và các taxonomy công khai
  được xét cache. Trang chưa thuộc phạm vi này không cache.
- Không cache người đăng nhập, cookie phiên/reset/commenter/post password,
  request có Authorization, POST, admin/AJAX/REST, preview, search, feed hoặc
  404. Mọi query string đều bypass; không bỏ query khỏi cache key.
- Trang viết đánh giá/thêm địa điểm/profile lấy ID từ `lk_page_map` hiện tại;
  dashboard lấy URL `listing-author` của chính môi trường. Không cố định slug
  hoặc đưa ID local vào code. Header và popup đăng nhập trong trang công khai
  có nonce khách; TTL 5 phút ngắn hơn chu kỳ nonce WordPress mặc định.
- Cache Logged-in Users, Commenters, REST và Login Page: OFF. Guest Mode,
  Guest Optimization, ESI, crawler, object cache, CDN/QUIC.cloud, lazy load,
  CSS/JS/HTML minify/combine/defer và các tối ưu ảnh: chưa bật.
- Browser cache: chưa đổi trong đợt này. Header cache JS trên VPS là hạng mục
  riêng cần đối chiếu cấu hình OpenLiteSpeed trước khi triển khai.

Guard dùng `DONOTCACHEPAGE`, `nocache_headers()` và API
`litespeed_control_set_nocache`. Nếu profile hỏng/thiếu, cache được tắt thay
vì ép cache. Không dùng force-cache để vượt loại trừ.

Thêm/sửa/xóa/duyệt đánh giá `lk_review` hoặc đổi metadata hiển thị của đánh
giá phát purge cho listing và các trang home/explore của môi trường đó. Lưu
listing cũng purge các trang tổng hợp. Đây là thông báo cache; không sửa bài,
đánh giá hoặc layout trong lúc đọc. Integration Elementor của plugin chính
thức purge cache khi Regenerate CSS & Data, phù hợp bước triển khai đang có.

## Cài local

```powershell
php scripts/cache/setup-local.php
```

Script kiểm tra hostname local, chặn gửi mail/HTTP ngoài, backup danh sách
plugin, options LiteSpeed và runtime config vào `.codex-tools/` rồi kích hoạt
plugin và lưu profile. Plugin có thể cập nhật **wp-config.php/.htaccess local**
và tạo lại cache Elementor. Các file runtime này không đưa vào commit.
Không thay blog_public, permalink, cấu hình OAuth, nội dung hay layout.

Runtime `wp-content/litespeed/`, `.litespeed_conf.php/.dat`, object-cache.php,
advanced-cache.php và .htaccess.bk được loại khỏi Git/deploy; không mang cache,
drop-in hoặc credential của một máy sang máy khác. Thư mục source plugin
`wp-content/plugins/litespeed-cache/` vẫn được commit đầy đủ.

## Kiểm tra

```powershell
php scripts/test-cache-policy.php
php scripts/test-account-links.php
php scripts/test-saved-places.php
php scripts/test-dashboard-reviews.php
php scripts/elementor/export.php --check
php scripts/elementor/validate.php
php scripts/elementor/test.php
python scripts/elementor/test-deploy-check.py
```

Policy test chạy 20 request độc lập với API LiteSpeed thật, kiểm tra settings,
public TTL, cookie/session, mọi URL nhạy cảm và gửi review thật; fixtures dùng
transaction rollback. Các bài, user, review, metadata và manifest phải giữ
fingerprint trước/sau. Các test cũ kiểm tra đăng ký/reset mật khẩu, saved places
và lịch sử đánh giá với LiteSpeed đang bật. Chặn email/HTTP ngoài khi chạy test.

XAMPP/PHP CLI không thể chứng minh engine cache HIT/MISS. Kiểm tra HTTP cần
OpenLiteSpeed thật, gồm guest MISS -> HIT, hai phiên người dùng độc lập luôn
no-cache, form có nonce hoạt động, review mới làm mất cache cũ và khách thấy
review mới. Môi trường Docker QA chỉ dùng bản sao database **local**, tài khoản
thử riêng và listener loopback; không kết nối hoặc nhập dữ liệu lên prod.
Không commit SQL, credentials test, ảnh chụp hoặc runtime của Docker QA.

Kết quả kiểm tra ngày 25/11/2026:

- 20/20 policy cases đạt với API LiteSpeed thật; ba bộ hồi quy account-links,
  saved-places và dashboard-reviews đạt, fixtures rollback.
- Docker local: OpenLiteSpeed 1.9.4, PHP 8.5.13 (local XAMPP là PHP 8.5.12).
  Trang công khai trả MISS rồi HIT từ engine thật. Trang tài khoản và các
  endpoint nhạy cảm trả LiteSpeed no-cache.
- Hai tài khoản thử đăng nhập qua UI, mỗi phiên chỉ thấy danh tính và saved
  places của mình. POST thiếu nonce bị từ chối. Đăng ký, quên mật khẩu và
  đăng xuất qua UI đạt; mail bị chặn trong môi trường QA.
- Gửi review qua form thật làm mất cache listing cũ; khách thấy review ngay,
  sau đó bản HTML mới trả HIT. Lần ghi cache có thể hoàn tất sau response nên
  test chờ hữu hạn khi kiểm tra HIT tiếp theo, không ép cache hoặc bỏ kiểm tra.
- Form tài khoản vừa viewport 390/1280px; không có uncaught JavaScript error
  ở các luồng kiểm tra. Không bật minify/defer/lazy load.
- Export --check và validate đạt; 24/24 test engine layout và 19 test kiểm tra
  deploy HTTP đạt. Nội dung post/users/comments/taxonomy nguồn giữ fingerprint;
  postmeta cache được tạo lại khi cài, manifest Elementor không đổi.

Các container/database QA được đặt nhãn riêng và dọn sau kiểm tra. Không
thay đổi các container khác, database WordPress local gốc hay VPS/prod.

## Triển khai sau này

Commit này chỉ chuẩn bị source/cấu hình và kiểm thử local. Theo
`deployment-spec.md`, khi được yêu cầu triển khai: master thành công và kiểm
tra trước, rồi merge master vào prod. Git không tự đồng bộ trạng thái kích
hoạt plugin. Kích hoạt LiteSpeed trên từng môi trường là thao tác riêng của
chủ site trong quy trình được chấp thuận, không chạy setup-local trên server.
Không import DB hoặc copy wp-config/.htaccess local lên đích.

Sau kích hoạt phải kiểm tra HIT/MISS và bypass đăng nhập/tài khoản/đánh giá
trên chính môi trường đích. Không coi kết quả Docker local là xác nhận prod.

Tài liệu API: https://docs.litespeedtech.com/lscache/lscwp/api/
và https://docs.litespeedtech.com/lscache/lscwp/constants/
