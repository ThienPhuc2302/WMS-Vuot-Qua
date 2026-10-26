<?php
/**
 * VIEW: vBienBanKiemKe.php
 * Chức năng: Lập biên bản kiểm kê
 * Phụ trách: Thành viên nhóm (Giao diện đồng bộ Master chuẩn ERP)
 */
?>
<div class="max-w-7xl mx-auto space-y-5 pb-0">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Lập biên bản kiểm kê</h1>
            <p class="text-sm text-slate-500 mt-1">Ghi nhận số lượng kiểm đếm thực tế tại kho và đối chiếu chênh lệch sổ sách</p>
        </div>

        <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-200 shadow-sm text-xs">
            <button type="button" class="px-3.5 py-1.5 rounded-lg font-semibold whitespace-nowrap bg-amber-600 text-white shadow-sm cursor-default">
                Kiểm kê nguyên liệu
            </button>
            <button type="button" class="px-3.5 py-1.5 rounded-lg font-medium whitespace-nowrap text-slate-600 hover:bg-slate-100 cursor-default">
                Kiểm kê thành phẩm
            </button>
        </div>
    </div>

    <!-- Thông tin đợt kiểm kê -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-slate-500 uppercase font-semibold">Mã đợt kiểm kê</span>
                <i class="fa-solid fa-clipboard-list text-amber-600 text-base"></i>
            </div>
            <strong class="text-base text-slate-900 block whitespace-nowrap">DOT-KK-NL-102026</strong>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-slate-500 uppercase font-semibold">Kho thực hiện</span>
                <i class="fa-solid fa-warehouse text-amber-600 text-base"></i>
            </div>
            <strong class="text-base text-slate-900 block truncate" title="Kho Nguyên Liệu Khô 1 (KHO-NL01)">Kho Nguyên Liệu Khô 1 (KHO-NL01)</strong>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-slate-500 uppercase font-semibold">Trạng thái biên bản</span>
                <i class="fa-solid fa-circle-info text-amber-600 text-base"></i>
            </div>
            <div class="mt-1">
                <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>Đang kiểm đếm
                </span>
            </div>
        </div>
    </div>

    <!-- Bảng kiểm kê chi tiết (Tìm kiếm & Phân trang) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH MẶT HÀNG KIỂM KÊ CHI TIẾT</h2>
            </div>
            
            <!-- Thanh tìm kiếm mặt hàng kiểm kê -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" placeholder="Tìm mã lô, mặt hàng, vị trí..." 
                    class="w-full pl-8 pr-7 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3.5 px-6">Mã lô</th>
                        <th class="py-3.5 px-4">Tên hàng hóa</th>
                        <th class="py-3.5 px-4">Vị trí lưu trữ</th>
                        <th class="py-3.5 px-4 text-right">Tồn sổ sách</th>
                        <th class="py-3.5 px-4 text-right">SL tốt thực tế</th>
                        <th class="py-3.5 px-4 text-right">SL hỏng thực tế</th>
                        <th class="py-3.5 px-4 text-right">Tổng thực tế</th>
                        <th class="py-3.5 px-6 text-right">Chênh lệch</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="cursor-pointer bg-slate-100 font-medium">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">LO-BM-2610-01</td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Bột mì đa dụng thượng hạng</td>
                        <td class="py-3.5 px-4 text-slate-600">KHO-NL01 / Dãy A • Kệ 02 • Tầng 1</td>
                        <td class="py-3.5 px-4 text-right font-semibold">1,150 Kg</td>
                        <td class="py-3.5 px-4 text-right">
                            <input type="number" value="1150" class="w-24 text-right px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white font-semibold text-slate-900 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <input type="number" value="0" class="w-20 text-right px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-800 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">1,150 Kg</td>
                        <td class="py-3.5 px-6 text-right">
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>0 (Khớp)</span>
                        </td>
                    </tr>
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">LO-DG-2610-03</td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Đường tinh luyện nhuyễn</td>
                        <td class="py-3.5 px-4 text-slate-600">KHO-NL01 / Dãy A • Kệ 04 • Tầng 1</td>
                        <td class="py-3.5 px-4 text-right font-semibold">1,200 Kg</td>
                        <td class="py-3.5 px-4 text-right">
                            <input type="number" value="1200" class="w-24 text-right px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white font-semibold text-slate-900 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <input type="number" value="0" class="w-20 text-right px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-800 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">1,200 Kg</td>
                        <td class="py-3.5 px-6 text-right">
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>0 (Khớp)</span>
                        </td>
                    </tr>
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">LO-BL-2610-02</td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Bơ lạt Anchor khối 25kg</td>
                        <td class="py-3.5 px-4 text-slate-600">KHO-NL01 / Dãy B • Kệ 01 • Tầng 2</td>
                        <td class="py-3.5 px-4 text-right font-semibold">500 Kg</td>
                        <td class="py-3.5 px-4 text-right">
                            <input type="number" value="490" class="w-24 text-right px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white font-semibold text-slate-900 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </td>
                        <!-- Ô nhập số lượng hỏng: ô màu trắng bình thường (bỏ nền hồng, bỏ viền đỏ), chỉ có con số mang màu đỏ -->
                        <td class="py-3.5 px-4 text-right">
                            <input type="number" value="10" class="w-20 text-right px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white text-rose-600 font-bold text-xs focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">500 Kg</td>
                        <td class="py-3.5 px-6 text-right">
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>-10 (Hao hụt)</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer: Phân trang bên trái & Nút thao tác bên phải -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <!-- Phân trang danh sách kiểm kê -->
            <div class="flex items-center gap-1.5">
                <button type="button" disabled class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 bg-white cursor-not-allowed text-xs font-medium">
                    <i class="fa-solid fa-chevron-left mr-1"></i>Trước
                </button>
                <button type="button" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center bg-amber-600 text-white shadow-sm cursor-default">
                    1
                </button>
                <button type="button" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 cursor-default">
                    2
                </button>
                <button type="button" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-xs font-medium cursor-default">
                    Sau<i class="fa-solid fa-chevron-right ml-1"></i>
                </button>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>In biên bản</span>
                </button>
                <button type="button" class="px-5 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap bg-amber-600 hover:bg-amber-700 text-white shadow-sm cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Hoàn tất kiểm kê</span>
                </button>
            </div>
        </div>
    </div>
</div>
