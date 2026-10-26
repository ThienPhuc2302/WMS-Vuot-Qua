<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;
if (!$user) {
    header("Location: index.php?page=trangchu&error=unauthorized");
    exit();
}

$currentPage = $page ?? ($_GET['page'] ?? 'vDieuPhoiXuatTP');
$displayRole = $user['vaiTro'] ?? 'Nhân viên';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiscuitWMS - Hệ Thống Quản Lý Kho</title>
    <!-- CSRF Token & Kho dữ liệu WMS toàn cục cho toàn bộ hệ thống BiscuitWMS -->
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <script>
        window.csrfToken = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>';
        window.wmsData = window.wmsData || {};
    </script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#1c1917',
                            900: '#0c0a09',
                        },
                        stone: {
                            50: '#fafaf9',
                            100: '#f5f5f4',
                            200: '#e7e5e4',
                            300: '#d6d3d1',
                            400: '#a8a29e',
                            500: '#78716c',
                            600: '#57534e',
                            700: '#44403c',
                            800: '#292524',
                            900: '#1c1917',
                            950: '#0c0a09',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fafaf9;
            color: #1c1917;
        }
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f5f5f4;
        }
        ::-webkit-scrollbar-thumb {
            background: #d6d3d1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a29e;
        }
        .sidebar-transition {
            transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1), width 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: margin-left, transform;
        }
        .erp-table th {
            background-color: #f5f5f4;
            color: #44403c;
            font-weight: 600;
            font-size: 0.875rem;
            border-bottom: 1px solid #e7e5e4;
            white-space: nowrap;
        }
        .erp-table td {
            border-bottom: 1px solid #f5f5f4;
            font-size: 0.875rem;
            color: #1c1917;
            white-space: nowrap;
        }
        .erp-table tbody tr:hover td {
            background-color: #fafaf9;
        }
    </style>
    <script src="assets/js/main.js"></script>
</head>
<body class="h-screen flex flex-col overflow-hidden">

    <!-- ==================== HEADER (NÂU BÁNH QUY ĐỒNG BỘ VỚI TRANG CHỦ) ==================== -->
    <header class="h-16 bg-[#28150c] text-white flex items-center justify-between px-5 sm:px-6 border-b border-[#422415] shrink-0 z-30 shadow-md">
        <div class="flex items-center gap-4">
            <button id="btnToggleSidebar" onclick="toggleSidebar()" title="Đóng/Mở menu"
                class="w-10 h-10 rounded-xl flex items-center justify-center text-amber-200/90 hover:text-white hover:bg-[#3d2113] active:scale-90 focus:outline-none transition-all duration-200">
                <i id="sidebarToggleIcon" class="fa-solid fa-bars text-lg transition-transform duration-300"></i>
            </button>
            
            <a href="index.php?page=trangchu" class="flex items-center gap-3 group" title="Về Trang Chủ">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white shadow-md shadow-amber-600/20 border border-amber-400/40">
                    <i class="fa-solid fa-boxes-stacked text-base text-amber-100"></i>
                </div>
                <div>
                    <span class="font-extrabold text-base sm:text-lg tracking-tight text-white group-hover:text-amber-400 transition-colors">HỆ THỐNG QUẢN LÝ KHO</span>
                    <div class="text-[10px] text-amber-400 font-semibold tracking-wider uppercase -mt-0.5">BiscuitWMS • Enterprise</div>
                </div>
            </a>
        </div>

        <div class="flex items-center gap-3 sm:gap-4 shrink-0">
            <!-- Nút Đăng Xuất -->
            <a href="logout.php" 
                class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl bg-[#3d2113] hover:bg-[#522b17] text-amber-100 hover:text-white text-sm font-semibold border border-[#5d341f] hover:border-amber-500/50 shadow-sm transition active:scale-95 cursor-pointer shrink-0"
                title="Đăng xuất khỏi ca trực và về trang chủ">
                <i class="fa-solid fa-arrow-right-from-bracket text-amber-400 text-sm"></i>
                <span>Đăng xuất</span>
            </a>

            <!-- Chuông thông báo -->
            <button onclick="showToast('Không có thông báo mới.')"
                class="w-9 h-9 rounded-lg flex items-center justify-center text-amber-200/80 hover:text-white hover:bg-[#3d2113] transition relative shrink-0">
                <i class="fa-regular fa-bell text-base"></i>
                <span class="absolute top-2 right-2 w-2 h-2 bg-amber-400 rounded-full"></span>
            </button>

            <!-- Khung thông tin nhân viên ca trực (Ô gọn gàng, không bị khoảng trống thừa bên phải) -->
            <div class="flex items-center gap-3 pl-3 sm:pl-4 border-l border-[#422415] shrink-0">
                <div class="w-9 h-9 rounded-full bg-amber-600 text-white flex items-center justify-center text-xs font-bold shadow-inner shrink-0" id="wmsUserBadge">
                    <?= mb_substr($user['hoTen'] ?? 'QL', 0, 2) ?>
                </div>
                <div class="hidden sm:block text-left min-w-0">
                    <div id="headerActorRole" class="text-xs font-bold text-white truncate" title="Vai trò ca trực"><?= htmlspecialchars($displayRole) ?></div>
                    <div id="headerWarehouseScope" class="text-[11px] text-emerald-400 font-medium truncate" title="Khu vực trực">
                        <?= htmlspecialchars($user['hoTen'] ?? 'Đang trong ca trực') ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ==================== BODY WRAPPER ==================== -->
    <div class="flex-1 flex overflow-hidden relative">

        <!-- Lớp phủ mờ trên màn hình nhỏ -->
        <div id="sidebarBackdrop" onclick="toggleSidebar()" 
            class="fixed inset-0 bg-stone-900/40 z-20 hidden md:hidden"></div>

        <!-- ==================== LEFT SIDEBAR ==================== -->
        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- ==================== MAIN CONTENT + FOOTER ==================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-stone-100">
            <main id="mainWorkspace" class="flex-1 overflow-y-auto p-5 md:p-6 pb-2">
