<?php
$user = $_SESSION['user'] ?? null;
if (!$user) {
    return;
}

$rawRole = $user['maVaiTro'] ?? ($user['vaiTro'] ?? '');
$roleMap = [
    'VT01'         => 'Admin',        'Admin'         => 'Admin',        'Quản trị viên' => 'Admin',
    'VT02'         => 'QuanLyKho',    'QuanLyKho'     => 'QuanLyKho',    'Quản lý kho'   => 'QuanLyKho',
    'VT03'         => 'NhanVienKho',  'NhanVienKho'   => 'NhanVienKho',  'Thủ kho'       => 'NhanVienKho', 'Nhân viên kho' => 'NhanVienKho',
    'VT05'         => 'KiemKe',       'KiemKe'        => 'KiemKe',       'Nhóm kiểm kê'  => 'KiemKe',      'Nhân viên kiểm kê' => 'KiemKe',
    'VT06'         => 'QC',           'QC'            => 'QC',           'Bộ phận QC'    => 'QC',          'KCS/QC' => 'QC', 'KCS' => 'QC',
    'VT07'         => 'BanGiamDoc',   'BanGiamDoc'    => 'BanGiamDoc',   'Ban giám đốc'  => 'BanGiamDoc'
];
$role = $roleMap[$rawRole] ?? '';
$currentPage = $page ?? ($_GET['page'] ?? 'vPhieuNhapTP');

// Danh sách chức năng chuẩn theo đặc tả 6 vai trò dự án (Thứ tự và số lượng 100% khớp bảng thiết kế)
$roleMenus = [
    // 1. Quản trị viên (2 chức năng)
    'Admin' => [
        ['id' => 'vTaiKhoan',            'title' => 'Quản lý tài khoản',               'icon' => 'fa-solid fa-user-gear'],
        ['id' => 'vVaiTro',              'title' => 'Quản lý vai trò & quyền',        'icon' => 'fa-solid fa-shield-halved']
    ],
    // 2. Quản lý kho (12 chức năng)
    'QuanLyKho' => [
        ['id' => 'vPhieuYeuCauNhapXuat', 'title' => 'Xem yêu cầu nhập/xuất',            'icon' => 'fa-solid fa-file-lines'],
        ['id' => 'vNguyenLieu',          'title' => 'Quản lý nguyên liệu',             'icon' => 'fa-solid fa-wheat-awn'],
        ['id' => 'vDieuPhoiNhapNL',      'title' => 'Điều phối nhập nguyên liệu',      'icon' => 'fa-solid fa-diagram-project'],
        ['id' => 'vDieuPhoiXuatNL',      'title' => 'Điều phối xuất NVL FEFO',         'icon' => 'fa-solid fa-box-open'],
        ['id' => 'vDieuPhoiNhapTP',      'title' => 'Điều phối nhập thành phẩm',       'icon' => 'fa-solid fa-route'],
        ['id' => 'vThanhPham',           'title' => 'Quản lý thành phẩm',              'icon' => 'fa-solid fa-cookie-bite'],
        ['id' => 'vDieuPhoiXuatTP',      'title' => 'Điều phối xuất thành phẩm',       'icon' => 'fa-solid fa-truck-fast'],
        ['id' => 'vDeNghiTieuHuy',       'title' => 'Xử lý hàng hư hỏng/tiêu hủy',     'icon' => 'fa-solid fa-trash-can'],
        ['id' => 'vKho',                 'title' => 'Quản lý danh sách kho',           'icon' => 'fa-solid fa-warehouse'],
        ['id' => 'vXuLyChenhLech',       'title' => 'Xử lý chênh lệch sau kiểm kê',    'icon' => 'fa-solid fa-scale-unbalanced'],
        ['id' => 'vBaoCaoNhapXuat',      'title' => 'Báo cáo thống kê nhập/xuất',      'icon' => 'fa-solid fa-chart-line'],
        ['id' => 'vTonKho',              'title' => 'Xem báo cáo tồn kho',             'icon' => 'fa-solid fa-boxes-stacked']
    ],
    // 3. Nhân viên kho (Thủ kho) (5 chức năng)
    'NhanVienKho' => [
        ['id' => 'vPhieuNhapNL',         'title' => 'Lập phiếu nhập nguyên liệu',      'icon' => 'fa-solid fa-dolly'],
        ['id' => 'vPhieuXuatNL',         'title' => 'Lập phiếu xuất nguyên liệu',      'icon' => 'fa-solid fa-truck-ramp-box'],
        ['id' => 'vPhieuNhapTP',         'title' => 'Lập phiếu nhập thành phẩm',       'icon' => 'fa-solid fa-box-archive'],
        ['id' => 'vPhieuXuatTP',         'title' => 'Lập phiếu xuất thành phẩm',       'icon' => 'fa-solid fa-truck-moving'],
        ['id' => 'vViTriKho',            'title' => 'Quản lý vị trí hàng hóa',         'icon' => 'fa-solid fa-table-cells']
    ],
    // 4. Bộ phận QC (2 chức năng)
    'QC' => [
        ['id' => 'vKiemDinhNL',          'title' => 'Kiểm định nguyên liệu',           'icon' => 'fa-solid fa-clipboard-check'],
        ['id' => 'vKiemDinhTP',          'title' => 'Kiểm định thành phẩm',            'icon' => 'fa-solid fa-vial-circle-check']
    ],
    // 5. Nhóm kiểm kê (1 chức năng)
    'KiemKe' => [
        ['id' => 'vBienBanKiemKe',       'title' => 'Lập biên bản kiểm kê',            'icon' => 'fa-solid fa-list-check']
    ],
    // 6. Ban giám đốc (5 chức năng)
    'BanGiamDoc' => [
        ['id' => 'vBaoCaoNhapXuat',      'title' => 'Báo cáo thống kê nhập/xuất',      'icon' => 'fa-solid fa-chart-line'],
        ['id' => 'vTonKho',              'title' => 'Xem báo cáo tồn kho',             'icon' => 'fa-solid fa-boxes-stacked'],
        ['id' => 'vPheDuyetXuatBoSung',  'title' => 'Phê duyệt xuất bổ sung',          'icon' => 'fa-solid fa-file-circle-check'],
        ['id' => 'vPhieuPheDuyet',       'title' => 'Phê duyệt kiểm kê & tiêu hủy',    'icon' => 'fa-solid fa-stamp'],
        ['id' => 'vBaoCaoThongKeTP',     'title' => 'Báo cáo thống kê thành phẩm',     'icon' => 'fa-solid fa-chart-pie']
    ]
];

