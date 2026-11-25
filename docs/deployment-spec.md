# Quy trình local → master → prod

Quy tắc này do chủ dự án yêu cầu ngày 15/11/2026. Đây là quy trình triển khai
thường ngày, thay thế các cách upload/SSH thủ công đã dùng trước đây.

## Nơi chỉnh sửa và nơi triển khai

Chỉ chỉnh code và giao diện Elementor ở WordPress local trong workspace.
Không chỉnh trực tiếp trên hosting master hoặc prod. Agent mặc định chuẩn bị
commit local để người dùng tự push; không tự push, SSH/FTP, chạy script deploy,
chạy workflow bằng tay, sửa WordPress từ xa hoặc đổi secrets.

Luồng duy nhất:

1. Sửa local, kiểm tra, commit toàn bộ code và gói Elementor liên quan.
2. Người dùng push `master`. Job trong `.github/workflows/deploy.yml` tự deploy
   tới hosting master bằng cấu hình FTP hiện có.
3. Chờ workflow master xanh và kiểm tra giao diện/chức năng trên master.
4. Merge **master vào prod**, rồi push `prod`. Job trong
   `.github/workflows/deploy-prod.yml` tự deploy tới VPS prod bằng cấu hình
   SSH hiện có. Không đưa bản sửa lên prod trước master.
5. Nếu cần sửa tiếp, quay lại local/master và lặp lại đúng thứ tự.

Cả hai workflow chỉ chạy job deploy khi nhánh tương ứng khớp tuyệt đối.
Việc có commit local không có nghĩa là website đã được deploy thành công.

### Khôi phục tài khoản giới hạn sau restore file 20/11

Theo yêu cầu ngày 25/11/2026, lần khôi phục này chỉ chuyển 7 file tài khoản
đã tách từ commit `4777131e`, không triển khai lại core hoặc layout. Commit
có tiền tố `recover-account-only:` dùng `.github/workflows/recover-account.yml`;
hai workflow deploy toàn bộ bỏ qua push đó và chặn full deploy thủ công của
commit khôi phục. Script chỉ chạy trong Actions, kiểm tra checksum/baseline,
backup trước khi thay file và giữ bản đầy đủ 21/11 nếu đã tồn tại.

Luồng vẫn là master thành công rồi merge master vào prod. Job prod yêu cầu
master hiện tại đã merge và có run master thành công của cùng gói. Không
chuyển database/Elementor marker hoặc gọi importer trong bước khôi phục này;
guard core của deploy toàn bộ không đổi. Chi tiết phạm vi, kiểm thử và cách
push: [Khôi phục tài khoản 25/11](recovery-account-2026-11-25.md).

| Nhánh | Workflow | Đích đã được cấu hình trong dự án |
| --- | --- | --- |
| `master` | `deploy.yml` | FTP secrets hiện có, thư mục `/htdocs/`, URL `https://longkhanhsites.site.je` |
| `prod` | `deploy-prod.yml` | VPS qua `PROD_SSH_*`, `PROD_WEB_ROOT`, URL `https://thodiatienhai.com` |

URL đích của cơ chế đồng bộ được ghi trong
`wp-content/mu-plugins/lk-layout-sync/scopes.json`, dựa trên cấu hình/tài liệu
host có sẵn. Nếu master đã đổi domain, sửa `master_origin` ở local cho đúng
domain thực tế trước khi commit/push. Với prod, `production_origin` phải trùng
`PROD_SITE_URL`. Importer từ chối chạy nếu URL WordPress không khớp; không tự
đổi URL, database, key hoặc secrets để vượt qua kiểm tra này.

## Elementor được đưa vào Git như thế nào

Elementor lưu bố cục trong database; chỉ push file PHP/CSS không mang các thay
đổi đó lên server. Dự án dùng gói có phiên bản tại
`wp-content/lk-layouts/manifest.json` và `wp-content/lk-layouts/assets/`.

Exporter chỉ đọc các tài liệu trong allowlist `scopes.json`: header, footer,
menu mobile, popup đăng nhập, template địa điểm/album, active kit và 10 trang
chức năng hiện có. Nó không xuất các bài blog, địa điểm hay toàn bộ database.

