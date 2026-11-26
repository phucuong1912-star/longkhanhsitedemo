# Font và màn hình trắng trên điện thoại — 26/11/2026

## Bằng chứng và giới hạn

Người dùng báo iPhone chưa đăng nhập bị trắng lâu trên cả Wi-Fi, 4G/5G,
Chrome và Safari Riêng tư; `/license.txt` hiện chữ ngay. Kết quả này chưa
xác định được tài nguyên cụ thể bị treo trên thiết bị đó.

Kiểm tra HTTP công khai từ máy phát triển: HTML trang chủ trả byte đầu trong
khoảng 0,24–0,88 giây, có LiteSpeed MISS rồi HIT. WebKit chạy độc lập tải
trang prod với FCP khoảng 4,16 giây; một stylesheet Google Fonts mất 2,73
giây. Khi chỉ trì hoãn stylesheet Google Fonts 12 giây trong trình duyệt thử,
FCP tăng lên 12,42 giây. Đây là bằng chứng cho một phụ thuộc có thể chặn
hiển thị, chưa phải bằng chứng giải thích toàn bộ thời gian chờ 5 phút.

## Thay đổi local

- Đóng gói Quicksand variable 300–700, gồm Latin, Latin mở rộng và tiếng Việt,
  tải từ cùng origin với website, dùng `font-display: swap`.
- Giữ font Quicksand đang được `branding.css` áp dụng; không đổi nội dung,
  layout Elementor, form hoặc chính sách cache thành viên.
- Trên giao diện do child theme quản lý, bỏ stylesheet text font dư thừa của
  ListingPro, Redux và Elementor, kể cả font được phát hiện khi render muộn.
  Bỏ stylesheet tương thích Figtree vốn đã bị Quicksand ghi đè.
- Giữ stylesheet icon. Không tắt Google Fonts cho admin hoặc các trang giao
  diện cũ ngoài phạm vi quản lý của child theme.
- Nguồn, checksum và giấy phép SIL OFL nằm trong `assets/fonts/` của child theme.

## Kiểm tra

```text
php scripts/test-local-fonts.php
php scripts/elementor/export.php --check
php scripts/elementor/validate.php
```

Integration kiểm tra head WordPress thật, style Elementor phát sinh muộn,
icon, checksum font, và phạm vi admin/giao diện cũ.

WebKit local ở viewport 390×844, phiên mới và chỉ cho phép GET/HEAD:

| Trường hợp | Kết quả |
| --- | --- |
| Trì hoãn Google Fonts 12 giây | Không có request Google Fonts; font Quicksand tiếng Việt tải thành công; FCP khoảng 4,29 giây |
| Chặn cả ba file WOFF2 local | Trang vẫn hiển thị bằng font dự phòng; FCP khoảng 5,65 giây |
| Menu, popup đăng nhập, danh sách đã lưu | Mở/đóng thành công trong cả hai trường hợp |
| JavaScript, HTTP, chiều rộng mobile | Không lỗi JS/HTTP; không tràn ngang |

Các thời gian local và prod khác môi trường nên không dùng làm số đo mức
tăng tốc. Kiểm tra popup không bao gồm gửi form hoặc đăng nhập tài khoản thật.

## Triển khai và xác nhận sau này

Chỉ chuẩn bị commit local. Người dùng push master, kiểm tra workflow và môi
trường master trước khi merge master vào prod theo deployment-spec.md.
Không sửa cấu hình VPS, `.htaccess`, `wp-config.php` hoặc database prod.

Sau khi triển khai thành công, kiểm tra trang chủ và một địa điểm từ phiên
chưa đăng nhập: CSS font phải cùng origin, không có stylesheet Google Fonts
hoặc import Figtree, chữ tiếng Việt và icon đúng. Kiểm tra lại bằng chính
iPhone gặp lỗi trên Safari Riêng tư và 4G/5G. Nếu vẫn trắng lâu, cần thu
waterfall từ thiết bị để xác định request bị treo; chưa được báo lỗi 5 phút
đã giải quyết chỉ dựa trên kết quả local.
