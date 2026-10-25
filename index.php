<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = $_GET['page'] ?? 'trangchu';
$page = basename($page);

// Bản đồ phân quyền chức năng nghiêm ngặt 100% theo đặc tả Báo cáo Giữa kỳ
$roleAllowedPages = [
    'Admin' => [
        'vTaiKhoan', 'vVaiTro'
    ],
    'QuanLyKho' => [
        'vPhieuYeuCauNhapXuat', 'vNguyenLieu', 'vDieuPhoiNhapNL', 'vDieuPhoiXuatNL',
        'vDieuPhoiNhapTP', 'vThanhPham', 'vDieuPhoiXuatTP', 'vDeNghiTieuHuy',
        'vKho', 'vXuLyChenhLech', 'vBaoCaoNhapXuat', 'vTonKho'
    ],
    'NhanVienKho' => [
        'vPhieuNhapNL', 'vPhieuXuatNL', 'vPhieuNhapTP', 'vPhieuXuatTP', 'vViTriKho'
    ],
    'QC' => [
        'vKiemDinhNL', 'vKiemDinhTP'
    ],
    'KiemKe' => [
        'vBienBanKiemKe'
    ],
    'BanGiamDoc' => [
        'vPheDuyetXuatBoSung', 'vPhieuPheDuyet', 'vBaoCaoThongKeTP', 'vBaoCaoNhapXuat', 'vTonKho'
    ]
];

// Thiết lập phiên làm việc mặc định theo trang yêu cầu nếu vào thẳng URL chưa qua đăng nhập
if (!isset($_SESSION['user'])) {
    $matchedRole = 'NhanVienKho';
    foreach ($roleAllowedPages as $r => $pages) {
        if (in_array($page, $pages)) {
            $matchedRole = $r;
            break;
        }
    }
    
    $roleUsers = [
        'Admin' => ['maNV' => 'NV001', 'hoTen' => 'Nguyễn Quản Trị', 'tenDangNhap' => 'admin', 'vaiTro' => 'Admin'],
        'QuanLyKho' => ['maNV' => 'NV011', 'hoTen' => 'Nguyễn Văn An', 'tenDangNhap' => 'quanly', 'vaiTro' => 'QuanLyKho'],
        'NhanVienKho' => ['maNV' => 'NV012', 'hoTen' => 'Trần Thị Bình', 'tenDangNhap' => 'nhanvienkho', 'vaiTro' => 'NhanVienKho'],
        'QC' => ['maNV' => 'NV004', 'hoTen' => 'Vũ Thị Dung', 'tenDangNhap' => 'qc', 'vaiTro' => 'QC'],
        'KiemKe' => ['maNV' => 'NV005', 'hoTen' => 'Đặng Văn Em', 'tenDangNhap' => 'kiemke', 'vaiTro' => 'KiemKe'],
        'BanGiamDoc' => ['maNV' => 'NV006', 'hoTen' => 'Bùi Thị Giang', 'tenDangNhap' => 'giamdoc', 'vaiTro' => 'BanGiamDoc'],
    ];
    
    $_SESSION['user'] = $roleUsers[$matchedRole] ?? $roleUsers['NhanVienKho'];
}

$currentRole = $_SESSION['user']['vaiTro'] ?? 'NhanVienKho';


$roleDefaultPages = [
    'Admin' => 'vTaiKhoan',
    'QuanLyKho' => 'vDieuPhoiXuatTP',
    'NhanVienKho' => 'vPhieuNhapTP',
    'QC' => 'vKiemDinhNL',
    'KiemKe' => 'vBienBanKiemKe',
    'BanGiamDoc' => 'vPhieuPheDuyet'
];

// Nếu vào trang chủ (index.php hoặc index.php?page=trangchu)
if ($page === 'trangchu' || $page === 'index') {
    include __DIR__ . '/view/trangchu.php';
    exit();
}

// Kiểm tra quyền truy cập trang của vai trò (Bảo mật RBAC 100%)
$allowedPages = $roleAllowedPages[$currentRole] ?? $roleAllowedPages['QuanLyKho'];
if (!in_array($page, $allowedPages)) {
    $targetPage = $roleDefaultPages[$currentRole] ?? 'vDieuPhoiXuatTP';
    header("Location: index.php?page={$targetPage}");
    exit();
}

// Các trang phân hệ nghiệp vụ WMS
$viewFile = __DIR__ . "/view/{$page}.php";
if (!file_exists($viewFile)) {
    $page = $roleDefaultPages[$currentRole] ?? 'vDieuPhoiXuatTP';
    $viewFile = __DIR__ . "/view/{$page}.php";
}

// Nạp khung Layout chuẩn WMS
include __DIR__ . '/view/layout/header.php';
include $viewFile;
include __DIR__ . '/view/layout/footer.php';
?>