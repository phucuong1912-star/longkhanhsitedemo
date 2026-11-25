# Tên thương hiệu trong tiêu đề SEO

Theo yêu cầu khách hàng, tên dùng trong tiêu đề SEO là **Thổ Địa Tiền Hải**.
Module `long-khanh-directory/includes/seo-title.php` áp dụng tên này khi render
tiêu đề WordPress, và qua filter `wpseo_title` nếu môi trường có Yoast SEO.

Tiêu đề trang con giữ phần nội dung hiện tại, ví dụ:
`Tên địa điểm | Thổ Địa Tiền Hải`. Nếu tiêu đề đã có tên thương hiệu thì không
thêm lần nữa. Tên site cũ ở đầu/cuối tiêu đề, khi là một phần tách riêng bằng
dấu phân cách, được thay bằng tên mới. Tên bài viết trong nội dung và database
không bị đổi. Tiêu đề SEO riêng vẫn giữ phần nội dung, bổ sung tên nếu thiếu.

Local hiện có SEO Repair Kit đang kích hoạt, chưa cài Yoast. Thay đổi này
không cài/kích hoạt plugin SEO thứ hai, không sửa thiết lập sitemap, robots,
canonical, description hoặc schema. Không đồng bộ bảng options hay metadata
SEO từ local lên server. Tên website trong WordPress và logo giữ cấu hình
hiện có; đây là quy tắc cho tiêu đề SEO.

Kiểm tra HTML công khai ngày 25/11/2026: trang chủ prod trả HTTP 200 và
`<title>Thổ Địa Tiền Hải</title>` trước thay đổi này. Chưa xác nhận từ HTML đó
plugin SEO nào đang kích hoạt trên prod.

Triển khai theo `deployment-spec.md`: kiểm tra/commit local, người dùng push
master, xác nhận workflow và HTML master, rồi merge master vào prod và push.
Sau deploy kiểm tra `<title>` trang chủ, trang thường, địa điểm và bài viết;
không coi commit local là cấu hình đã áp dụng trên prod. Nếu có full-page
cache, cần xác nhận HTML mới sau khi cache hết hạn hoặc được làm mới bởi
quy trình triển khai đã cấu hình.

API tham khảo: https://developer.yoast.com/features/seo-tags/titles/api/