$displayList = $roleMenus[$role] ?? [];
?>
<aside id="leftSidebar" 
    class="sidebar-transition w-80 bg-white border-r border-stone-200 flex flex-col shrink-0 z-20 overflow-hidden shadow-sm">
    <nav id="sidebarNavContainer" class="flex-1 overflow-y-auto py-4 px-3 space-y-1.5">
        <?php foreach ($displayList as $fn): 
            $isActive = ($currentPage === $fn['id']);
            $activeClasses = $isActive
                ? 'bg-amber-50 text-amber-900 font-bold border-l-4 border-amber-600 shadow-sm'
                : 'text-stone-700 hover:text-stone-900 hover:bg-stone-100 font-medium border-l-4 border-transparent';
            $iconClasses = $isActive
                ? 'bg-amber-600 text-white shadow-sm'
                : 'bg-stone-100 text-stone-500 group-hover:bg-amber-100 group-hover:text-amber-700';
        ?>
            <a href="index.php?page=<?= $fn['id'] ?>"
                class="group w-full text-left px-3.5 py-3 rounded-r-xl transition-all flex items-center gap-3.5 text-sm whitespace-nowrap <?= $activeClasses ?>">
                <div class="w-9 h-9 rounded-lg <?= $iconClasses ?> flex items-center justify-center shrink-0 transition-colors">
                    <i class="<?= $fn['icon'] ?> text-sm"></i>
                </div>
                <span class="truncate tracking-wide"><?= $fn['title'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