Ngày 16/11/2026, chủ dự án xác nhận đã chủ động xóa trang Liên hệ trên prod
và không dùng nữa. Khóa `contact` được rút khỏi `scopes.json` và gói xuất ở
local theo quyết định này. Deploy không khôi phục/tạo lại trang, sửa ánh xạ
`lk_page_map`, xóa metadata cũ hay thay đổi các layout còn lại vì việc rút
scope. Trang Liên hệ còn lưu ở local/master (nếu có) được giữ nguyên nhưng
không còn đồng bộ. Các trang khác vẫn phải vượt qua đầy đủ kiểm tra ánh xạ
và xung đột; không tự bỏ scope khi gặp trang thiếu trong các lần deploy sau.

Cùng ngày, chủ dự án xác nhận bỏ Tải app và yêu cầu đọc danh sách trang trong
thùng rác prod để xác định các trang khác đã xóa. Đối chiếu đủ 15 trang trong
thùng rác với 12 trang đang xuất bản cho thấy Tải ứng dụng (`download`) và
Tài khoản (`profile`) đã bị xóa, không có trang cùng tên trong danh sách
page đang xuất bản. Hai khóa này cũng được rút khỏi scope và gói local.
Chính sách bảo mật và Thêm địa điểm trong thùng rác là các bản cũ; bản đang
xuất bản vẫn tồn tại nên giữ nguyên scope `privacy` và `add-place`. Các trang
demo/thanh toán/ưu đãi cũ khác trong thùng rác vốn không thuộc scope.
Việc rút scope không đổi các liên kết/menu hoặc luồng tài khoản trên prod.

Đổi slug trang không có nghĩa là bỏ trang khỏi scope. Ví dụ khóa logic
`add-place` vẫn ánh xạ tới trang Thêm địa điểm dù permalink trên prod là
`/them-dia-diem/`. Chuyển hướng sau gửi form và nút đóng góp trong tài khoản
lấy permalink từ `lk_page_map` của môi trường đích, không ép slug local lên
prod và không cập nhật option/slug khi deploy. Kiểm thử luồng này ở local:
`php scripts/test-form-redirects.php` (các lần ghi thử được rollback).

Thẻ khu vực (`lk-region-card`) lấy bộ lọc từ liên kết tìm kiếm ở tiêu đề để
đếm bằng cùng hàm lọc với trang kết quả, kể cả khi giá trị `lk_region` cũ chưa
được đổi. Việc ghép nguồn số đếm chỉ diễn ra trên bản dữ liệu dùng để render;
không ghi lại `_elementor_data`, slug, tên, ảnh hoặc liên kết đã sửa trên đích.
Trang có thẻ khu vực bỏ cache HTML document của Elementor trong lúc render
để không tái dùng cấu hình đếm cũ. Các document khác giữ chính sách cache cũ.
Link phủ thẻ được thêm ở frontend, theo đúng link/target/rel của tiêu đề;
giữ link tiêu đề cho bàn phím và không phủ vùng chọn trong trình sửa Elementor.
Kiểm thử local: `php scripts/test-region-cards.php`; thêm `--fixture` để xuất
bộ 8 thẻ thử vào `.codex-tools/region-cards-fixture.json` cho kiểm tra browser.

Feedback `new-day.rtf` (17/11/2026) được xử lý bằng code, không sửa gói layout:
tìm kiếm và gợi ý đọc taxonomy `list-tags`; icon tiện ích ưu tiên trường
`lp_features_icon` mà quản trị ListingPro đang sử dụng. Chế độ danh mục chọn
riêng khôi phục tab Mới ở đầu, lấy địa điểm công khai mới nhất theo ngày đăng
(ID để phân định khi cùng ngày), theo giới hạn số lượng của widget; các nhóm
danh mục và thứ tự chọn riêng giữ nguyên.

Frontend giữ nguyên href của Elementor khi đã đăng nhập, không ép về
`listing-author/`. Nút đăng nhập trong menu mobile đóng drawer trước khi mở
popup và trả focus về nút mở menu sau khi đóng popup. Với button có class
`lk-menu-account`, mục Elementor “Liên kết tài khoản trong menu” cung cấp hai
URL “Chỉnh sửa hồ sơ” và “Danh sách đã lưu”. Nếu để trống, hồ sơ lấy link chính
của button làm gốc; danh sách đã lưu mở cửa sổ danh sách đã lưu. Các URL
tùy chỉnh và các widget menu khác giữ nguyên HTML/link của Elementor.
Không cần chạy migration hoặc ghi lại header/menu/homepage trên đích.
Kiểm thử local: `php scripts/test-new-day.php`; dữ liệu tag/icon thử rollback.

