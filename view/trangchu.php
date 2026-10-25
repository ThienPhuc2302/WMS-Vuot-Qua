<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loginErrorMsg = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Hệ Thống Điều Hành Kho Bánh Quy</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            950: '#451a03',
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
            overflow-x: hidden;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .animate-shake {
            animation: shake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }

        /* Hero Background with Cinematic Ken Burns & Overlay (Kho hàng thực thụ) */
        .hero-banner-container {
            position: relative;
            min-height: calc(100vh - 5rem);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            padding: 2rem 1rem 1rem 1rem;
        }

        .hero-bg-image {
            position: absolute;
            inset: 0;
            background-image: url('assets/img/food_warehouse_hero.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            animation: kenburns 35s ease-in-out infinite alternate;
            z-index: 0;
        }

        @keyframes kenburns {
            0% { transform: scale(1); }
            100% { transform: scale(1.06); }
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(17, 12, 8, 0.65) 0%, rgba(17, 12, 8, 0.78) 100%);
            z-index: 1;
        }

        /* Khung Hình Chữ Nhật Dài Dưới Đáy Hero (Trong suốt thuần khiết, không bị mờ đục) */
        .rect-glass-dock {
            background: rgba(15, 10, 7, 0.18);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.10);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        /* Đường dẫn dòng chảy tinh tế nối liền 4 trạm nghiệp vụ (Static Elegant Pipeline) */
        .flow-pipeline-track {
            position: absolute;
            top: 34px;
            left: 2.5rem;
            right: 2.5rem;
            height: 2px;
            background: linear-gradient(90deg, 
                rgba(6, 182, 212, 0.4) 0%, 
                rgba(245, 158, 11, 0.4) 35%, 
                rgba(16, 185, 129, 0.4) 70%, 
                rgba(168, 85, 247, 0.4) 100%
            );
            border-radius: 9999px;
            pointer-events: none;
            z-index: 1;
        }



        /* Smooth scroll with header offset */
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 5rem; /* Bằng chính xác chiều cao header h-20 (80px) để cuộn dừng chuẩn xác, không bị đè lố */
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
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
    </style>
