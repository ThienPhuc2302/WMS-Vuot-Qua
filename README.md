# 🏭 BiscuitWMS - Hệ Thống Điều Hành Quản Lý Kho Bánh Quy (Nhóm Vượt Qua)

Dự án phần mềm Quản lý Kho Trong Nhà máy Sản Xuất Bánh quy (WMS) xây dựng theo kiến trúc MVC chuẩn, phân quyền theo 6 vai trò nghiêm ngặt theo đặc tả Báo cáo Giữa kỳ.

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Dự Án

### Bước 1: Sao chép mã nguồn vào XAMPP
- Copy toàn bộ thư mục `QuanLyKho_VuotQua` vào thư mục `htdocs` của XAMPP:
  - Windows: `C:\xampp\htdocs\QuanLyKho_VuotQua` (hoặc `D:\xamp\htdocs\QuanLyKho_VuotQua`).

### Bước 2: Khởi động dịch vụ
- Mở **XAMPP Control Panel**, nhấn **Start** cho cả 2 dịch vụ:
  - **Apache**
  - **MySQL**

### Bước 3: Import Cơ Sở Dữ Liệu
1. Mở trình duyệt truy cập: `http://localhost/phpmyadmin/`
2. Chọn tab **Import** (Nhập).
3. Chọn file CSDL có sẵn trong dự án: `QuanLyKho_VuotQua/database/quanlykho.sql`.
4. Nhấn **Import** ở cuối trang để tạo tự động cơ sở dữ liệu `quanlykho_vuotqua` cùng toàn bộ bảng và dữ liệu mẫu.

### Bước 4: Truy cập ứng dụng
- Mở trình duyệt và truy cập: **`http://localhost/QuanLyKho_VuotQua/`**

---

## 🔑 Danh Sách Tài Khoản Đăng Nhập Thực Tế Từ CSDL (quanlykho.sql)

### 1. Tài khoản cá nhân 6 thành viên nhóm Vượt Qua:
| Thành viên | Tên đăng nhập | Mật khẩu | Vai trò CSDL | Mã VT | Chức năng phụ trách |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Nguyễn Hữu Lộc** | `loc_kho` (hoặc `loc@iuh.edu.vn`) | `123456` | `NhanVienKho` | `VT03` | Lập phiếu nhập thành phẩm (UC 3.4) |
| **Trần Văn Sang** | `sang_kho` (hoặc `sang@iuh.edu.vn`) | `123456` | `NhanVienKho` | `VT03` | Lập phiếu nhập/xuất nguyên liệu |
| **Lê Minh Thiện Phúc** | `phuc_qc` (hoặc `phuc@iuh.edu.vn`) | `123456` | `QC` | `VT06` | Kiểm định chất lượng (QC) NVL & TP |
| **Võ Thị Tuyết Ngân** | `ngan_kiemke` (hoặc `ngan@iuh.edu.vn`) | `123456` | `KiemKe` | `VT05` | Lập biên bản kiểm kê định kỳ |
| **Trần Bích Thuận** | `thuan_qlk` (hoặc `thuan@iuh.edu.vn`) | `123456` | `QuanLyKho` | `VT02` | Quản lý danh mục kho, kệ, báo cáo |
| **Trần Thanh Toàn** | `toan_qlk` (hoặc `toan@iuh.edu.vn`) | `123456` | `QuanLyKho` | `VT02` | Lập lệnh điều phối xuất kho theo FEFO |

### 2. Danh sách 6 tài khoản đăng nhập chuẩn:
| STT | Vai trò (Role) | Tên đăng nhập | Mật khẩu | Họ và tên | Trang hạ cánh khi đăng nhập |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | **Quản lý kho** | `quanly` | `123456` | Nguyễn Văn An | Điều phối xuất thành phẩm |
| 2 | **Nhân viên kho (Thủ kho)** | `nhanvienkho` | `123456` | Trần Thị Bình | Lập phiếu nhập thành phẩm |
| 3 | **Bộ phận QC (Kiểm định)** | `qc` | `123456` | Lê Hoàng Cường | Kiểm định nguyên liệu |
| 4 | **Nhóm kiểm kê** | `kiemke` | `123456` | Phạm Minh Dũng | Lập biên bản kiểm kê |
| 5 | **Ban giám đốc** | `giamdoc` | `123456` | Hoàng Văn Giang | Phê duyệt kiểm kê & tiêu hủy |
| 6 | **Quản trị viên (Admin)** | `admin` | `123456` | Đỗ Thành Đạt | Quản lý tài khoản |