Khi chưa đăng nhập, địa điểm đã lưu dùng `localStorage` khóa `lk-saved` của
đúng origin/trình duyệt. Khi đã đăng nhập (kể cả bằng Google/Facebook), nút
Lưu/Bỏ lưu đọc và cập nhật usermeta `lp_saved_user_posts` của tài khoản hiện
tại, dùng chung với mục “Đã lưu” trong dashboard ListingPro. AJAX yêu cầu
phiên đăng nhập và nonce hợp lệ; thao tác không nhận user ID từ trình duyệt.
Chỉ thêm địa điểm công khai, giữ các mục đã có trong tài khoản, không chạy
migration hay ghi favorites trong deploy. Lỗi mạng/phiên hết hạn không báo
lưu thành công hoặc chuyển sang lưu trình duyệt một cách ngầm định.
Đường dẫn `/?lk_saved=1` trên trang chủ mở danh sách ở cả hai trạng thái;
có thể vào từ menu mobile hoặc “Xem danh sách” sau khi lưu. Nếu còn danh sách
cũ của trình duyệt, người đã đăng nhập có nút “Chuyển … địa điểm từ trình
duyệt vào tài khoản”; chỉ hợp nhất khi người dùng bấm, không tự nhập dữ liệu
của máy dùng chung. Chỉ bỏ các ID đã chuyển thành công khỏi danh sách cũ.
Link tới dashboard lấy option `listing-author` của môi trường đích, không ép
slug local hay thay đổi link Elementor đã tùy chỉnh. Danh sách chỉ hiện các
địa điểm công khai còn tồn tại và cho phép bỏ lưu.
Button chi tiết đổi nền trắng/viền xanh sang nền xanh/chữ trắng theo
`aria-pressed`; trạng thái và nhãn hỗ trợ truy cập cập nhật cùng dữ liệu lưu.
Các thay đổi này không yêu cầu xuất lại bố cục Elementor hay tạo page mới.
Kiểm thử local: `php scripts/test-saved-places.php` (fixture được rollback).

Nút Google/Facebook dùng API render chính thức của Nextend Social Login;
không còn handler giả chỉ hiện thông báo. Nhà cung cấp chưa cấu hình/xác minh/
bật thì không có link OAuth khả dụng. Client Secret/App Secret chỉ nằm trong
cấu hình Nextend của từng môi trường, không xuất vào Git hay gói Elementor.
Không bật provider, tạo key, đổi đăng ký/liên kết tài khoản hay sửa cấu hình
prod tự động. Hướng dẫn chủ site: `docs/social-login-setup.md`.

Đăng ký, đăng nhập và quên/đặt lại mật khẩu dùng endpoint frontend
`/?lk_account=login|register|lost|reset`, không phụ thuộc trang `profile` đã
bị xóa trên prod. Không tạo lại page hoặc thay `_elementor_data`. Đăng ký
giữ vai trò subscriber; các form POST yêu cầu nonce. Quên mật khẩu dùng API
email/key native WordPress, có giới hạn số lần yêu cầu, thông báo gửi mail
và lỗi gửi. Khóa email được kiểm tra rồi chuyển sang cookie HttpOnly/SameSite
trước khi render form mật khẩu mới; trang bỏ cache và không gửi referrer.
Đặt lại thành công vô hiệu hóa khóa như WordPress, không tự đăng nhập.
Code không cấu hình SMTP hoặc đảm bảo thư đã đến hộp thư trên server đích.

Các CTA cộng đồng lấy permalink hiện tại từ `lk_page_map`. Link Elementor
cũ cùng site `/new-review/`, `/explore/` (kể cả `lk_feed=following`) và
`/profile/` được sửa trên HTML render; giữ query/anchor, chữ, ảnh, style,
target/rel và mọi URL tùy chỉnh khác. Document có các link động này bỏ cache
Elementor lúc đọc. Không ghi lại bố cục, slug, mapping hay gói local lên prod.
Kiểm thử `php scripts/test-account-links.php` dùng tài khoản thử rollback và
chặn gửi thư thật. Kiểm tra browser local dùng các bản sao page riêng.