</head>
<body class="min-h-screen flex flex-col bg-stone-50">

    <!-- ==================== HEADER (NÂU BÁNH QUY ĐỒNG BỘ VỚI WMS) ==================== -->
    <header class="sticky top-0 z-40 bg-[#28150c] text-white border-b border-[#422415] shadow-md">
        <div class="w-full px-6 sm:px-10 lg:px-14 xl:px-16 h-20 flex items-center justify-between">
            
            <!-- Logo & Brand Name (Căn trái thoáng đãng, bấm để về đầu trang mượt mà) -->
            <a href="#hero" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="flex items-center gap-3.5 cursor-pointer group">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white shadow-md shadow-amber-600/20 border border-amber-400/40 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-boxes-stacked text-xl text-amber-100"></i>
                </div>
                <div>
                    <span class="font-extrabold text-lg sm:text-xl tracking-tight text-white group-hover:text-amber-200 transition-colors">HỆ THỐNG QUẢN LÝ KHO</span>
                    <div class="text-[10px] text-amber-400 font-medium tracking-wider uppercase -mt-0.5">Nhà Máy Bánh Quy</div>
                </div>
            </a>

            <!-- Right Area: Navigation Links -->
            <div class="flex items-center gap-6 sm:gap-8">
                <!-- Navigation Links -->
                <nav class="flex items-center gap-6 sm:gap-8 text-sm font-semibold text-amber-100/80">
                    <a href="#hero" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="text-amber-400 hover:text-white transition-colors cursor-pointer">Trang chủ</a>
                    <a href="#quy-trinh" class="hover:text-amber-300 transition-colors">Quy trình kho</a>
                    <a href="#tra-cuu" class="hover:text-amber-300 transition-colors">Tra cứu lô trong kho</a>
                </nav>
            </div>

        </div>
    </header>

    <!-- ==================== HERO SECTION (FULL 100VH VIEWPORT - CÂN BẰNG TUYỆT ĐỐI) ==================== -->
    <section id="hero" class="hero-banner-container text-white border-b border-stone-300">
        
        <!-- Animated Background Image with Ken Burns Depth -->
        <div class="hero-bg-image"></div>
        <div class="hero-overlay"></div>

        <!-- Center Content: Title & Action (Gọn gàng, thoáng đãng, sang trọng) -->
        <div class="relative z-10 flex-1 flex flex-col justify-center items-center text-center px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto pt-10 sm:pt-16 pb-12 sm:pb-16 my-auto">
            
            <!-- Khối 1: Headline 2 dòng cân đối tuyệt đối -->
            <div class="w-full flex flex-col items-center justify-center text-center mx-auto mb-7 sm:mb-9">
                <h1 class="flex flex-col items-center justify-center text-center space-y-1 sm:space-y-1.5">
                    <span class="block text-2xl sm:text-3xl lg:text-[40px] font-bold text-white tracking-normal leading-snug drop-shadow-md">
                        Hệ Thống Điều Phối Kho
                    </span>
                    <span class="inline-block text-2xl sm:text-3xl lg:text-[40px] font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-300 to-amber-200 tracking-normal leading-relaxed pt-3 pb-2 sm:pt-4 sm:pb-2.5 drop-shadow-md">
                        Nguyên Phụ Liệu & Bánh Quy Thành Phẩm
                    </span>
                </h1>
            </div>

            <!-- Khối 2: Nút Hành Động Mở Modal Đăng Nhập Ca (Tượng trưng) -->
            <div class="flex justify-center">
                <button onclick="openLoginModal()" 
                    class="inline-flex items-center justify-center gap-3.5 px-9 py-4 rounded-full bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-sm sm:text-base shadow-2xl shadow-black/50 transition-all duration-300 hover:scale-105 active:scale-95 group">
                    <span>Bắt đầu ca làm việc</span>
                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-200 group-hover:translate-x-1.5"></i>
                </button>
            </div>

        </div>

        <!-- ==================== KHỐI HÌNH CHỮ NHẬT DÀI (KÉO XÍCH XUỐNG DƯỚI ĐÁY) ==================== -->
        <div class="relative z-10 w-full max-w-6xl xl:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-2 pt-2">
            
            <!-- Khung Hình Chữ Nhật Dài Liền Mạch, Hòa Quyện Vào Background (Seamless Liquid Glass Dock) -->
            <div class="rect-glass-dock rounded-3xl p-4 sm:p-5 shadow-2xl relative">
                
                <!-- Header: Chu Trình Vận Hành Kho Bánh Quy (Đã đổi theo yêu cầu) -->
                <div class="flex items-center justify-between px-2.5 pb-3 mb-3 border-b border-white/10">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-300">CHU TRÌNH VẬN HÀNH KHO BÁNH QUY</span>
                        <span class="hidden sm:inline text-xs text-stone-400">•</span>
                        <span class="hidden sm:inline text-xs text-stone-300 font-medium">Luân chuyển khép kín từ nguyên liệu đến thành phẩm</span>
                    </div>
                </div>

                <!-- Container Dòng Chảy Xuyên Suốt Cả 4 Nghiệp Vụ (Continuous Pipeline Flow) -->
                <div class="relative overflow-hidden rounded-2xl">
                    
                    <!-- Đường Ống Nối Liền Xuyên Suốt 4 Khối (Static Elegant Pipeline) -->
                    <div class="flow-pipeline-track hidden lg:block"></div>

                    <!-- 4 Nghiệp Vụ Kho Liền Mạch (Kiểm Định -> Nhập Kho -> Kiểm Kê -> Xuất Kho) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-1 lg:gap-2 text-left relative z-10">
                        
                        <!-- Nghiệp vụ 1: Kiểm Định (QC) -->
                        <div class="p-3.5 sm:p-4 rounded-2xl relative">
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="relative z-10 w-9 h-9 rounded-xl bg-cyan-500/25 border border-cyan-400/50 text-cyan-300 flex items-center justify-center text-sm font-bold shadow-lg shadow-cyan-500/20 backdrop-blur-md">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </div>
                            </div>
                            <h4 class="font-bold text-sm text-white">Kiểm Định (QC)</h4>
                            <div class="text-[10px] text-cyan-300/90 font-medium mb-1">Tiêu chuẩn HACCP</div>
                            <p class="text-[11px] text-stone-300/80 leading-relaxed">Kiểm tra mẫu bột, bơ sữa từ NCC & nghiệm thu mẻ bánh nướng từ xưởng.</p>
                        </div>

                        <!-- Nghiệp vụ 2: Nhập Kho -->
                        <div class="p-3.5 sm:p-4 rounded-2xl relative">
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="relative z-10 w-9 h-9 rounded-xl bg-amber-500/25 border border-amber-400/50 text-amber-300 flex items-center justify-center text-sm font-bold shadow-lg shadow-amber-500/20 backdrop-blur-md">
                                    <i class="fa-solid fa-dolly"></i>
                                </div>
                            </div>
                            <h4 class="font-bold text-sm text-white">Nhập Kho</h4>
                            <div class="text-[10px] text-amber-300/90 font-medium mb-1">Nguyên liệu & Thành phẩm</div>
                            <p class="text-[11px] text-stone-300/80 leading-relaxed">Chỉ định ô kệ lưu trữ và lập phiếu nhập kho chính thức khi QC đạt.</p>
                        </div>

                        <!-- Nghiệp vụ 3: Kiểm Kê Kho -->
                        <div class="p-3.5 sm:p-4 rounded-2xl relative">
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="relative z-10 w-9 h-9 rounded-xl bg-emerald-500/25 border border-emerald-400/50 text-emerald-300 flex items-center justify-center text-sm font-bold shadow-lg shadow-emerald-500/20 backdrop-blur-md">
                                    <i class="fa-solid fa-boxes-packing"></i>
                                </div>
                            </div>
                            <h4 class="font-bold text-sm text-white">Kiểm Kê Kho</h4>
                            <div class="text-[10px] text-emerald-300/90 font-medium mb-1">Đối soát & Cân bằng tồn</div>
                            <p class="text-[11px] text-stone-300/80 leading-relaxed">Kiểm đếm định kỳ số lượng thực tế, đối chiếu sổ sách và chốt tồn kho.</p>
                        </div>

                        <!-- Nghiệp vụ 4: Xuất Kho (FEFO) -->
                        <div class="p-3.5 sm:p-4 rounded-2xl relative">
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="relative z-10 w-9 h-9 rounded-xl bg-purple-500/25 border border-purple-400/50 text-purple-300 flex items-center justify-center text-sm font-bold shadow-lg shadow-purple-500/20 backdrop-blur-md">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                            </div>
                            <h4 class="font-bold text-sm text-white">Xuất Kho (FEFO)</h4>
                            <div class="text-[10px] text-purple-300/90 font-medium mb-1">Sản xuất & Giao hàng</div>
                            <p class="text-[11px] text-stone-300/80 leading-relaxed">Điều phối xuất nguyên liệu xưởng & xuất thành phẩm ưu tiên hạn gần.</p>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Nút cuộn xuống động mượt mà (Animated Scroll Down) -->
            <div class="flex justify-center mt-2.5">
                <a href="#quy-trinh" class="inline-flex items-center gap-1.5 text-xs text-stone-300/80 hover:text-amber-300 transition-colors animate-bounce">
                    <span>Khám phá quy trình kho</span>
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </a>
            </div>

        </div>

    </section>

    <!-- ==================== SECTION 2: QUY TRÌNH KHO (3 TRỤ CỘT CỐT LÕI) ==================== -->
    <section id="quy-trinh" class="py-20 bg-white border-b border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-900 bg-amber-100 px-3.5 py-1.5 rounded-full border border-amber-200">
                    Quy Trình Khép Kín
                </span>
                <h2 class="text-3xl font-extrabold text-stone-900 mt-3.5 tracking-tight">
                    Trọng Tâm Vận Hành Trong Kho Bánh Quy
                </h2>
                <p class="text-stone-600 text-sm sm:text-base mt-2">
                    Dữ liệu được cập nhật tức thì qua các ca trực, kiểm soát nghiêm ngặt từ đầu vào đến xuất xưởng.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Khối Đầu Vào -->
                <div class="bg-stone-50 rounded-2xl p-7 border border-stone-200 shadow-sm hover:shadow-md hover:border-amber-300 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-dolly"></i>
                    </div>
                    <div class="text-xs font-bold text-amber-700 tracking-wide uppercase mb-1">Đầu vào</div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2 group-hover:text-amber-800 transition-colors">Tiếp Nhận & Kiểm Định Nguyên Liệu</h3>
                    <p class="text-stone-600 text-xs sm:text-sm leading-relaxed">
                        Tiếp nhận bột mì, bơ lạt, đường tinh luyện từ nhà cung cấp. Kiểm định mẫu bột độ ẩm, tạp chất trước khi nhập kho lạnh hoặc silo.
                    </p>
                </div>

                <!-- Khối Sản Xuất & Nhập Kho -->
                <div class="bg-stone-50 rounded-2xl p-7 border border-amber-300/80 shadow-sm hover:shadow-md hover:border-amber-400 transition-all relative group">
                    <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl font-bold mb-5 shadow group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div class="text-xs font-bold text-amber-700 tracking-wide uppercase mb-1">Sản xuất & Nhập kho</div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2 group-hover:text-amber-800 transition-colors">Nhập Bánh Nướng & Lưu Trữ Ô Kệ</h3>
                    <p class="text-stone-600 text-xs sm:text-sm leading-relaxed">
                        Bánh quy nướng chín được kiểm định KCS đạt độ giòn và an toàn. Quản lý lập lệnh điều phối vị trí ô kệ lưu trữ và thủ kho hoàn tất phiếu nhập kho TP.
                    </p>
                </div>

                <!-- Khối Đầu Ra -->
                <div class="bg-stone-50 rounded-2xl p-7 border border-stone-200 shadow-sm hover:shadow-md hover:border-amber-300 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div class="text-xs font-bold text-amber-700 tracking-wide uppercase mb-1">Đầu ra</div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2 group-hover:text-amber-800 transition-colors">Điều Phối Xuất Bánh Chuẩn FEFO</h3>
                    <p class="text-stone-600 text-xs sm:text-sm leading-relaxed">
                        Tiếp nhận yêu cầu xuất hàng, hệ thống tự động lọc các lô bánh quy có hạn sử dụng gần nhất để xuất trước (FEFO), điều xe bốc dỡ nhanh chóng.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- ==================== SECTION 3: TRA CỨU LÔ TRONG KHO ==================== -->
    <section id="tra-cuu" class="py-16 bg-stone-50 border-b border-stone-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
            
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-semibold mb-3 border border-amber-200">
                <i class="fa-solid fa-qrcode text-amber-700"></i>
                <span>TIỆN ÍCH NỘI BỘ</span>
            </div>

            <h2 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                Tra Cứu Lô Trong Kho & Kiểm Định
            </h2>
            <p class="text-stone-600 text-sm mt-2 max-w-xl mx-auto">
                Nhập nhanh Mã Lô Thành Phẩm hoặc Nguyên Liệu để kiểm tra ngày sản xuất, hạn sử dụng và kết quả KCS.
            </p>

            <!-- Search Form -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 max-w-xl mx-auto">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                    <input type="text" id="inputBatchCode" placeholder="Ví dụ: MALOTP01, MALOTP02, MALONL01..." 
                        class="w-full pl-11 pr-4 py-3 rounded-full border border-stone-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                </div>
                <button onclick="handleSearchBatch()" 
                    class="w-full sm:w-auto px-7 py-3 rounded-full bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm transition shrink-0 active:scale-95 shadow">
                    Tra cứu ngay
                </button>
            </div>

            <!-- Result Box -->
            <div id="batchResultBox" class="hidden mt-6 bg-white border border-stone-200 rounded-2xl p-5 text-left max-w-xl mx-auto shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-3">
                    <span class="font-bold text-sm text-stone-900" id="resTitle">Lô Bánh Quy Bơ Danish #MALOTP01</span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold border border-emerald-200" id="resStatus">KCS Đạt chuẩn</span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs text-stone-600">
                    <div>Ngày sản xuất: <strong class="text-stone-900" id="resNSX">15/10/2026</strong></div>
                    <div>Hạn sử dụng: <strong class="text-stone-900" id="resHSD">15/10/2027</strong></div>
                    <div>Vị trí lưu kho: <strong class="text-amber-800 font-semibold" id="resLocation">Khu Vực Kho TP</strong></div>
                    <div>Số lượng tồn: <strong class="text-stone-900" id="resQty">1.250 hộp</strong></div>
                </div>
            </div>

        </div>
    </section>

    <!-- ==================== FOOTER (NÂU BÁNH QUY ĐỒNG BỘ - TINH TẾ & THỰC TẾ) ==================== -->
    <footer class="bg-[#24130a] text-stone-300 pt-12 pb-8 border-t border-[#3d2113]">
        <div class="w-full px-6 sm:px-10 lg:px-14 xl:px-16">
            
            <!-- Phần Nội dung 3 Cột (Chân thực, gọn gàng, đúng nghiệp vụ kho bánh) -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-[#3d2113]">
                
                <!-- Cột 1: Thông tin Nhà máy & Đơn vị vận hành (5 cols) -->
                <div class="md:col-span-5 space-y-3.5 text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 text-white flex items-center justify-center font-bold shadow-md shadow-amber-600/20 border border-amber-400/40">
                            <i class="fa-solid fa-boxes-stacked text-base text-amber-100"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-base tracking-tight text-white block">HỆ THỐNG QUẢN LÝ KHO</span>
                            <span class="text-[11px] text-amber-400 font-semibold tracking-wider uppercase">Nhà Máy Bánh Quy • BiscuitWMS</span>
                        </div>
                    </div>
                    <p class="text-xs text-stone-400 leading-relaxed max-w-sm">
                        Hệ thống số hóa luân chuyển nguyên phụ liệu và điều phối lưu trữ - xuất kho bánh quy thành phẩm theo quy trình công nghiệp khép kín.
                    </p>
                    <div class="space-y-1.5 text-xs text-stone-400 pt-1">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-amber-500/80 w-4 text-center text-xs shrink-0"></i>
                            <span>Khu công nghiệp Tân Bình, TP. Hồ Chí Minh</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-amber-500/80 w-4 text-center text-xs shrink-0"></i>
                            <span>Tổng đài nội bộ: (028) 1234 5678 (Máy nhánh 109)</span>
                        </div>
                    </div>
                </div>

                <!-- Cột 2: Điều hướng nội bộ (3 cols) -->
                <div class="md:col-span-3 space-y-3 text-left">
                    <h4 class="text-xs font-bold text-amber-300 uppercase tracking-wider">Tiện ích hệ thống</h4>
                    <ul class="space-y-2 text-xs text-stone-400">
                        <li>
                            <a href="#hero" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="hover:text-amber-300 transition-colors inline-flex items-center gap-2">
                                <i class="fa-solid fa-angle-right text-[10px] text-stone-600"></i>
                                <span>Trang chủ</span>
                            </a>
                        </li>
                        <li>
                            <a href="#quy-trinh" class="hover:text-amber-300 transition-colors inline-flex items-center gap-2">
                                <i class="fa-solid fa-angle-right text-[10px] text-stone-600"></i>
                                <span>Trọng tâm vận hành</span>
                            </a>
                        </li>
                        <li>
                            <a href="#tra-cuu" class="hover:text-amber-300 transition-colors inline-flex items-center gap-2">
                                <i class="fa-solid fa-angle-right text-[10px] text-stone-600"></i>
                                <span>Tra cứu lô trong kho</span>
                            </a>
                        </li>
                        <li>
                            <button onclick="openLoginModal()" class="hover:text-amber-300 transition-colors inline-flex items-center gap-2 text-left cursor-pointer">
                                <i class="fa-solid fa-angle-right text-[10px] text-stone-600"></i>
                                <span>Đăng nhập ca trực WMS</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Cột 3: Phạm vi tổ chức hệ thống kho (4 cols) -->
                <div class="md:col-span-4 space-y-3 text-left">
                    <h4 class="text-xs font-bold text-amber-300 uppercase tracking-wider">Tổ chức hệ thống kho</h4>
                    <div class="space-y-2 text-xs text-stone-400 leading-relaxed">
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-warehouse text-amber-500/80 mt-0.5 text-xs shrink-0"></i>
                            <span>Quy mô: 5 Kho Nguyên Liệu, 5 Kho Thành Phẩm & 1 Kho Hàng Lỗi</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-industry text-amber-500/80 mt-0.5 text-xs shrink-0"></i>
                            <span>Liên kết: Cung ứng & tiếp nhận từ 5 Phân xưởng sản xuất</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-boxes-stacked text-amber-500/80 mt-0.5 text-xs shrink-0"></i>
                            <span>Đặc tính: Quản lý theo Lô (NSX, HSD) & Ô kệ chuẩn FEFO</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Dòng Bản quyền & Thông tin đồ án (Bottom Bar) -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-stone-500 text-center sm:text-left">
                <div>
                    © 2026 <span class="text-stone-400 font-semibold">BiscuitWMS</span>. Hệ thống Quản lý Kho Nhà Máy Bánh Quy.
                </div>
                <div class="text-[11px] text-stone-400">
                    Đồ án môn Phát triển Ứng dụng • Trường Đại học Công nghiệp TP.HCM (IUH)
                </div>
            </div>

        </div>
    </footer>

    <!-- ==================== LOGIN MODAL (CHỈ HIỂN THỊ VAI TRÒ, RỘNG RÃI THOÁNG ĐÃNG) ==================== -->
    <div id="loginModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 sm:p-6 bg-black/65 backdrop-blur-md transition-opacity">
        <div class="bg-white rounded-[28px] w-full max-w-lg sm:max-w-[540px] shadow-2xl border border-stone-200 overflow-hidden transform transition-all">
            
            <!-- Modal Header (Rộng rãi, sang trọng) -->
            <div class="bg-gradient-to-r from-[#28150c] via-[#3a2012] to-[#28150c] border-b border-[#4d2a19] p-7 sm:p-8 text-white text-left relative">
                <button onclick="closeLoginModal()" class="absolute top-6 right-6 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-base transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/25 text-amber-300 flex items-center justify-center text-xl mb-3.5 border border-amber-500/40 shadow-inner">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h3 class="text-2xl font-black text-white tracking-tight">Đăng Nhập Ca Trực WMS</h3>
                <p class="text-sm text-amber-200/90 mt-1">Xác thực vai trò nhân viên nhà máy để bắt đầu ca làm việc</p>
            </div>

            <!-- Modal Body Form (To thoáng, dễ nhìn) -->
            <div class="p-7 sm:p-8 text-left space-y-5">
                
                <!-- Alert Error Box (Cao cấp, mượt mà, hiệu ứng rung animate-shake, KHÔNG DÙNG ALERT TRÌNH DUYỆT) -->
                <div id="loginErrorAlert" class="hidden rounded-2xl bg-rose-50 border border-rose-200/90 p-4 text-rose-800 flex items-start gap-3.5 text-sm shadow-sm transition-all duration-200 animate-shake">
                    <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-rose-950 text-sm">Xác thực thất bại</div>
                        <div class="text-xs text-rose-700 mt-0.5 leading-relaxed" id="loginErrorMsg">Tài khoản hoặc mật khẩu không chính xác!</div>
                    </div>
                    <button type="button" onclick="hideLoginError()" class="text-rose-400 hover:text-rose-700 p-1 rounded-lg transition">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>

                <!-- Box Thông tin Quên Mật Khẩu (Popup trong suốt mượt mà, thay thế hoàn toàn alert) -->
                <div id="forgotPasswordNotice" class="hidden rounded-2xl bg-amber-50 border border-amber-200 p-4 text-amber-900 flex items-start gap-3.5 text-sm shadow-sm transition-all duration-200">
                    <div class="w-8 h-8 rounded-xl bg-amber-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                        <i class="fa-solid fa-headset text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0 text-xs">
                        <div class="font-bold text-amber-950 text-sm mb-1">Cấp lại mật khẩu ca trực</div>
                        <div class="text-stone-700 space-y-1">
                            <p>Vui lòng liên hệ trực tiếp <strong>Kỹ thuật WMS</strong> qua máy nhánh nội bộ <strong>109</strong> hoặc hotline quản trị <strong>(028) 3894.0390</strong> để được cấp lại mật khẩu ca làm việc.</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('forgotPasswordNotice').classList.add('hidden')" class="text-amber-500 hover:text-amber-800 p-1 rounded-lg transition">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>

                <!-- Email / Username Input -->
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-stone-700 mb-1.5 uppercase tracking-wide">Mã tài khoản / Email vai trò</label>
                    <div class="relative">
                        <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-base"></i>
                        <input type="text" id="loginUsername" value="" placeholder="Nhập tài khoản (VD: admin, quanly, qc, thukho...)" 
                            autocomplete="off"
                            oninput="clearInputError(this)"
                            onkeydown="if(event.key === 'Enter') handleLoginSubmit()"
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-amber-600 text-sm sm:text-base bg-stone-50 transition-all">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-stone-700 mb-1.5 uppercase tracking-wide">Mật khẩu ca trực</label>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-base"></i>
                        <input type="password" id="loginPassword" value="" 
                            autocomplete="off"
                            oninput="clearInputError(this)"
                            onkeydown="if(event.key === 'Enter') handleLoginSubmit()"
                            class="w-full pl-11 pr-11 py-3.5 rounded-2xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-amber-600 text-sm sm:text-base bg-stone-50 transition-all">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 text-base">
                            <i class="fa-regular fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between text-xs sm:text-sm text-stone-600 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="rememberMeCheckbox" class="rounded text-amber-600 focus:ring-amber-600 w-4 h-4">
                        <span class="font-medium">Ghi nhớ ca này</span>
                    </label>
                    <button type="button" onclick="showForgotPasswordHelp()" class="text-amber-700 hover:text-amber-800 hover:underline font-semibold cursor-pointer">Quên mật khẩu?</button>
                </div>

                <!-- Submit Button -->
                <button id="btnLoginSubmit" onclick="handleLoginSubmit()" 
                    class="w-full py-4 rounded-2xl bg-amber-600 hover:bg-amber-500 text-white font-extrabold text-base shadow-xl shadow-amber-700/25 transition-all duration-200 active:scale-95 cursor-pointer flex items-center justify-center gap-2.5">
                    <span>Xác nhận vào ca làm việc</span>
                </button>
            </div>

        </div>
    </div>

    <!-- ==================== TOAST NOTIFICATION (NÂNG CẤP GIAO DIỆN CAO CẤP) ==================== -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden bg-stone-900/95 backdrop-blur-md text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-stone-700 flex items-center gap-3 text-xs sm:text-sm font-semibold transition-all duration-300">
        <i class="fa-solid fa-circle-check text-emerald-400 text-base" id="toastIcon"></i>
        <span id="toastMsg">Thông báo</span>
    </div>

    <!-- ==================== JAVASCRIPT ==================== -->
    <script>
        // Tự động mở modal và hiện lỗi nếu đăng nhập từ form thường trả về
        <?php if (!empty($loginErrorMsg)): ?>
        document.addEventListener('DOMContentLoaded', () => {
            openLoginModal();
            showLoginError(<?= json_encode($loginErrorMsg) ?>);
        });
        <?php endif; ?>

        // Xử lý khi đăng xuất: hiển thị toast và xóa sạch thông tin ghi nhớ
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('logged_out') === '1') {
                showToast('Đã đăng xuất ca làm việc thành công!', 'info');
                try {
                    localStorage.removeItem('currentUserRole');
                } catch(e) {}
                const cleanUrl = window.location.pathname + (urlParams.get('page') ? '?page=' + urlParams.get('page') : '');
                window.history.replaceState({}, document.title, cleanUrl);
            }
        });

        // Chỉ lưu vai trò, không lưu tên riêng
        const ROLES_MAP = {
            thukho_nl:     { role: 'Thủ kho Nguyên liệu', email: 'thukhonl@vuotqua.vn', badge: 'NL' },
            kcs:           { role: 'Nhân viên KCS', email: 'kcs@vuotqua.vn', badge: 'KC' },
            quanly_tp:     { role: 'Quản lý Kho Thành phẩm', email: 'quanlykhotp@vuotqua.vn', badge: 'QL' },
            thukho_tp:     { role: 'Thủ kho Thành phẩm', email: 'thukhotp@vuotqua.vn', badge: 'TP' },
            dieuphoi_xuat: { role: 'Điều phối xuất kho', email: 'dieuphoixuat@vuotqua.vn', badge: 'ĐP' },
            admin:         { role: 'Quản trị hệ thống', email: 'admin@vuotqua.vn', badge: 'AD' }
        };

        let currentRole = null;

        function openLoginModal() {
            hideLoginError();
            const uInput = document.getElementById('loginUsername');
            const pInput = document.getElementById('loginPassword');
            if (uInput) uInput.value = '';
            if (pInput) pInput.value = '';
            document.getElementById('loginModal').classList.remove('hidden');
            setTimeout(() => {
                if (uInput) uInput.focus();
            }, 100);
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.add('hidden');
            hideLoginError();
            const uInput = document.getElementById('loginUsername');
            const pInput = document.getElementById('loginPassword');
            if (uInput) uInput.value = '';
            if (pInput) pInput.value = '';
        }

        function showLoginError(msg) {
            const alertBox = document.getElementById('loginErrorAlert');
            const msgEl = document.getElementById('loginErrorMsg');
            if (alertBox && msgEl) {
                msgEl.textContent = msg;
                alertBox.classList.remove('hidden');
                // Khởi động lại hiệu ứng rung
                alertBox.classList.remove('animate-shake');
                void alertBox.offsetWidth;
                alertBox.classList.add('animate-shake');
            }
        }

        function hideLoginError() {
            const alertBox = document.getElementById('loginErrorAlert');
            if (alertBox) alertBox.classList.add('hidden');
            const uInput = document.getElementById('loginUsername');
            const pInput = document.getElementById('loginPassword');
            if (uInput) uInput.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200', 'bg-rose-50/50');
            if (pInput) pInput.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200', 'bg-rose-50/50');
        }

        function clearInputError(input) {
            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200', 'bg-rose-50/50');
        }

        function showForgotPasswordHelp() {
            const notice = document.getElementById('forgotPasswordNotice');
            if (notice) {
                notice.classList.toggle('hidden');
            }
        }

        function togglePasswordVisibility() {
            const passInput = document.getElementById('loginPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }


        async function handleLoginSubmit() {
            const uInput = document.getElementById('loginUsername');
            const pInput = document.getElementById('loginPassword');
            const submitBtn = document.getElementById('btnLoginSubmit');
            const u = uInput.value.trim();
            const p = pInput.value.trim();

            hideLoginError();

            if (!u) {
                showLoginError('Vui lòng nhập tên đăng nhập hoặc mã tài khoản ca trực!');
                uInput.focus();
                uInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-200', 'bg-rose-50/50');
                showToast('Vui lòng nhập tên đăng nhập!', 'error');
                return;
            }
            if (!p) {
                showLoginError('Vui lòng nhập mật khẩu ca trực!');
                pInput.focus();
                pInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-200', 'bg-rose-50/50');
                showToast('Vui lòng nhập mật khẩu ca trực!', 'error');
                return;
            }

            const originalHTML = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-base"></i><span>Đang xác thực thông tin...</span>';

            try {
                const formData = new FormData();
                formData.append('username', u);
                formData.append('password', p);
                formData.append('is_ajax', '1');

                const res = await fetch('login.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await res.json();

                if (data.success) {
                    submitBtn.innerHTML = '<i class="fa-solid fa-circle-check text-base"></i><span>Xác thực thành công! Đang chuyển hướng...</span>';
                    submitBtn.classList.replace('bg-amber-600', 'bg-emerald-600');
                    submitBtn.classList.replace('hover:bg-amber-500', 'hover:bg-emerald-500');
                    showToast('Đăng nhập thành công! Đang chuyển hướng vào hệ thống...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 400);
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                    showLoginError(data.message || 'Tài khoản hoặc mật khẩu không chính xác!');
                    pInput.focus();
                    pInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-200', 'bg-rose-50/50');
                    showToast(data.message || 'Tài khoản hoặc mật khẩu không chính xác!', 'error');
                }
            } catch (err) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
                showLoginError('Không thể kết nối máy chủ xác thực. Vui lòng thử lại!');
                showToast('Lỗi kết nối máy chủ xác thực!', 'error');
            }
        }

        function showToast(msg, type = 'info') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMsg');
            const toastIcon = document.getElementById('toastIcon');
            if (!toast || !toastMsg) return;

            toastMsg.textContent = msg;

            if (type === 'error') {
                toast.className = 'fixed bottom-6 right-6 z-50 bg-rose-950/95 backdrop-blur-md text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-rose-500/50 flex items-center gap-3 text-xs sm:text-sm font-semibold transition-all duration-300';
                if (toastIcon) toastIcon.className = 'fa-solid fa-circle-exclamation text-rose-400 text-base';
            } else if (type === 'success') {
                toast.className = 'fixed bottom-6 right-6 z-50 bg-emerald-950/95 backdrop-blur-md text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-emerald-500/50 flex items-center gap-3 text-xs sm:text-sm font-semibold transition-all duration-300';
                if (toastIcon) toastIcon.className = 'fa-solid fa-circle-check text-emerald-400 text-base';
            } else {
                toast.className = 'fixed bottom-6 right-6 z-50 bg-stone-900/95 backdrop-blur-md text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-amber-600/40 flex items-center gap-3 text-xs sm:text-sm font-semibold transition-all duration-300';
                if (toastIcon) toastIcon.className = 'fa-solid fa-circle-info text-amber-400 text-base';
            }

            toast.classList.remove('hidden');

            if (window.toastTimeout) clearTimeout(window.toastTimeout);
            window.toastTimeout = setTimeout(() => {
                toast.classList.add('hidden');
            }, 3500);
        }

        function handleLoginSuccess(r) {
            currentRole = r;
            closeLoginModal();
            try {
                localStorage.setItem('currentUserRole', JSON.stringify(r));
            } catch(e) {}

            showToast(`Đăng nhập ca trực: ${r.role}. Đang chuyển đến không gian làm việc...`);
            setTimeout(() => {
                window.location.href = 'index.php?page=vDieuPhoiXuatTP';
            }, 500);
        }

        function handleStartWorking() {
            openLoginModal();
        }

        function handleSearchBatch() {
            const code = document.getElementById('inputBatchCode').value.trim().toUpperCase();
            const resBox = document.getElementById('batchResultBox');
            if (!code) {
                showToast('Vui lòng nhập mã lô (VD: MALOTP01)');
                return;
            }

            resBox.classList.remove('hidden');
            if (code.includes('NL')) {
                document.getElementById('resTitle').innerText = `Lô Bột Mì Hoa Ngọc Trời #${code}`;
                document.getElementById('resStatus').innerText = 'KCS Đạt chuẩn';
                document.getElementById('resNSX').innerText = '02/10/2026';
                document.getElementById('resHSD').innerText = '02/10/2027';
                document.getElementById('resLocation').innerText = 'Silo Bột Số 2';
                document.getElementById('resQty').innerText = '5.000 kg';
            } else {
                document.getElementById('resTitle').innerText = `Lô Bánh Quy Bơ Danish #${code}`;
                document.getElementById('resStatus').innerText = 'KCS Đạt chuẩn';
                document.getElementById('resNSX').innerText = '15/10/2026';
                document.getElementById('resHSD').innerText = '15/10/2027';
                document.getElementById('resLocation').innerText = 'Khu Vực Kho TP';
                document.getElementById('resQty').innerText = '1.250 hộp';
            }
            showToast(`Đã tìm thấy thông tin lô ${code}`);
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMsg');
            toastMsg.innerText = msg;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }
    </script>
</body>
</html>