### 3. Bảng phân quyền danh mục chức năng hiển thị trên menu:
| Tài khoản | Vai trò hiển thị | Số chức năng | Danh sách chức năng hiển thị trên menu |
| :--- | :--- | :---: | :--- |
| `admin` | **Quản trị viên** | 2 | 1. Quản lý tài khoản<br>2. Quản lý vai trò & quyền |
| `quanly` | **Quản lý kho** | 12 | 1. Xem yêu cầu nhập/xuất<br>2. Quản lý nguyên liệu<br>3. Điều phối nhập nguyên liệu<br>4. Điều phối xuất NVL FEFO<br>5. Điều phối nhập thành phẩm<br>6. Quản lý thành phẩm<br>7. Điều phối xuất thành phẩm<br>8. Xử lý hàng hư hỏng/tiêu hủy<br>9. Quản lý danh sách kho<br>10. Xử lý chênh lệch sau kiểm kê<br>11. Báo cáo thống kê nhập/xuất<br>12. Xem báo cáo tồn kho |
| `nhanvienkho` | **Nhân viên kho** | 5 | 1. Lập phiếu nhập nguyên liệu<br>2. Lập phiếu xuất nguyên liệu<br>3. Lập phiếu nhập thành phẩm<br>4. Lập phiếu xuất thành phẩm<br>5. Quản lý vị trí hàng hóa |
| `qc` | **Bộ phận QC** | 2 | 1. Kiểm định nguyên liệu<br>2. Kiểm định thành phẩm |
| `kiemke` | **Nhóm kiểm kê** | 1 | 1. Lập biên bản kiểm kê |
| `giamdoc` | **Ban giám đốc** | 5 | 1. Báo cáo thống kê nhập/xuất<br>2. Xem báo cáo tồn kho<br>3. Phê duyệt xuất bổ sung<br>4. Phê duyệt kiểm kê & tiêu hủy<br>5. Báo cáo thống kê thành phẩm |


---

## 📁 Cấu Trúc Dự Án & Phân Công Cho Thành Viên

```
QuanLyKho_VuotQua/
├── assets/                  # CSS, Javascript, hình ảnh
├── controller/              # Chứa 27 file Controller khung điều hướng
├── model/                   # Chứa 27 file Model khung kết nối CSDL
├── database/
│   └── quanlykho.sql        # File kịch bản CSDL đầy đủ 17 bảng cốt lõi
├── view/                    # Giao diện hiển thị chức năng
│   ├── layout/              # Header, Footer, Sidebar phân quyền động
│   ├── trangchu.php         # Trang chủ điều hành kho
│   └── v*.php               # Các giao diện nghiệp vụ WMS
├── index.php                # Bộ điều hướng trung tâm (Router & RBAC)
├── login.php                # Xử lý xác thực đăng nhập
└── logout.php               # Xử lý đăng xuất an toàn
```

### 📌 Lưu ý cho thành viên nhóm:
- Cơ chế Autoloader: Đã đăng ký `spl_autoload_register` tại `index.php`. Tất cả các Class trong `model/` và `controller/` được tự động nạp khi khởi tạo (`new m...`, `new c...`), tuyệt đối KHÔNG cần gõ `require` hay `include` thủ công.
- Lớp kết nối CSDL chuẩn Singleton: `model/Database.php` (gọi `$this->conn = Database::getInstance()->getConnection();`).
- Điều hướng tự động (Dynamic Routing): `index.php?page=v[TênChucNang]` sẽ tự động gọi Controller `c[TênChucNang]`, không cần cấu hình thêm vào `index.php`.
- Phân hệ chuẩn mực kết nối 100% CSDL MySQL thật: **Lập phiếu nhập thành phẩm** (`vPhieuNhapTP.php`, `cPhieuNhapTP.php`, `mPhieuNhapTP.php`).

---

## 🚫 QUY TẮC CỐT LÕI (BẮT BUỘC TOÀN BỘ THÀNH VIÊN TUÂN THỦ)

1. **100% DỮ LIỆU THỰC TẾ TỪ MYSQL:**
   - Mọi chức năng, truy vấn, xác thực và hiển thị phải kết nối trực tiếp với CSDL MySQL `quanlykho_vuotqua`.
   - **TUYỆT ĐỐI CẤM** tự ý bịa mock data, fake data hay hardcode mảng dữ liệu mẫu trong JavaScript hoặc PHP.

2. **TUYỆT ĐỐI CẤM CƠ CHẾ DỰ PHÒNG NGẦM (NO FALLBACK TRICKS):**
   - Không viết code kiểu "thử kết nối db này không được thì nhảy sang db khác".
   - Không tự ý tạo cơ chế tự động đăng nhập tài khoản giả khi đăng nhập thất bại.
   - Không âm thầm nuốt lỗi (silent error).

3. **LỖI THÌ PHẢI FIX - KHÔNG CHE GIẤU LỖI:**
   - Nếu kết nối CSDL thất bại, bảng chưa tồn tại hoặc câu truy vấn SQL sai cú pháp, hệ thống phải báo lỗi rõ ràng, minh bạch để lập trình viên biết chính xác nguyên nhân và fix bug tận gốc.

4. **TÊN CƠ SỞ DỮ LIỆU DUY NHẤT:**
   - Tên CSDL chính thức duy nhất là: `quanlykho_vuotqua` (được khởi tạo từ file `database/quanlykho.sql`). Không sử dụng bất kỳ tên CSDL nào khác.