Ads dùng chiến dịch `lp-ads` và metadata native ListingPro của từng môi trường.
Code tự thêm Spotlight trước danh sách động đầu tiên ở trang chủ, Top of
Search trước kết quả thường và Side of Listing dưới nội dung của cột
`lk-map-column` trên chi tiết (mobile xếp theo cột thông tin hiện có). Nếu cột
đã bị bỏ khỏi template, phần chi tiết đặt cuối nội dung. Không hiện quảng cáo
trên trang album/menu hoặc địa điểm có mật khẩu. Các thẻ có nhãn “Được tài trợ”;
kết quả quảng cáo tuân theo bộ lọc tìm kiếm, không đổi số đếm/phân trang thường
hay các danh mục/địa điểm chọn thủ công trong Elementor. Spotlight trên mobile
vuốt ngang để tránh kéo dài trang chủ.

Widget mới “Địa điểm được tài trợ (Ads)” cho phép chọn vị trí và số lượng
1–6 khi muốn tự đặt trong Elementor; widget tường minh thay vị trí tự động
tương ứng. Mặc định không cần lưu lại bất kỳ document nào. Cột chi tiết chỉ
nhận một widget động trong bản sao dữ liệu lúc render; cache Elementor của
document chứa các vị trí động được tắt lúc đọc, không ghi option hoặc layout.
Gói Elementor/allowlist giữ nguyên, nên giữ các chỉnh sửa bố cục trên đích.

Từ bản hỗ trợ chỉnh sửa Ads độc lập trong Elementor, khi người có quyền mở
trang/template cũ trong editor, khối tự động được đưa vào mô hình editor dưới
dạng widget `lk-ads` riêng, trước danh sách trang chủ/kết quả hoặc trong cột
chi tiết. Editor và iframe preview dùng cùng ID widget ổn định. Chuyển đổi
chỉ đọc dữ liệu hiện tại của document trên chính môi trường đó, giữ nguyên
ID/thứ tự/nội dung/ảnh/liên kết/cấu hình của các node còn lại. Không ghi metadata
trong lúc mở editor, render frontend hay deploy; không chép homepage local
lên prod để thực hiện chuyển đổi này.

Widget có tiêu đề, bật/tắt tiêu đề, số lượng, vị trí và điều khiển kiểu chữ,
màu, số cột PC và khoảng cách. Khi chưa có chiến dịch hợp lệ, chỉ editor hiện
khung giữ chỗ để vẫn chọn/kéo thả/xóa được; khách không thấy khung này. Khi
người dùng lưu, Elementor lưu widget cùng page setting `lk_ads_layout_mode`
bằng `widgets`. Chế độ này tắt mọi fallback tự động của document, kể cả khi
đã xóa hết widget Ads, nên khối đã xóa không xuất hiện lại sau tải trang.
Document chưa lưu giữ cách hiển thị tự động cũ. Cài đặt trang → Quảng cáo
cho phép chọn lại chế độ; không sửa trạng thái/giá/ngân sách của chiến dịch.
Kiểm thử: `php scripts/test-ads-editor.php`; các lần lưu thử dùng bản sao và
rollback, gói Elementor gốc giữ nguyên.

Chiến dịch chỉ hiện khi Published, liên kết đúng địa điểm công khai, trạng
thái/vị trí còn active và công tắc vị trí trong Theme Options → Ads đang bật.
PPD cần số ngày còn lại > 0; ngày kết thúc native (`campain_expire_date`) nếu
có được kiểm tra thêm, kể cả cron chưa chạy. PPC cần giá lượt nhấp dương và
số dư đủ cho đúng vị trí. Không tự kích hoạt, tạo, sửa hay đồng bộ chiến dịch,
giá, gói thanh toán hoặc dữ liệu kinh doanh trong deploy.

