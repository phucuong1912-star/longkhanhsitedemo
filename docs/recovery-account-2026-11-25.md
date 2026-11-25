# Khôi phục riêng tài khoản sau khi restore file ngày 20/11

## Phạm vi

Prod `https://thodiatienhai.com` được chủ site xác nhận chỉ restore file,
không restore database. Ngày 25/11, HTTP công khai trả 200 nhưng các URL
`/?lk_account=login` và `/?lk_account=register` vẫn render trang chủ cũ.
`applied.json` ghi commit `7fcd371f`; marker này không chứng minh checksum
của mọi file sau restore, nên không dùng nó để bỏ qua kiểm tra file đích.

Git giữ hai commit ngày 21/11: `4777131e` sửa tài khoản và liên kết cộng
đồng; `faab7279` cập nhật core và kiểm tra deploy. Lần khôi phục này chỉ lấy
7 file tài khoản từ `4777131e`, nằm trong `.github/account-recovery/`.
Riêng resolver/cache matcher trong account.php được thu hẹp còn `profile`;
không lấy sửa liên kết new-review/explore hoặc community.php, không lấy core.

Gói khôi phục đăng nhập, đăng ký, thông báo lỗi, chuyển hướng sau đăng nhập,
đường dẫn tài khoản không phụ thuộc trang profile đã xóa và phần quên/đặt
lại mật khẩu đi cùng các biểu mẫu trong commit gốc. Không thay nhà cung cấp
Google/Facebook, SMTP, options, user, bài viết, địa điểm, review, uploads
hoặc Elementor trong quá trình chuyển file. Không import database.

## Workflow giới hạn

Commit có tiền tố chính xác `recover-account-only:` sẽ bỏ job deploy toàn
bộ ở hai workflow cũ và chạy `recover-account.yml` theo nhánh master/prod.
Script của workflow cũ cũng chặn manual full deploy của commit khôi phục
và chặn commit thay gói khôi phục nhưng thiếu tiền tố. Commit phục hồi chỉ
được chứa gói/script/test/tài liệu/workflow được khai báo; lẫn sửa khác thì
workflow khôi phục dừng trước kết nối hosting.

Script từ xa chỉ chạy bên trong Actions. Giữ nguyên credential hiện có.
Đích chỉ được thay đúng 7 đường dẫn cứng trong updater. Mọi file đích phải
khớp checksum bản trước 20/11, bản khôi phục, hoặc đúng bản đầy đủ ngày
21/11 đã biết. Nếu đang có bản đầy đủ 21/11 (ví dụ trên master), giữ nguyên
file đó, không làm mất sửa liên kết cộng đồng đã có. Khác cả ba thì dừng,
không force. Chấp nhận khác biệt LF/CRLF khi nhận diện phiên bản nhưng
kiểm tra raw checksum lần nữa để phát hiện chỉnh sửa trong lúc chuẩn bị.

Master tải backup các file hiện có trước khi upload; Actions giữ artifact
30 ngày, kể cả khi lỗi. Prod giữ backup toàn code và database ngoài webroot
trước khi thay file; database chỉ được **xuất backup**, không nhập hoặc sửa
bởi updater. Updater PHP độc lập không bootstrap WordPress. Staging và
checksum được kiểm tra trước khi kích hoạt; lỗi publish thì trả các file
vừa ghi về byte ban đầu nếu không có chỉnh sửa đồng thời.

Không chuyển core, manifest/assets Elementor hoặc ready/applied marker;
không gọi importer hay regenerate CSS. Kiểm tra version.php không đổi trong
lượt khôi phục. Cơ chế kiểm tra core khớp nguồn/đích của deploy toàn bộ giữ
nguyên; lần khôi phục giới hạn này không có file core trong gói chuyển.

Prod yêu cầu origin/master là tổ tiên của HEAD và có run khôi phục master
thành công chứa **cùng manifest** trong lịch sử đã merge. Cả hai môi trường
kiểm tra trang chủ, đăng nhập, đăng ký và quên mật khẩu qua GET sau chuyển
file. Master dùng Chromium để đi qua challenge của host như workflow cũ.
Challenge master thêm đúng một `i=1` vào query sau khi thực thi JavaScript;
bộ kiểm tra chấp nhận riêng dấu này, giữ nguyên origin/path và toàn bộ tham
số chức năng. Thay `lk_account`, mất tham số, thêm query khác hoặc chuyển
domain/trang vẫn bị từ chối. Prod không bật ngoại lệ challenge này.
Form POST và tài khoản thật không được thử trên prod. Nếu HTTP kiểm tra
thất bại sau khi chuyển file, workflow thất bại và giữ backup; không restore
database hay tự ghi đè thay đổi phát sinh sau đó.

## Kiểm tra local

- Gói tài khoản: 5 PHP lint; patch apply/reverse đúng 7 đường dẫn; checksum.
- Plugin tạm với gói đã tách và community.php trước 20/11: 34 kiểm tra PASS;
  email thật bị chặn và mọi tài khoản thử rollback. So fingerprint các hàng
  posts/postmeta/comments/commentmeta/users/usermeta/terms/term_taxonomy/
  term_relationships/options trước/sau không đổi; gói Elementor không đổi.
  Chạy lại bằng `python scripts/test-account-recovery-wordpress.py` tại local.
- `python scripts/test-account-recovery.py`: filesystem và FTP giả kiểm tra
  phạm vi, backup, rollback, xung đột, thay đổi đồng thời, checksum, lặp lại,
  bảo toàn bản mới đã có và gate master → prod; không kết nối server.
- `python scripts/test-account-recovery.py --browser RecoveryBrowserTests`:
  Chromium chỉ truy cập fixture localhost, thực thi JS/cookie và thêm `i=1`
  giống host; xác nhận đủ ba mode tài khoản, giữ session và từ chối chuyển
  hướng làm mất mode, trang thiếu biểu mẫu hoặc HTTP 500. Workflow master chạy bộ này
  sau bước cài browser, trước khi kết nối FTP.
- `php scripts/elementor/export.php --check`, validate, engine tests.

Test symlink chỉ chạy trên Linux trong Actions; Windows có thể skip do yêu
cầu đặc quyền tạo symlink. Các kiểm tra local không thay cho kết quả Actions
và HTTP trên môi trường đích. Hiện chưa triển khai nếu chưa có run xác minh.

## Cách chủ site push

Không sửa hoặc đổi tiền tố commit khôi phục. Từ workspace, đưa nguyên commit
ở `codex/recover-account-only` vào master bằng fast-forward:

```powershell
git switch master
git merge --ff-only codex/recover-account-only
git push origin master
```

Chờ workflow **Recover account files only** trên master thành công, kiểm
tra giao diện master; sau đó mới thực hiện:

```powershell
git switch prod
git merge --ff-only master
git push origin prod
```

Nếu fast-forward không được, dừng để đối chiếu lịch sử; không reset/force
push. Không đưa đồng thời lệnh push master và prod vào một chuỗi chạy.
Không chạy lại workflow deploy toàn bộ để thay cho khôi phục giới hạn.
Mọi sửa tiếp theo vẫn quay lại local/master theo deployment-spec.md.
