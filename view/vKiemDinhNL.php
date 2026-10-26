<?php
/**
 * VIEW: vKiemDinhNL.php
 * Chức năng: Lập biên bản kiểm định nguyên liệu
 * Phụ trách: Thành viên nhóm (Giao diện đồng bộ Master chuẩn ERP)
 */
?>
<div class="max-w-7xl mx-auto space-y-5 pb-0">
    <!-- Tiêu đề trang -->
    <div>
        <h1 class="text-xl font-bold text-slate-900">Lập biên bản kiểm định nguyên liệu</h1>
        <p class="text-sm text-slate-500 mt-1">Kiểm tra chất lượng lô nguyên liệu đầu vào theo bộ tiêu chí trước khi điều phối nhập kho</p>
    </div>

    <!-- Phần 1: Bảng danh sách lô nguyên liệu (Tìm kiếm & Phân trang chuẩn ERP) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH LÔ NGUYÊN LIỆU</h2>
            </div>
            
            <!-- Thanh tìm kiếm lô nguyên liệu -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" placeholder="Tìm theo mã lô, tên nguyên liệu..." 
                    class="w-full pl-8 pr-7 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3.5 px-6">Mã lô</th>
                        <th class="py-3.5 px-4">Tên nguyên liệu</th>
                        <th class="py-3.5 px-4">Nhà cung cấp</th>
                        <th class="py-3.5 px-4 text-right">Số lượng</th>
                        <th class="py-3.5 px-4">Ngày SX</th>
                        <th class="py-3.5 px-4">Hạn sử dụng</th>
                        <th class="py-3.5 px-6">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="cursor-pointer bg-slate-100 font-medium">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-4 rounded-sm bg-brand-600"></span>
                                <span>LONL20261021001</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Bột mì đa dụng thượng hạng số 8</td>
                        <td class="py-3.5 px-4 text-slate-600">Công ty CP Xay Lúa Mì Việt Nam</td>
                        <td class="py-3.5 px-4 text-right font-semibold">5,000 Kg</td>
                        <td class="py-3.5 px-4 text-slate-600">20/10/2026</td>
                        <td class="py-3.5 px-4 text-slate-600">20/10/2027</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>Đạt</span>
                        </td>
                    </tr>
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-4 rounded-sm bg-transparent"></span>
                                <span>LONL20261021002</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Bơ lạt Anchor New Zealand</td>
                        <td class="py-3.5 px-4 text-slate-600">Fonterra Brands Vietnam</td>
                        <td class="py-3.5 px-4 text-right font-semibold">2,000 Kg</td>
                        <td class="py-3.5 px-4 text-slate-600">15/10/2026</td>
                        <td class="py-3.5 px-4 text-slate-600">15/10/2027</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>Chờ kiểm định</span>
                        </td>
                    </tr>
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-4 rounded-sm bg-transparent"></span>
                                <span>LONL20261022001</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Đường tinh luyện Biên Hòa Pure</td>
                        <td class="py-3.5 px-4 text-slate-600">Công ty Đường Biên Hòa</td>
                        <td class="py-3.5 px-4 text-right font-semibold">3,500 Kg</td>
                        <td class="py-3.5 px-4 text-slate-600">22/10/2026</td>
                        <td class="py-3.5 px-4 text-slate-600">22/10/2027</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>Chờ kiểm định</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Phân trang danh sách lô nguyên liệu -->
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

    <!-- Phần 2: 2 Khối thông tin song song (Thông tin lô 7 cột & Kết luận tổng thể 5 cột) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 gap-2">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-file-invoice text-amber-600 text-lg"></i>
                        <h3 class="text-base font-bold text-slate-900 whitespace-nowrap">Thông tin lô nguyên liệu</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-xs font-semibold text-slate-700 tracking-wider">PNK-NL-2610-001</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-5 gap-y-4 text-sm pt-3.5">
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lô</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">LONL20261021001</strong>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Tên nguyên liệu</span>
                        <strong class="text-slate-900 mt-1 block truncate" title="Bột mì đa dụng thượng hạng số 8">Bột mì đa dụng thượng hạng số 8</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Số lượng kiểm định</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap font-semibold">5,000 Kg</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Ngày sản xuất</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">20/10/2026</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Hạn sử dụng</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">20/10/2027</strong>
                    </div>
                    <div class="sm:col-span-3">
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Nhà cung cấp</span>
                        <strong class="text-slate-900 mt-1 block">Công ty CP Xay Lúa Mì Việt Nam</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 gap-2">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-clipboard-check text-amber-600 text-lg"></i>
                        <h3 class="text-base font-bold text-slate-900 whitespace-nowrap">Kết luận kiểm định</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-xs font-semibold text-slate-700 tracking-wider">BBKD-2610-001</span>
                </div>
                <div class="space-y-4 pt-3.5 text-sm">
                    <div>
                        <label class="block font-medium text-slate-700 mb-1.5 text-xs">Kết quả kiểm định tổng thể (*)</label>
                        <select class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white font-semibold text-slate-900 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <option value="Đạt" selected>Đạt chuẩn nhập kho</option>
                            <option value="Không đạt">Không đạt chuẩn (Từ chối nhập)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-1.5 text-xs">Ghi chú biên bản</label>
                        <input type="text" value="Đạt tất cả tiêu chí chất lượng đầu vào, bao bì nguyên vẹn." 
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white text-slate-900 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phần 3: Bảng bộ tiêu chí kiểm định (Full-width) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-vial-circle-check text-amber-600 text-lg"></i>
                <h3 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH TIÊU CHÍ KIỂM ĐỊNH CHI TIẾT</h3>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3.5 px-6">Mã tiêu chí</th>
                        <th class="py-3.5 px-4">Tên tiêu chí kiểm định</th>
                        <th class="py-3.5 px-4">Quy chuẩn cho phép</th>
                        <th class="py-3.5 px-4">Kết quả đo thực tế</th>
                        <th class="py-3.5 px-4">Đánh giá</th>
                        <th class="py-3.5 px-6">Ghi chú kiểm nghiệm</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-3.5 px-6 font-semibold text-slate-700">TC-01</td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Độ ẩm nguyên liệu</td>
                        <td class="py-3.5 px-4 text-slate-600">≤ 14.0%</td>
                        <td class="py-3.5 px-4">
                            <input type="text" value="12.5%" class="w-32 px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-900 text-sm font-semibold">
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>Đạt</span>
                        </td>
                        <td class="py-3.5 px-6">
                            <input type="text" value="Đạt chuẩn kho bảo quản" class="w-full min-w-[220px] px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-800 text-sm">
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-6 font-semibold text-slate-700">TC-02</td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Hàm lượng Protein</td>
                        <td class="py-3.5 px-4 text-slate-600">≥ 9.5%</td>
                        <td class="py-3.5 px-4">
                            <input type="text" value="10.8%" class="w-32 px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-900 text-sm font-semibold">
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>Đạt</span>
                        </td>
                        <td class="py-3.5 px-6">
                            <input type="text" value="Đạt tiêu chuẩn kỹ thuật bánh quy" class="w-full min-w-[220px] px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-800 text-sm">
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-6 font-semibold text-slate-700">TC-03</td>
                        <td class="py-3.5 px-4 font-medium text-slate-900">Tạp chất vô cơ</td>
                        <td class="py-3.5 px-4 text-slate-600">0% (Không phát hiện)</td>
                        <td class="py-3.5 px-4">
                            <input type="text" value="0%" class="w-32 px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-900 text-sm font-semibold">
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>Đạt</span>
                        </td>
                        <td class="py-3.5 px-6">
                            <input type="text" value="Không có mọt, sạn" class="w-full min-w-[220px] px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-800 text-sm">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3">
            <button type="button" class="px-4 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium flex items-center gap-2 whitespace-nowrap cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>In biên bản</span>
            </button>
            <button type="button" class="px-5 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap bg-amber-600 hover:bg-amber-700 text-white shadow-sm cursor-pointer">
                <i class="fa-solid fa-check"></i>
                <span>Lưu biên bản kiểm định</span>
            </button>
        </div>
    </div>
</div>
