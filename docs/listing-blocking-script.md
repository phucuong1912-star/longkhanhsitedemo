# Trang địa điểm trắng lâu — 26/11/2026

## Bằng chứng từ HTTP công khai

Trang được kiểm tra:
`https://thodiatienhai.com/dia-diem/tre-tron-tien-hai/`.

- GET đầu tiên: HTTP 200, LiteSpeed MISS, byte đầu 1,37 giây.
- GET tiếp theo: HTTP 200, LiteSpeed HIT, byte đầu 0,26 giây.
- HTML vẫn có font Quicksand cùng origin của bản sửa trước.
- Trong `<head>` có script đồng bộ `datelocale-js`, URL
  `https://sandbox.listingprowp.com/datepicker-locales/datepicker-vi.js?ver=7.1.5`.
  Script không có `async` hoặc `defer` và nằm trước `<body>`.
- Tên miền sandbox không phân giải được từ máy kiểm tra; curl báo mã lỗi 6,
  WebKit báo `Could not resolve hostname`.

WebKit mới, viewport 390×844, phiên chưa đăng nhập, chỉ cho phép GET/HEAD:

| Phép thử trên trang prod | Kết quả |
| --- | --- |
| DNS lỗi nhanh trên máy thử | FCP 1,04 giây, load 1,90 giây |
| Giữ request script sandbox 12 giây rồi hủy trong trình duyệt thử | Sau 3 giây: `readyState=loading`, chưa có body, chưa có paint; FCP 12,91 giây |

Phép thử chứng minh script này có thể giữ trang trắng khi kết nối bị treo.
Chưa đo trực tiếp được thời gian DNS/timeout trên iPhone của người dùng, nên
không coi thời gian 12 giây giả lập là thời gian lỗi thực tế trên thiết bị.
Mẫu script này chặn parser theo [tài liệu HTML script của MDN](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/script).

## Sửa ở local

Child theme bỏ enqueue handle `datelocale` trên giao diện canvas được quản lý
bởi `lk_is_site()`, chạy sau enqueue của parent và trước khi in script.
Đó là locale datepicker của dashboard ListingPro; các form canvas dùng các
control riêng/native. Không đổi template Elementor, nội dung, script bản đồ,
script tài khoản, lưu địa điểm, đánh giá hoặc chính sách cache.

Các trang giao diện cũ ngoài canvas và admin giữ xử lý hiện tại. Bản sửa
không thay thế datepicker cho dashboard cũ.

## Xác minh local

```text
php scripts/test-listing-assets.php
php scripts/test-local-fonts.php
php scripts/elementor/export.php --check
php scripts/elementor/validate.php
```

Test listing render template WordPress/Elementor thật, chủ động enqueue cùng
script đang có trên prod để tái hiện điều kiện bất kể URL dashboard local.
Assertion không có script sandbox thất bại trước sửa và đạt sau sửa.
Kiểm tra giữ font, control Lưu, form/nonce đánh giá, popup tài khoản, thư viện
bản đồ; document listing và manifest không thay đổi. Giao diện cũ vẫn enqueue
được locale.

WebKit local được giả lập cùng enqueue của prod và trì hoãn sandbox 12 giây:
không có request sandbox; sau 3 giây tính từ header đã có body và paint.
Nút lưu/bỏ lưu, mở đánh giá, menu, đăng nhập và danh sách đã lưu đạt; không gửi
form. Không lỗi JS/HTTP, font tiếng Việt tải được, không tràn ngang. Thời gian
local không dùng để so sánh tốc độ với prod do môi trường khác nhau.

Một CSS CubeWP trên prod trả 404 trong lần đo; không thấy gây tình trạng
parser chờ như script sandbox. Bản sửa này chỉ xử lý phụ thuộc đã tái hiện.

## Triển khai và kiểm tra đích

Chỉ commit local, người dùng tự push master. Sau workflow master thành công
và kiểm tra master, merge master vào prod theo `deployment-spec.md`.
Không sửa VPS, cấu hình cache, `.htaccess`, `wp-config.php` hay database prod.

Sau deploy, kiểm tra nguồn HTML của trang địa điểm không còn
`<script ... sandbox.listingprowp.com ...>`; thử Safari Riêng tư trên chính
iPhone gặp lỗi qua Wi-Fi và 4G/5G. Kiểm tra cả trạng thái cache MISS/HIT vì
HTML cache cũ có thể vẫn chứa script. Chưa được báo prod đã hết lỗi chỉ từ
commit hoặc kết quả local.