Link thẻ quảng cáo qua endpoint frontend có chữ ký, hạn 24 giờ và mỗi vé ghi
nhận tối đa một lần trên mỗi tài khoản/trình duyệt. Cookie first-party có chữ
ký chỉ nhận diện trình duyệt khách để chống đếm lặp; không lưu IP. Vì vậy các
khách khác nhau vẫn được ghi nhận khi dùng cùng HTML cache. HEAD/prefetch,
chữ ký sai, replay trong cùng trình duyệt, chủ địa điểm và quản trị viên
không bị tính phí. Endpoint kiểm tra lại chiến dịch dưới khóa MySQL trước
khi gọi hàm PPC native `lp_count_clicks_for_campaigns`; thống kê/số dư giữ
trong `lp_listingpro_options`, dùng chung dashboard ListingPro. PPD chỉ tăng
`click_performed`, không trừ tiền/ngày. Link sau chuyển hướng dùng permalink
hiện tại của môi trường, không hardcode slug hay URL local. Nút Lưu, link
địa điểm thường và danh sách dựng lại bằng JavaScript không mang tham số
thu phí native. Vé không phải hệ thống chống gian lận hoàn chỉnh: lượt mở từ
các lần render khác có vé khác. Full-page cache ngoài Elementor cần giữ quy
tắc bỏ qua query theo cấu hình WordPress hiện có; link quá hạn vẫn dẫn tới
địa điểm nhưng không thu phí.
Kiểm thử local: `php scripts/test-ads.php` (mọi fixture và lượt trừ thử rollback).

Màn hình sửa Ads có hộp “Kiểm tra hiển thị trên giao diện”, trả lý do chặn
cho từng vị trí bằng cùng hàm kiểm tra frontend (công tắc, trạng thái, liên
kết campaign_id, số ngày, ngày hết hạn và số dư). Chỉ người có quyền sửa Ads
được xem; không có endpoint chẩn đoán công khai, không sửa dữ liệu khi đọc.
Ảnh chụp các checkbox và số ngày không đủ xác định hết các trạng thái này.

ListingPro giữ `campain_expire_date` cũ khi quản trị viên xuất bản/cập nhật
lại một Ads PPD. Nếu ngày đó đã qua, bản tích hợp vẫn chặn dù duration mới
là 10. Khi người có quyền chủ động lưu form Ads Published với nonce native
hợp lệ, Mode PPD và số ngày nguyên dương, code gia hạn riêng ngày cũ đã hết
theo số ngày vừa nhập, tính theo múi giờ WordPress. Không thay ngày đang còn
hạn, không gia hạn PPC/cron/request thiếu nonce, không sửa cờ hoặc liên kết
chiến dịch và không chạy migration/repair khi deploy hoặc đọc trang.
Kiểm thử các hook lưu quản trị thật: `php scripts/test-native-ads-save.php`;
mọi account, bài và metadata thử đều rollback, không tạo hóa đơn thử.

Các trường được đồng bộ giới hạn ở `_elementor_data`,
`_elementor_page_settings`, `_elementor_edit_mode`, `_wp_page_template` và
`_elementor_template_type`. Việc bỏ một trường trong local cũng được đồng bộ
như thao tác bỏ metadata đó, vẫn giới hạn đúng tài liệu trong allowlist.
Không chép `post_content`, title, slug, ngày đăng, trạng thái hoặc revisions
của tài liệu. Không đồng bộ menu WordPress, theme options hoặc trang mới nằm
ngoài allowlist một cách ngầm định.

ID page/template lấy từ các option ánh xạ **của từng môi trường** như
`lk_page_map`, `lk_template_header`, `elementor_active_kit`. Manifest chứa khóa
logic và token URL/media; không dùng ID local làm ID đích. Nếu ánh xạ thiếu,
sai loại bài hoặc trỏ vào tài liệu không được quản lý, deploy dừng thay vì
tự đoán, tạo lại trang hay ghi đè bài viết.

Ảnh được tham chiếu trong bố cục được đóng gói theo SHA-256. Importer thêm bản
riêng trong `uploads/lk-layouts/` và attachment riêng của môi trường đích.
Không ghi đè/xóa uploads hoặc attachment đã có. Gói dùng ảnh gốc; không mang
theo thumbnail/cache từ local và không tự tái tạo toàn bộ thư viện ảnh.

Manifest v2 chuyển từng ảnh bằng payload `.png.gz`, `.jpeg.gz`… trong
`assets/`; không chuyển ảnh PNG/JPEG trần làm nguồn import. Đã có trường hợp
ảnh sau deploy khác byte với bản Git dù cùng kích thước, khiến kiểm tra ảnh
chặn import. Payload gzip giữ nguyên ảnh gốc, không re-encode hay đổi chất
lượng. Mỗi ảnh là một payload riêng để tránh một archive lớn vượt giới hạn
file của hosting master.

