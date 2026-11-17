# Kết nối Google và Facebook

Code giao diện đã nối vào Nextend Social Login đang có trong dự án. Plugin
chịu trách nhiệm OAuth, xác minh token, tạo/liên kết tài khoản và phiên đăng
nhập WordPress. Chỉ nhà cung cấp đã cấu hình, xác minh và bật mới có link
đăng nhập thật. Khi chưa sẵn sàng, nút bị vô hiệu hóa; đăng nhập bằng mật khẩu
vẫn dùng được. Sau đăng nhập xã hội, người dùng trở về trang chủ cùng môi trường.

## Xem trạng thái và callback

Đăng nhập WordPress bằng quản trị viên, mở **Tiền Hải** để xem trạng thái và
callback của từng bên. Cấu hình thật nằm ở **Cài đặt → Nextend Social Login**.
Không nhập Secret trong Elementor, mã nguồn, manifest, chat hoặc GitHub.

Mỗi môi trường có database cấu hình riêng. Deploy code không mang cấu hình
Nextend từ local lên master/prod. Agent không tạo app hoặc sửa key trên prod.
Chủ website thực hiện phần thiết lập ứng dụng và cấu hình môi trường đích.

## Google

1. Mở [Google Cloud Console](https://console.cloud.google.com/), tạo/chọn project.
2. Trong Google Auth Platform, thiết lập Branding, Audience và thông tin liên hệ.
   Chọn đối tượng External nếu phục vụ người dùng ngoài tổ chức; thêm tài khoản
   thử khi ứng dụng đang ở chế độ Testing.
3. Tạo OAuth Client loại **Web application**. Điền Authorized redirect URI
   bằng **đúng callback của Google** trong Nextend, gồm cả query string.
4. Sao chép Client ID và Client Secret vào trang Google trong Nextend của đúng
   môi trường, lưu, chạy **Verify Settings**, rồi **Enable** khi xác minh thành công.
5. Khi mở cho người dùng thật, hoàn tất các yêu cầu xuất bản/xác minh mà Google
   hiển thị. Không chỉ thử bằng tài khoản đang sở hữu project.

Giao diện console có thể thay đổi; xem [hướng dẫn Google của Nextend](https://social-login.nextendweb.com/documentation/providers/google/).

## Facebook

1. Mở [Meta for Developers](https://developers.facebook.com/apps/), tạo ứng dụng
   có use case đăng nhập/xác thực người dùng bằng Facebook theo hướng dẫn Nextend.
2. Cấu hình website, domain, email liên hệ và các URL chính sách quyền riêng tư,
   xóa dữ liệu mà Meta yêu cầu. Dùng thông tin thật của website.
3. Trong Facebook Login, thêm **đúng callback Facebook** của Nextend vào danh
   sách Valid OAuth Redirect URIs; giữ các thiết lập OAuth theo hướng dẫn plugin.
4. Nhập App ID và App Secret vào trang Facebook trong Nextend của đúng môi
   trường, lưu, Verify Settings và Enable sau khi xác minh thành công.
5. Thử với tài khoản có vai trò phù hợp trong lúc phát triển. Muốn phục vụ
   công chúng, hoàn tất yêu cầu Live/Review/Verification mà Meta áp dụng cho app.

Facebook cần website HTTPS để dùng đăng nhập. Không dùng callback HTTP của
local để cấu hình prod. Xem [hướng dẫn Facebook của Nextend](https://social-login.nextendweb.com/documentation/providers/facebook/).

## Callback trong dự án

Nếu dùng wp-login mặc định, callback local hiện được plugin tạo là:

```text
http://localhost:180/longkhanhsite/wp-login.php?loginSocial=google
http://localhost:180/longkhanhsite/wp-login.php?loginSocial=facebook
```

Trên prod, dạng mặc định dự kiến là:

```text
https://thodiatienhai.com/wp-login.php?loginSocial=google
https://thodiatienhai.com/wp-login.php?loginSocial=facebook
```

Đây không thay thế callback thực tế: nếu đổi login URL/proxy page thì lấy lại
URI ở trang Getting Started của Nextend. Nên tách client/app cấu hình cho môi
trường thử và prod. Không dùng app thử hoặc thông tin tài khoản thử làm cấu
hình công khai. Chưa có app/key thì chưa thể kiểm thử OAuth đầu cuối.

## Kiểm tra sau khi cấu hình

- Google và Facebook: tài khoản mới, tài khoản đã liên kết, hủy ở màn hình cấp quyền.
- PC và điện thoại: bấm nút từ popup login, hoàn tất rồi quay về đúng domain.
- Đăng nhập mật khẩu vẫn hoạt động; không sinh tài khoản thử trên prod chỉ để QA.
- Kiểm tra cấu hình đăng ký/liên kết tài khoản trong Nextend trước khi mở công khai;
  code giao diện không ép cấp quyền, bật đăng ký hoặc tự gộp tài khoản qua email.

Luồng triển khai vẫn là local → master → prod theo deployment-spec.md. Chưa
deploy hoặc chưa xác minh qua nhà cung cấp thì không coi là đã đăng nhập thật.
