# 🏭 BiscuitWMS - Hệ Thống Điều Hành Quản Lý Kho Bánh Quy (Nhóm Vượt Qua)

Dự án phần mềm Quản lý Kho Nhà máy Bánh quy (WMS) xây dựng theo kiến trúc MVC chuẩn, phân quyền theo 6 vai trò nghiêm ngặt theo đặc tả Báo cáo Giữa kỳ.

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

## 🔑 Danh Sách Tài Khoản Đăng Nhập Ca Trực (6 Vai Trò)

| Vai trò | Tên đăng nhập | Mật khẩu | Chức năng mặc định |
| :--- | :--- | :--- | :--- |
| **Quản lý kho** | `quanly` | `123456` | Điều phối xuất/nhập, quản lý kho, duyệt kế hoạch |
| **Nhân viên kho** | `nhanvienkho` | `123456` | Lập phiếu nhập/xuất, sơ đồ vị trí kệ |
| **Bộ phận QC** | `qc` | `123456` | Kiểm định chất lượng nguyên liệu & thành phẩm |
| **Nhóm kiểm kê** | `kiemke` | `123456` | Lập biên bản kiểm kê, đối soát tồn kho |
| **Ban Giám Đốc** | `giamdoc` | `123456` | Phê duyệt xuất bổ sung, tiêu hủy, xem báo cáo |
| **Quản trị viên** | `admin` | `123456` | Quản lý tài khoản, vai trò và phân quyền |

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
- Mỗi thành viên tự mở đúng file `controller/c<ChucNang>.php` và `model/m<ChucNang>.php` được phân công để phát triển các phương thức xử lý nghiệp vụ và câu lệnh SQL.
- File kết nối CSDL chung: `model/ketnoi.php`.
- Phân hệ mẫu hoàn chỉnh kết nối CSDL thật: **Lập phiếu nhập thành phẩm** (`vPhieuNhapTP.php`, `cPhieuNhapTP.php`, `mPhieuNhapTP.php`).
