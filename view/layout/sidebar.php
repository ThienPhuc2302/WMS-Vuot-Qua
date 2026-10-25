<?php
$user = $_SESSION['user'] ?? ['vaiTro' => 'QuanLyKho', 'hoTen' => 'Nguyễn Văn An'];
$role = $user['vaiTro'] ?? 'QuanLyKho';
$currentPage = $page ?? ($_GET['page'] ?? 'vDieuPhoiXuatTP');

// =========================================================================
// MA TRẬN PHÂN QUYỀN CHUẨN XÁC 100% THEO ĐẶC TẢ BÁO CÁO GIỮA KỲ
// Nghiêm ngặt: Mỗi vai trò chỉ thấy đúng các chức năng được phân công trong báo cáo!
// =========================================================================

$allFunctions = [
    // --- 1. NHÓM QUẢN LÝ KHO (12 chức năng - Actor: Quản lý kho) ---
    ['id' => 'vPhieuYeuCauNhapXuat', 'title' => 'Xem yêu cầu nhập/xuất',            'icon' => 'fa-solid fa-file-lines',       'roles' => ['QuanLyKho']],
    ['id' => 'vNguyenLieu',          'title' => 'Quản lý nguyên liệu',             'icon' => 'fa-solid fa-wheat-awn',        'roles' => ['QuanLyKho']],
    ['id' => 'vDieuPhoiNhapNL',      'title' => 'Điều phối nhập nguyên liệu',      'icon' => 'fa-solid fa-diagram-project',  'roles' => ['QuanLyKho']],
    ['id' => 'vDieuPhoiXuatNL',      'title' => 'Điều phối xuất NVL FEFO',         'icon' => 'fa-solid fa-box-open',         'roles' => ['QuanLyKho']],
    ['id' => 'vDieuPhoiNhapTP',      'title' => 'Điều phối nhập thành phẩm',       'icon' => 'fa-solid fa-route',            'roles' => ['QuanLyKho']],
    ['id' => 'vThanhPham',           'title' => 'Quản lý thành phẩm',              'icon' => 'fa-solid fa-cookie-bite',      'roles' => ['QuanLyKho']],
    ['id' => 'vDieuPhoiXuatTP',      'title' => 'Điều phối xuất thành phẩm',       'icon' => 'fa-solid fa-truck-fast',       'roles' => ['QuanLyKho']],
    ['id' => 'vDeNghiTieuHuy',       'title' => 'Xử lý hàng hư hỏng/tiêu hủy',     'icon' => 'fa-solid fa-trash-can',        'roles' => ['QuanLyKho']],
    ['id' => 'vKho',                 'title' => 'Quản lý danh sách kho',           'icon' => 'fa-solid fa-warehouse',        'roles' => ['QuanLyKho']],
    ['id' => 'vXuLyChenhLech',       'title' => 'Xử lý chênh lệch sau kiểm kê',    'icon' => 'fa-solid fa-scale-unbalanced', 'roles' => ['QuanLyKho']],
    ['id' => 'vBaoCaoNhapXuat',      'title' => 'Báo cáo thống kê nhập/xuất',      'icon' => 'fa-solid fa-chart-line',       'roles' => ['QuanLyKho', 'BanGiamDoc']],
    ['id' => 'vTonKho',              'title' => 'Xem báo cáo tồn kho',             'icon' => 'fa-solid fa-boxes-stacked',    'roles' => ['QuanLyKho', 'BanGiamDoc']],

    // --- 2. NHÓM NHÂN VIÊN KHO (5 chức năng - Actor: Nhân viên kho) ---
    ['id' => 'vPhieuNhapNL',         'title' => 'Lập phiếu nhập nguyên liệu',      'icon' => 'fa-solid fa-dolly',            'roles' => ['NhanVienKho']],
    ['id' => 'vPhieuXuatNL',         'title' => 'Lập phiếu xuất nguyên liệu',      'icon' => 'fa-solid fa-truck-ramp-box',   'roles' => ['NhanVienKho']],
    ['id' => 'vPhieuNhapTP',         'title' => 'Lập phiếu nhập thành phẩm',       'icon' => 'fa-solid fa-box-archive',      'roles' => ['NhanVienKho']],
    ['id' => 'vPhieuXuatTP',         'title' => 'Lập phiếu xuất thành phẩm',       'icon' => 'fa-solid fa-truck-moving',     'roles' => ['NhanVienKho']],
    ['id' => 'vViTriKho',            'title' => 'Quản lý vị trí hàng hóa',         'icon' => 'fa-solid fa-table-cells',      'roles' => ['NhanVienKho']],

    // --- 3. NHÓM BỘ PHẬN QC (2 chức năng - Actor: Bộ phận QC) ---
    ['id' => 'vKiemDinhNL',          'title' => 'Kiểm định nguyên liệu',           'icon' => 'fa-solid fa-clipboard-check',  'roles' => ['QC']],
    ['id' => 'vKiemDinhTP',          'title' => 'Kiểm định thành phẩm',            'icon' => 'fa-solid fa-vial-circle-check','roles' => ['QC']],

    // --- 4. NHÓM KIỂM KÊ (1 chức năng - Actor: Nhóm kiểm kê) ---
    ['id' => 'vBienBanKiemKe',       'title' => 'Lập biên bản kiểm kê',            'icon' => 'fa-solid fa-list-check',       'roles' => ['KiemKe']],

    // --- 5. NHÓM BAN GIÁM ĐỐC (5 chức năng - Actor: Ban giám đốc) ---
    ['id' => 'vPheDuyetXuatBoSung',  'title' => 'Phê duyệt xuất bổ sung',          'icon' => 'fa-solid fa-file-circle-check','roles' => ['BanGiamDoc']],
    ['id' => 'vPhieuPheDuyet',       'title' => 'Phê duyệt kiểm kê & tiêu hủy',    'icon' => 'fa-solid fa-stamp',            'roles' => ['BanGiamDoc']],
    ['id' => 'vBaoCaoThongKeTP',     'title' => 'Báo cáo thống kê thành phẩm',     'icon' => 'fa-solid fa-chart-pie',        'roles' => ['BanGiamDoc']],

    // --- 6. NHÓM QUẢN TRỊ VIÊN (2 chức năng - Actor: Quản trị viên) ---
    ['id' => 'vTaiKhoan',            'title' => 'Quản lý tài khoản',               'icon' => 'fa-solid fa-user-gear',        'roles' => ['Admin']],
    ['id' => 'vVaiTro',              'title' => 'Quản lý vai trò & quyền',        'icon' => 'fa-solid fa-shield-halved',    'roles' => ['Admin']]
];

// Lọc chức năng hiển thị nghiêm ngặt 100% theo vai trò, KHÔNG hiển thị thừa
$displayList = [];
foreach ($allFunctions as $fn) {
    if (in_array($role, $fn['roles'])) {
        $displayList[] = $fn;
    }
}
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
