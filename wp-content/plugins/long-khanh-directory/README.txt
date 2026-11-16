CHON DANH MUC VA DIA DIEM TRONG ELEMENTOR

Trong widget "Danh sách địa điểm động", vào Nội dung > Danh sách địa điểm:

1. Nguồn danh sách = "Tự động (cấu hình cũ)": giữ nguyên cách lấy bài,
   slug danh mục, địa điểm lân cận và các tab cũ. Đây là chế độ mặc định.
2. Nguồn danh sách = "Chọn theo từng danh mục": thêm dòng vào
   "Danh mục và địa điểm". Chọn danh mục, đặt tên tab tùy ý, rồi chọn
   tự động hoặc thủ công. Khi chọn thủ công, ô tìm kiếm chỉ cung cấp
   địa điểm thuộc danh mục đó. Thứ tự hiển thị là thứ tự chọn; bỏ chọn
   rồi chọn lại để đưa một địa điểm xuống cuối.
3. Kéo thả các dòng để đổi thứ tự danh mục. "Số địa điểm" là giới hạn
   của từng danh mục. Tắt "Hiện tab danh mục" để hiện các nhóm nối tiếp.

Lựa chọn lưu slug danh mục và slug địa điểm, không lưu ID database local.
Khi render, widget tra dữ liệu đã xuất bản, không có mật khẩu và thuộc
directory của chính môi trường đang chạy. Bài bị xóa/ẩn hoặc chuyển ra
khỏi danh mục sẽ bị bỏ qua. Chọn thủ công nhưng để trống sẽ hiện trạng
thái trống, không tự lấy toàn bộ bài. Khi đổi slug cần chọn lại mục đó.

BAO TOAN BO CUC VA TRIEN KHAI

Việc cập nhật code widget không tự lưu hoặc migrate bất kỳ page/template
Elementor nào. Không có bước ghi đè Trang chủ, header, logo hay media.
Chế độ mới chỉ bật sau khi người chỉnh chủ động thay đổi cấu hình widget.
Không sửa manifest bằng tay và không import database local lên server.
Tuân theo docs/deployment-spec.md: local -> master -> prod. Gói Elementor
không đổi so với baseline sẽ giữ sửa đổi ở môi trường đích; nếu cả hai
bên đổi cùng trường metadata, importer dừng vì xung đột để giải quyết
ở local. Không bỏ kiểm tra xung đột hoặc bật force.

Kiểm tra logic local: php scripts/test-listing-selection.php
Kiểm tra gói sau khi sửa Elementor: php scripts/elementor/export.php --check