Mỗi payload có SHA-256 riêng; importer kiểm tra cả checksum gzip lẫn số byte
và SHA-256 của ảnh sau giải nén, trước khi bắt đầu cập nhật layout. Ảnh gốc
được đọc qua stream gzip rồi ghi thành file ảnh bình thường trong thư mục
media quản lý riêng. Không chấp nhận ảnh sai hash, không đổi hash theo ảnh
đã bị biến đổi và không sửa ảnh/uploads đang có trên server. Các bản PNG/JPEG
trần của gói v1 được thay bằng gzip trong Git; thư viện uploads gốc giữ nguyên.

Muốn đồng bộ một page/template mới hoặc một dạng tham chiếu mới, phải bổ sung
scope/ánh xạ và kiểm thử ở local. Exporter từ chối tham chiếu template chưa
được khai báo; không mở rộng sang `post`, `listing`, orders hoặc users.

## Git hooks ở máy local

Đã cài hook cho checkout hiện tại bằng local Git config, không đổi remote.
Máy/checkout mới cần cài một lần, từ thư mục dự án:

```powershell
C:\xampp\php\php.exe scripts/elementor/install-hooks.php
```

- `pre-commit`: trên master/nhánh làm việc, tự xuất Elementor local và stage
  manifest cùng ảnh liên quan. Nếu local WordPress không hoạt động, commit
  dừng để tránh thiếu bố cục.
- `pre-merge-commit`: chạy cùng kiểm tra khi tạo merge commit.
- Trên `prod`, hook giữ nguyên gói đã merge từ master, không xuất lại từ DB
  local. Không commit bản sửa riêng trên prod.
- `pre-push`: kiểm tra gói đã nằm đầy đủ trong commit; trên master còn so
  sánh gói với Elementor local để chặn thay đổi chưa commit. Khi push prod,
  local master phải là tổ tiên của prod. Push master/prod phải từ đúng nhánh
  đang checkout, không đẩy một ref khác vào nhánh deploy.

Hook chỉ có tác dụng khi dùng Git CLI/client có thực thi hooks. Không bỏ qua
hook, dùng `--no-verify` hoặc đổi `core.hooksPath` để làm commit/push chạy qua.
Nếu client không chạy hook, phải chạy exporter và commit gói trước khi push.
Actions kiểm tra gói trong Git nhưng không thể đọc database trên máy local.

Lệnh kiểm tra/xuất thủ công **ở local**:

```powershell
C:\xampp\php\php.exe scripts/elementor/export.php
C:\xampp\php\php.exe scripts/elementor/export.php --check
C:\xampp\php\php.exe scripts/elementor/validate.php
C:\xampp\php\php.exe scripts/elementor/test.php
C:\xampp\php\php.exe scripts/elementor/test-wordpress.php
python scripts/elementor/test-deploy-check.py
```

Phải commit sau mỗi lần sửa Elementor. Push lại một commit cũ không chứa thay
đổi database và cũng không tạo push event mới cho GitHub Actions.

## Điều gì xảy ra trong Actions

Ngày 21/11/2026, source core 7.1.4 lệch với phiên bản 7.1.5 được prod công
khai qua HTML. Script kiểm tra core trước khi chuyển file; bản cũ dùng
`test` im lặng nên chỉ thấy exit 1 sau khi gói Elementor đã hợp lệ. Giữ nguyên
yêu cầu core hai đầu phải khớp: cập nhật core ở local/master bằng bản phát
hành WordPress chính thức, kiểm tra rồi mới đi theo luồng master → prod.
Không sửa riêng số version, hạ core trên prod, bỏ guard hoặc chạy cập nhật
trực tiếp trên server để vượt lỗi. Core local được đồng bộ lên bản bảo mật
7.1.5, giữ locale `vi`; DB schema version vẫn là 61833. Không đổi database,
uploads, plugin, theme hoặc gói bố cục trong thao tác cập nhật core này.

Preflight hiện in rõ version nguồn/đích khi lệch, và lý do thiếu marker,
công cụ hoặc staging bị trùng. Các lỗi khác nêu phase/dòng/exit code của
runner hoặc đoạn shell từ xa. Không bật `set -x`, không in BASH_COMMAND,
biến môi trường hay key để chẩn đoán. Kiểm thử offline
`python scripts/test-prod-core-guard.py` chỉ trích hàm kiểm tra version thuần
để chạy với dữ liệu giả; không chạy entrypoint deploy hay kết nối SSH.

