<?php
/**
 * VIEW: vPhieuNhapNL.php
 * Use Case: UC 3.2 - Lập phiếu nhập nguyên liệu
 * Phụ trách: Thành viên nhóm (Giao diện đồng bộ Master chuẩn ERP)
 */
?>
<div class="max-w-7xl mx-auto space-y-5 pb-0">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Lập phiếu nhập nguyên liệu</h1>
        <p class="text-sm text-slate-500 mt-1">Tiếp nhận lệnh điều phối nguyên liệu từ nhà cung cấp và chỉ định vị trí kệ lưu trữ</p>
    </div>

    <!-- Danh sách lệnh điều phối nhập nguyên liệu (Tìm kiếm & Phân trang chuẩn ERP) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH LỆNH ĐIỀU PHỐI TIẾP NHẬN NGUYÊN LIỆU</h2>
            </div>
            
            <!-- Thanh tìm kiếm lệnh điều phối -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" placeholder="Tìm mã lệnh, mã lô, nguyên liệu..." 
                    class="w-full pl-8 pr-7 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3.5 px-6">Mã lệnh ĐP</th>
                        <th class="py-3.5 px-4">Mã lô</th>
                        <th class="py-3.5 px-4">Tên nguyên liệu</th>
                        <th class="py-3.5 px-4">Kho chỉ định</th>
                        <th class="py-3.5 px-4 text-right">Số lượng yêu cầu</th>
                        <th class="py-3.5 px-6">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="cursor-pointer bg-slate-100 font-medium">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">LDP-NL-2610-01</td>
                        <td class="py-3.5 px-4">LONL20261021001</td>
                        <td class="py-3.5 px-4">Bột mì đa dụng thượng hạng số 8</td>
                        <td class="py-3.5 px-4 text-slate-600">Kho Nguyên Liệu Khô 1</td>
                        <td class="py-3.5 px-4 text-right font-semibold">5,000 Kg</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>Chờ nhập kho</span>
                        </td>
                    </tr>
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">LDP-NL-2610-02</td>
                        <td class="py-3.5 px-4">LONL20261021002</td>
                        <td class="py-3.5 px-4">Bơ lạt Anchor New Zealand</td>
                        <td class="py-3.5 px-4 text-slate-600">Kho Nguyên Liệu Lạnh 2</td>
                        <td class="py-3.5 px-4 text-right font-semibold">2,000 Kg</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>Chờ nhập kho</span>
                        </td>
                    </tr>
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">LDP-NL-2610-03</td>
                        <td class="py-3.5 px-4">LONL20261020001</td>
                        <td class="py-3.5 px-4">Đường tinh luyện Biên Hòa Pure</td>
                        <td class="py-3.5 px-4 text-slate-600">Kho Nguyên Liệu Khô 1</td>
                        <td class="py-3.5 px-4 text-right font-semibold">3,500 Kg</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>Hoàn tất nhập kho</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Phân trang bảng lệnh điều phối nguyên liệu -->
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

    <!-- 2 Cột: Chi tiết phiếu nhập (7 cột) & Chọn vị trí kệ (5 cột) - 2 bảng nhìn ngang bằng nhau chuẩn xác -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Cột Trái: Chi Tiết Phiếu Nhập Nguyên Liệu -->
        <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 gap-2">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-file-invoice text-amber-600 text-lg"></i>
                        <h3 class="text-base font-bold text-slate-900 whitespace-nowrap">Chi Tiết Phiếu Nhập Nguyên Liệu</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-xs font-semibold text-slate-700 tracking-wider">PNK-NL-2610-001</span>
                </div>
                
                <!-- Tất cả các trường thông tin: bố cục thanh lịch, dài xuống vừa phải -->
                <div class="grid grid-cols-2 gap-x-5 gap-y-4 text-sm pt-3.5">
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lệnh điều phối</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">LDP-NL-2610-01</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lô nguyên liệu</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">LONL20261021001</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Tên nguyên liệu</span>
                        <strong class="text-slate-900 mt-1 block truncate" title="Bột mì đa dụng thượng hạng số 8">Bột mì đa dụng thượng hạng số 8</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Nhà cung cấp</span>
                        <strong class="text-slate-900 mt-1 block truncate" title="Công ty CP Xay Lúa Mì Việt Nam">Công ty CP Xay Lúa Mì Việt Nam</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kho tiếp nhận</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">Kho Nguyên Liệu Khô 1</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Hạn sử dụng</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">20/10/2026 – 20/10/2027</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Người điều phối</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">Trần Văn Sang</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kiểm định chất lượng</span>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>Đạt</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1.5 text-xs whitespace-nowrap">Số lượng yêu cầu</label>
                        <input type="text" disabled value="5,000 Kg" 
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold text-sm">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Số lượng thực nhập (Kg) (*)</label>
                        <input type="number" value="5000" 
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white text-slate-900 font-bold text-sm">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Ghi chú nhập kho</label>
                        <input type="text" value="Hàng nguyên tem niêm phong, kiểm định đạt chuẩn 100%." 
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white text-slate-800 text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Phải: Chọn vị trí lưu trữ còn trống -->
        <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div class="flex-1 flex flex-col min-h-0">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-table-cells text-amber-600 text-base"></i>
                        <h3 class="text-base font-bold text-slate-900">Chọn vị trí lưu trữ còn trống</h3>
                    </div>
                </div>

                <!-- Thanh Selection: Chọn Dãy • Kệ • Tầng • Nhấn nút Tìm kiếm -->
                <div class="py-2.5 border-b border-slate-100 flex items-center gap-1.5">
                    <div class="grid grid-cols-3 gap-1.5 flex-1">
                        <select class="py-1 px-1.5 bg-slate-50 border border-slate-300 rounded text-slate-700 text-xs focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <option value="">Tất cả dãy</option>
                            <option value="A" selected>Dãy A</option>
                            <option value="B">Dãy B</option>
                        </select>
                        <select class="py-1 px-1.5 bg-slate-50 border border-slate-300 rounded text-slate-700 text-xs focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <option value="">Tất cả kệ</option>
                            <option value="01">Kệ 01</option>
                            <option value="02" selected>Kệ 02</option>
                            <option value="03">Kệ 03</option>
                        </select>
                        <select class="py-1 px-1.5 bg-slate-50 border border-slate-300 rounded text-slate-700 text-xs focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <option value="">Tất cả tầng</option>
                            <option value="1" selected>Tầng 1</option>
                            <option value="2">Tầng 2</option>
                        </select>
                    </div>
                    <button type="button" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs font-semibold flex items-center gap-1 shadow-sm cursor-default whitespace-nowrap">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                        <span>Tìm</span>
                    </button>
                </div>

                <!-- Danh sách kệ phân trang (4 vị trí / trang) -->
                <div class="space-y-2 py-2 flex-1">
                    <div class="px-3.5 py-2 rounded-lg border border-brand-600 bg-amber-50/50 shadow-sm flex items-center justify-between gap-3 cursor-pointer">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm"><span class="font-bold text-slate-900">Dãy A • Kệ 02 • Tầng 1</span></div>
                            <div class="text-xs text-slate-500 mt-0.5 whitespace-nowrap">Sức chứa khả dụng: 2,000 Kg</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-sky-500 shrink-0"></span>Trống</span>
                            <i class="fa-solid fa-circle-check text-brand-600 text-lg"></i>
                        </div>
                    </div>

                    <div class="px-3.5 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 flex items-center justify-between gap-3 cursor-pointer">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm"><span class="font-bold text-slate-900">Dãy A • Kệ 03 • Tầng 1</span></div>
                            <div class="text-xs text-slate-500 mt-0.5 whitespace-nowrap">Sức chứa khả dụng: 2,000 Kg</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-sky-500 shrink-0"></span>Trống</span>
                            <i class="fa-regular fa-circle text-slate-300 text-lg"></i>
                        </div>
                    </div>

                    <div class="px-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50/70 opacity-60 flex items-center justify-between gap-3 cursor-not-allowed">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm"><span class="font-bold text-slate-900">Dãy A • Kệ 01 • Tầng 1</span></div>
                            <div class="text-xs text-slate-500 mt-0.5 whitespace-nowrap">Sức chứa khả dụng: 2,500 Kg</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>Đang chứa</span>
                            <i class="fa-regular fa-circle text-slate-300 text-lg"></i>
                        </div>
                    </div>

                    <div class="px-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50/70 opacity-60 flex items-center justify-between gap-3 cursor-not-allowed">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm"><span class="font-bold text-slate-900">Dãy B • Kệ 01 • Tầng 2</span></div>
                            <div class="text-xs text-slate-500 mt-0.5 whitespace-nowrap">Sức chứa khả dụng: 0 Kg</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-slate-500 shrink-0"></span>Đầy</span>
                            <i class="fa-regular fa-circle text-slate-300 text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Thanh phân trang vị trí kệ -->
                <div class="pt-2 pb-1 border-t border-slate-100 flex items-center justify-end text-xs text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <button type="button" disabled class="px-2 py-1 rounded border border-slate-200 text-slate-300 bg-white cursor-not-allowed text-[11px] font-medium">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button type="button" class="w-6 h-6 rounded text-[11px] font-semibold flex items-center justify-center bg-amber-600 text-white shadow-sm cursor-default">
                            1
                        </button>
                        <button type="button" class="w-6 h-6 rounded text-[11px] font-semibold flex items-center justify-center border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 cursor-default">
                            2
                        </button>
                        <button type="button" class="px-2 py-1 rounded border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-[11px] font-medium cursor-default">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2 Nút thao tác ở đáy: Kéo xích lên ngang bằng đáy bảng bên trái -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3 mt-2">
                <button type="button" class="px-4 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>In phiếu</span>
                </button>
                <button type="button" class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap bg-amber-600 hover:bg-amber-700 text-white shadow-sm cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Lưu phiếu nhập kho</span>
                </button>
            </div>
        </div>
    </div>
</div>
