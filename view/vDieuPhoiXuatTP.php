<?php
/**
 * VIEW: vDieuPhoiXuatTP.php
 * Chức năng: Điều phối xuất thành phẩm (FEFO)
 * Phụ trách: Thành viên nhóm (Giao diện đồng bộ Master chuẩn ERP)
 */
?>
<div class="max-w-7xl mx-auto space-y-5 pb-0">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Điều phối xuất thành phẩm</h1>
        <p class="text-sm text-slate-500 mt-1">Phân bổ lô thành phẩm xuất kho theo hạn sử dụng gần nhất đáp ứng đơn đặt hàng (Thuật toán FEFO)</p>
    </div>

    <!-- Danh sách phiếu yêu cầu xuất thành phẩm (Tìm kiếm & Phân trang) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH PHIẾU YÊU CẦU XUẤT THÀNH PHẨM</h2>
            </div>
            
            <!-- Thanh tìm kiếm phiếu yêu cầu -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" placeholder="Tìm mã PYC, đơn hàng, khách hàng..." 
                    class="w-full pl-8 pr-7 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3.5 px-6">Mã phiếu yêu cầu</th>
                        <th class="py-3.5 px-4">Mã đơn hàng</th>
                        <th class="py-3.5 px-4">Khách hàng</th>
                        <th class="py-3.5 px-4">Tên thành phẩm</th>
                        <th class="py-3.5 px-4 text-right">Số lượng cần xuất</th>
                        <th class="py-3.5 px-6">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="cursor-pointer bg-slate-100 font-medium">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">PYC-XTP-2610-01</td>
                        <td class="py-3.5 px-4">DH20261022001</td>
                        <td class="py-3.5 px-4 text-slate-800">Siêu thị Co.opmart Lý Thường Kiệt</td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Bánh quy bơ Danisa Gold 454g</td>
                        <td class="py-3.5 px-4 text-right font-semibold">1,000 Hộp</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>Chờ điều phối</span>
                        </td>
                    </tr>
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">PYC-XTP-2610-02</td>
                        <td class="py-3.5 px-4">DH20261023004</td>
                        <td class="py-3.5 px-4 text-slate-800">Đại lý Phân phối Miền Tây</td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Bánh cracker mè Cosy 250g</td>
                        <td class="py-3.5 px-4 text-right font-semibold">1,500 Hộp</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>Đã điều phối</span>
                        </td>
                    </tr>
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">PYC-XTP-2610-03</td>
                        <td class="py-3.5 px-4">DH20261024002</td>
                        <td class="py-3.5 px-4 text-slate-800">Bách Hóa Xanh Khu Vực 7</td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Bánh cookies chocochip AFC 200g</td>
                        <td class="py-3.5 px-4 text-right font-semibold">800 Hộp</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>Chờ điều phối</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Phân trang danh sách phiếu yêu cầu -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
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
        </div>
    </div>

    <!-- Bảng phân bổ lô thành phẩm xuất theo FEFO -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-truck-fast text-amber-600 text-lg"></i>
                    <h3 class="text-base font-bold text-slate-900">Chi tiết phân bổ lô thành phẩm xuất kho (Thuật toán FEFO)</h3>
                </div>
                <p class="text-sm text-slate-500 mt-1">Khách hàng: <strong class="text-slate-800">Siêu thị Co.opmart Lý Thường Kiệt</strong> — Mặt hàng: <strong class="text-slate-800">Bánh quy bơ Danisa Gold 454g</strong></p>
            </div>
            <!-- Khung Tổng tồn & Cần xuất: 1 khung viền duy nhất, không có background, không có ô tròn ở tổng tồn -->
            <div class="inline-flex items-center gap-3 px-3.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs text-slate-700">
                <span>Tổng tồn khả dụng: <strong class="text-slate-900">3,500 Hộp</strong></span>
                <span class="w-px h-3.5 bg-slate-200"></span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Cần xuất: <strong class="text-slate-900">1,000 Hộp</strong></span>
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3.5 px-6">Thứ tự ưu tiên (FEFO)</th>
                        <th class="py-3.5 px-4">Mã lô TP</th>
                        <th class="py-3.5 px-4">Hạn sử dụng</th>
                        <th class="py-3.5 px-4">Vị trí lưu trữ</th>
                        <th class="py-3.5 px-4 text-right">Tồn khả dụng</th>
                        <th class="py-3.5 px-6 text-right">Số lượng phân bổ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-3.5 px-6 text-slate-800">
                            <span class="font-bold text-slate-900 mr-1.5">1.</span>
                            <span>Ưu tiên 1 (HSD gần nhất)</span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-900">LOTP20260905001</td>
                        <td class="py-3.5 px-4 text-slate-800">05/09/2027 <span class="text-slate-500 text-xs ml-1">(Gần hạn nhất)</span></td>
                        <td class="py-3.5 px-4 text-slate-700">KHO-TP01 / Dãy B • Kệ 01 • Tầng 1</td>
                        <td class="py-3.5 px-4 text-right font-medium">600 Hộp</td>
                        <td class="py-3.5 px-6 text-right font-bold text-slate-900">600 Hộp</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-6 text-slate-800">
                            <span class="font-bold text-slate-900 mr-1.5">2.</span>
                            <span>Ưu tiên 2</span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-900">LOTP20261012002</td>
                        <td class="py-3.5 px-4 text-slate-800">12/10/2027 <span class="text-slate-500 text-xs ml-1">(Sau 37 ngày)</span></td>
                        <td class="py-3.5 px-4 text-slate-700">KHO-TP01 / Dãy B • Kệ 03 • Tầng 2</td>
                        <td class="py-3.5 px-4 text-right font-medium">2,900 Hộp</td>
                        <td class="py-3.5 px-6 text-right font-bold text-slate-900">400 Hộp</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between flex-wrap gap-3">
            <!-- Đã đủ 100%: tick xanh, chữ đen, không viền, không nền màu -->
            <div class="flex items-center gap-2 text-xs">
                <span class="text-slate-500">Trạng thái phân bổ:</span>
                <span class="inline-flex items-center gap-1.5 font-medium text-slate-900">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    <span>Đã đủ 1,000 / 1,000 Hộp (100%)</span>
                </span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>In bảng phân bổ</span>
                </button>
                <button type="button" class="px-5 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap bg-amber-600 hover:bg-amber-700 text-white shadow-sm cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Xác nhận điều phối xuất kho</span>
                </button>
            </div>
        </div>
    </div>
</div>