1. Xác thực checksum, allowlist và các ảnh trong gói đã commit.
2. Chuyển code/gói tới đúng môi trường bằng job hiện có. Hai job loại trừ
   toàn bộ uploads, `wp-config.php`, `.htaccess`, SQL, backups và cache.
   Prod giữ bước backup database/code ngoài webroot và rsync không `--delete`.
   Master giữ `dangerous-clean-slate: false`; không dọn hosting.
3. **Sau khi chuyển xong**, workflow tạo/publish `ready.json` chứa release,
   commit SHA, nhánh môi trường và origin. Marker và `applied.json` không nằm
   trong Git/gói chuyển code chính. Master publish marker bằng lượt FTP riêng;
   prod ghi marker sau rsync. Manifest mới không được áp dụng khi marker cũ
   chưa khớp.
4. MU-plugin `lk-layout-sync.php` kiểm tra môi trường, ánh xạ và toàn bộ gói
   khi WordPress boot. Importer khóa đồng bộ, chuẩn bị ảnh và backup metadata,
   rồi cập nhật metadata/state trong transaction InnoDB. Nó khóa/đọc lại các
   hàng metadata trước khi ghi để phát hiện thay đổi trong lúc chuẩn bị.
5. Xóa cache/CSS Elementor cần tạo lại. Workflow gọi HTTP warmup không cần
   đăng nhập và kiểm tra `applied.json` phải khớp **release + commit + nhánh**.
   Job thất bại nếu importer báo lỗi hoặc không xác minh được đúng bản.
   Sau trạng thái `applied`, cả master/prod còn kiểm tra trang chủ phải là
   HTML với HTTP 200, không phải trang lỗi WordPress hoặc redirect sang trang
   khác. Metadata đã áp dụng chưa đủ để kết luận frontend hoạt động.

Các lệnh WP-CLI bảo trì prod bỏ qua `nextend-facebook-connect` và
`nextend-social-login-pro` trong đúng tiến trình CLI. Nextend có nhánh nâng
version gọi redirect rồi `exit` tại `plugins_loaded`, có thể làm lệnh cache
thoát trước khi thực hiện. Plugin vẫn giữ kích hoạt và cấu hình đăng nhập
cho các request web. Không tắt social login hoặc sửa option để né cảnh báo.

MU-plugin `long-khanh-compat.php` xử lý module CubeWP business-hours cũ còn
trên server khi giữ file qua rsync. Khi thiếu helper `get_user_fields_by_type`,
chỉ tag `cubewp-business-hours-tag` không đủ dependency bị bỏ khỏi registry
trước lúc Elementor đọc controls. Khi helper tồn tại, tag được giữ nguyên.
Không thêm helper giả, sửa/xóa vendor trên server hay xóa dữ liệu giờ mở cửa.
Kiểm thử compatibility chạy bằng `php scripts/test-compat.php` ở cả hai job.

Hosting master có lớp kiểm tra JavaScript/cookie: HTTP thuần có thể nhận
`200 text/html` chứa `/aes.js` thay cho JSON. Vì vậy workflow master cài
Playwright theo `requirements-browser.txt`, kiểm thử với fixture local, rồi
chạy `check-deploy.py --browser`. Chromium thực thi kiểm tra của host như
trình duyệt bình thường và giữ cùng phiên cookie từ warmup tới status. Đọc
JSON từ phản hồi mạng cuối cùng sau chuyển trang; không đọc trang HTML
challenge như JSON, không tự tính/chèn cookie và không tắt bảo vệ của host.

Prod dùng HTTP như hiện có. Cả hai chế độ vẫn bắt buộc khớp release, commit,
environment và `status=applied`; HTML/404, bản cũ hoặc importer lỗi đều làm
job thất bại. Log lỗi nêu endpoint, HTTP status và loại phản hồi. Không bỏ
bước xác minh chỉ để làm workflow xanh.

Trạng thái lỗi công khai bổ sung mã cố định và khóa asset/document được kiểm
tra định dạng để Actions hiển thị nguyên nhân (ví dụ `asset_integrity`).
Không đưa exception PHP/SQL thô, đường dẫn server, mật khẩu hoặc nội dung bài
viết vào file JSON/log Actions. Chi tiết nội bộ vẫn nằm ở Tools → Layout Sync.

