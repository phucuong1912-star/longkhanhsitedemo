# Tên thương hiệu trong tiêu đề SEO

Theo yêu cầu khách hàng, tên dùng trong tiêu đề SEO là **Thổ Địa Tiền Hải**.
Module `long-khanh-directory/includes/seo-title.php` áp dụng tên này khi render
tiêu đề WordPress, và qua filter `wpseo_title` nếu môi trường có Yoast SEO.

Tiêu đề trang con giữ phần nội dung hiện tại, ví dụ:
`Tên địa điểm | Thổ Địa Tiền Hải`. Nếu tiêu đề đã có tên thương hiệu thì không
thêm lần nữa. Tên site cũ ở đầu/cuối tiêu đề, khi là một phần tách riêng bằng
dấu phân cách, được thay bằng tên mới. Tên bài viết trong nội dung và database
không bị đổi. Tiêu đề SEO riêng vẫn giữ phần nội dung, bổ sung tên nếu thiếu.

## Cài Yoast ở local

Ngày 25/11/2026, cài và kích hoạt **Yoast SEO 28.9** từ gói WordPress.org
chính thức. SEO Repair Kit được tắt ở local để tránh xuất SEO hai lần; thư mục
plugin và các option/metadata cũ được giữ lại, không uninstall hoặc nhập đè.

Nguồn: `https://downloads.wordpress.org/plugin/wordpress-seo.28.9.zip`.
SHA-256 gói ZIP:
`1f1af9d3dff70288b37e6edc04e27cff4979b258f62960c0b91c092614bf2301`.
Đã đối chiếu đủ 1.984 file với checksum chính thức của WordPress.org.
Gói yêu cầu WordPress >= 7.0 và PHP >= 7.4; local là WordPress 7.1.5/PHP 8.5.12.
Source plugin được giữ nguyên, gồm license và dependencies đi kèm.

Chạy lại cài đặt local bằng `php scripts/seo/setup-local.php`. Script chỉ chấp
nhận WordPress có hostname local, chặn email/HTTP ngoài trong tiến trình cài
đặt và lưu backup cấu hình liên quan trong `.codex-tools/` không commit.
Nó kích hoạt Yoast, tắt SEO Repair Kit và lưu hai thiết lập Yoast:

- `website_name`: `Thổ Địa Tiền Hải`.
- `title-post`: `%%title%% %%page%% | Thổ Địa Tiền Hải`.

Template áp dụng cho bài WordPress `post` chưa có tiêu đề SEO riêng, kể cả
bài mới. Tiêu đề SEO riêng vẫn giữ câu chữ và thêm tên thương hiệu khi thiếu.
Module có filter defaults cho lần kích hoạt Yoast mới ở từng môi trường và
filter title cho HTML hiện tại. Không cần sửa hàng loạt `_yoast_wpseo_title`,
`post_title`, `post_content`, slug, ảnh hoặc dữ liệu Elementor.

Tên website trong WordPress, logo và heading H1 vẫn lấy dữ liệu hiện có.
SEO Repair Kit vốn nạp font Figtree cho body/button/input/select/textarea.
Khi chuyển sang Yoast, module nạp `assets/seo-font-compat.css` với đúng CSS đó
để giữ typography; không sửa vendor hoặc giữ phần xuất SEO của plugin cũ.
Không đổi index/noindex của site để đưa local lên Google. Yoast có thể tạo
indexables và làm mới cache khi kích hoạt; đây là dữ liệu của plugin/cache,
không phải chỉnh nội dung hay bố cục.

## Đưa lên master và prod

Kiểm tra HTML công khai ngày 25/11/2026: trang chủ prod trả HTTP 200 và
`<title>Thổ Địa Tiền Hải</title>` trước thay đổi này. Chưa xác nhận từ HTML đó
plugin SEO nào đang kích hoạt trên prod.

Triển khai theo `deployment-spec.md`: kiểm tra/commit local, người dùng push
master, xác nhận workflow và HTML master, rồi merge master vào prod và push.
Git đưa file Yoast và module tiêu đề lên server; nó **không sao chép danh sách
plugin kích hoạt hoặc option database local**. Sau khi file đã lên master,
chủ site cần tắt SEO Repair Kit nếu đang bật và kích hoạt Yoast trong quản trị
WordPress master. Với lần cài Yoast mới, module defaults đặt sẵn template trên.
Nếu Yoast đã được cài trước đó, đặt Website name và template Posts như trên
trong Yoast Settings. Kiểm tra master xong mới tiếp tục luồng prod và thực hiện
kích hoạt/cấu hình trên chính môi trường prod. Agent không đăng nhập/chỉnh
server trực tiếp, không dùng script setup-local trên server và không nhập DB.

Trước khi chuyển plugin ở môi trường đích, chủ site cần đối chiếu thiết lập
SEO riêng đang có, đặc biệt description/canonical/noindex và redirect của
plugin cũ; bản cài này không tự nhập các thiết lập đó từ local hay plugin cũ.

Sau kích hoạt kiểm tra `<title>` trang chủ, trang thường, địa điểm và bài viết;
không coi commit local là cấu hình đã áp dụng trên prod. Nếu có full-page
cache, cần xác nhận HTML mới sau khi cache hết hạn hoặc được làm mới bởi
quy trình triển khai đã cấu hình.

## Kiểm tra local

- `php scripts/test-seo-titles.php`: dùng Yoast thật và toàn bộ post đang
  publish; kiểm tra template, tên không lặp, custom title và dấu phân cách
  HTML entity; fingerprint bài/metadata/gói Elementor trước/sau không đổi.
- `php scripts/elementor/export.php --check`, `validate.php` và `test.php`.
- HTTP local: kiểm tra title của mọi post publish và đối chiếu phần hiển thị
  trang chủ/post/page/listing trước/sau kích hoạt. Bỏ qua vé quảng cáo có hạn
  và mã phiên bản cache khi đối chiếu. Không commit HTML, báo cáo hoặc cache.

Kết quả ngày 25/11: 10/10 post trả HTTP 200, mỗi bài có một thẻ title với tên
thương hiệu đúng một lần. DOM hiển thị của 4 loại trang đối chiếu giữ nguyên;
CSS font cũ được giữ bằng module compatibility. Toàn bộ hàng post (gồm title,
content, slug), bảng nội dung khác và gói layout không đổi; postmeta cache
Elementor được tạo lại. Export/check và validate gói đạt, 24/24 test layout đạt.
Source Yoast có whitespace theo upstream; kiểm tra whitespace chỉ áp dụng
phần code dự án, không sửa vendor để làm sạch định dạng.

Tài liệu: https://developer.yoast.com/features/seo-tags/titles/api/
và https://yoast.com/help/how-to-modify-default-snippet-templates-in-yoast-seo/