Checksum JSON dùng `serialize_precision=-1` trong đúng lúc encode, sau đó
khôi phục setting PHP ban đầu. Không phụ thuộc precision mặc định của local,
runner hay hosting; cùng một số thập phân phải tạo cùng checksum. Không bỏ
checksum hoặc sửa tay release để vượt lỗi `Layout package checksum mismatch`.

Kiểm thử browser ở local dùng fixture HTTP trong bộ test, không gọi WordPress
hay môi trường xa:

```powershell
python -m pip install -r scripts/elementor/requirements-browser.txt
python -m playwright install --only-shell chromium
python -B scripts/elementor/test-browser-check.py
```

Không thêm credential mới. FTP/SSH chỉ được sử dụng **bên trong Actions**;
script kiểm tra HTTP cũng chỉ được chạy cho môi trường xa bởi workflow.
Không chạy script deploy ở máy agent để thay thế pipeline.

## Bảo toàn nội dung, backup và xung đột

- Không import database local lên master/prod, không SQL replace toàn DB,
  không reset dữ liệu, không restore database cũ trong một lần deploy thường.
- Không sửa/xóa bài viết cũ hay mới, địa điểm, reviews/comments, users, orders,
  taxonomy hoặc thư viện uploads hiện có. Bài mới vẫn đăng từ WordPress của
  đúng môi trường; chúng hiển thị qua cơ chế blog động đã có trong plugin.
- Lần đồng bộ đầu tiên áp dụng giao diện local cho đúng allowlist sau khi
  lưu backup metadata đích. Nội dung giao diện trong các mẫu này sẽ theo
  local; nội dung bài viết không được nhập từ local.
- Các lần sau so từng metadata với baseline của lần deploy trước. Trường
  local không đổi thì không ghi đè chỉnh sửa phát sinh trên đích. Nếu cùng
  một trường vừa đổi ở local vừa đổi trên đích, toàn bộ lượt cập nhật metadata
  dừng trước khi ghi. Không có chế độ force tự động.
- Lỗi trong transaction rollback metadata/state của lượt đó. Ảnh quản lý
  riêng đã thêm có thể còn lại để tái sử dụng; không xóa media để "dọn lỗi".
- Backup metadata lưu trong option không autoload `lk_layout_backup_*`, không
  đưa ra JSON công khai. Trang Tools → Layout Sync có thông tin lỗi/release
  và nút hoàn tác metadata lần gần nhất, có kiểm tra quyền/nonce.
- Hoàn tác là thao tác khôi phục có chủ đích của quản trị viên, không phải
  cách chỉnh giao diện trên server. Agent không tự chạy thao tác này. Chỉ
  hoàn tác được bản mới nhất khi metadata chưa bị sửa tiếp; bản đã hoàn tác
  bị chặn tự áp dụng lại. Muốn đưa lên lại, sửa/xuất một revision mới ở local.
  Rollback chỉ trả lại metadata đã thay đổi, không restore toàn DB hoặc xóa bài.

Nếu deploy dừng: đọc log Actions và Tools → Layout Sync, xác định scope,
origin, quyền ghi, InnoDB hoặc trường xung đột. Giải quyết nguyên nhân ở local
và chạy lại đúng luồng master → prod. Không bỏ backup/checksum, gỡ MU-plugin,
đổi mapping tùy tiện hoặc dùng script upload cũ để vượt lỗi.

## Kiểm tra trước bàn giao

Chạy export `--check`, validate và bộ kiểm thử engine. Kiểm tra không có
localhost trong manifest, không stage SQL/credentials/uploads cache; commit
gói cùng source. Khi bàn giao phải nói rõ kiểm tra nào đã chạy và chưa deploy
nếu người dùng chưa push. Các workflow mới chỉ được xác minh thực tế trên
server sau khi người dùng push theo thứ tự quy định.

`CHUYEN-HOST.txt` và `.codex-tools/prod_*` là tài liệu/công cụ migration, sửa
lỗi lịch sử. Chúng không cấp quyền deploy trực tiếp. Chuyển host mới hoặc
khôi phục toàn hệ thống là tác vụ khác, cần yêu cầu riêng; không áp dụng vào
deploy giao diện/code hằng ngày.
