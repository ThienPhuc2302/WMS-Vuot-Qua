<?php
/**
 * VIEW: vPhieuXuatNL.php
 * Use Case: Lập phiếu xuất nguyên liệu
 * Phụ trách: Trần Văn Sang (Giao diện đồng bộ Master chuẩn ERP)
 */
$isDaHoanTat = (isset($chiTiet['trangThai']) && ($chiTiet['trangThai'] === 'Đã xuất kho' || $chiTiet['trangThai'] === 'Đã hoàn tất'));
?>
<div class="max-w-7xl mx-auto space-y-5 pb-0">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Lập phiếu xuất nguyên liệu</h1>
        <p class="text-sm text-slate-500 mt-1">Thực hiện xuất kho nguyên liệu phục vụ sản xuất theo lệnh điều phối</p>
    </div>

    <?php if (isset($thongBao)): ?>
        <div class="p-4 rounded-lg text-sm font-semibold <?= $thongBao['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
            <?= htmlspecialchars($thongBao['msg']) ?>
        </div>
    <?php endif; ?>

    <!-- Banner Thông báo Thông minh khi chọn Lệnh đã hoàn tất xuất kho -->
    <?php if ($isDaHoanTat): ?>
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                <div>
                    <strong class="font-bold text-sm block">Lệnh điều phối [<?= htmlspecialchars($chiTiet['maLenh']) ?>] đã xuất kho thành công!</strong>
                    <span class="text-xs text-emerald-700">Dữ liệu đã được cập nhật vào thẻ kho và giải phóng vị trí kệ. Biểu mẫu đang ở chế độ xem (Read-only).</span>
                </div>
            </div>
            <span class="px-3 py-1 bg-emerald-600 text-white font-bold text-xs rounded-full">Chế độ Chỉ Xem</span>
        </div>
    <?php endif; ?>

    <!-- Danh sách lệnh điều phối xuất nguyên liệu -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-boxes-packing text-rose-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH LỆNH ĐIỀU PHỐI XUẤT NGUYÊN LIỆU</h2>
            </div>
            
            <form method="GET" class="relative w-full sm:w-72">
                <input type="hidden" name="page" value="vPhieuXuatNL">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Tìm mã lệnh, bộ phận nhận..." 
                    class="w-full pl-8 pr-7 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3.5 px-6">Mã lệnh ĐP Xuất</th>
                        <th class="py-3.5 px-4">Mã phiếu yêu cầu</th>
                        <th class="py-3.5 px-4">Bộ phận nhận</th>
                        <th class="py-3.5 px-4">Kho xuất</th>
                        <th class="py-3.5 px-4 text-right">Tổng số lượng</th>
                        <th class="py-3.5 px-6">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($dsDieuPhoiXuat)): ?>
                        <?php foreach ($dsDieuPhoiXuat as $dp): 
                            $isSelected = (isset($chiTiet['maLenh']) && $chiTiet['maLenh'] == $dp['maLenh']);
                        ?>
                            <tr onclick="window.location.href='index.php?page=vPhieuXuatNL&maLenh=<?= $dp['maLenh'] ?>'" 
                                class="cursor-pointer <?= $isSelected ? 'bg-rose-50/70 font-medium' : 'hover:bg-slate-50' ?>">
                                <td class="py-3.5 px-6 font-semibold text-slate-900"><?= htmlspecialchars($dp['maLenh']) ?></td>
                                <td class="py-3.5 px-4"><?= htmlspecialchars($dp['maPhieuYeuCau']) ?></td>
                                <td class="py-3.5 px-4"><?= htmlspecialchars($dp['donViYeuCau']) ?></td>
                                <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($dp['tenKho'] ?? 'Kho Nguyên Liệu Khô 1') ?></td>
                                <td class="py-3.5 px-4 text-right font-semibold"><?= number_format($dp['tongSoLuong'], 0) ?> Kg</td>
                                <td class="py-3.5 px-6">
                                    <?php if ($dp['trangThai'] === 'Đã xuất kho' || $dp['trangThai'] === 'Đã hoàn tất'): ?>
                                        <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>Đã xuất kho</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span><?= htmlspecialchars($dp['trangThai']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-4 px-6 text-center text-slate-500">Chưa tìm thấy lệnh điều phối xuất nguyên liệu nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2 Cột: Chi tiết phiếu xuất & Chi tiết kệ chỉ định xuất FEFO -->
    <form method="POST" action="index.php?page=vPhieuXuatNL" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ($_SESSION['token'] ?? '') ?>">
        <input type="hidden" name="action" value="luuPhieuXuat">
        <input type="hidden" name="maLenh" value="<?= htmlspecialchars($chiTiet['maLenh'] ?? '') ?>">

        <!-- Cột Trái: Chi Tiết Phiếu Xuất -->
        <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 gap-2">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-file-export text-rose-600 text-lg"></i>
                        <h3 class="text-base font-bold text-slate-900 whitespace-nowrap">Chi Tiết Phiếu Xuất Nguyên Liệu</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-xs font-semibold text-slate-700 tracking-wider">
                        PXK-NL-<?= date('ym') ?>-<?= str_pad(substr($chiTiet['maLenh'] ?? '1', -3), 3, '0', STR_PAD_LEFT) ?>
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-x-5 gap-y-4 text-sm pt-3.5">
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lệnh xuất</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap"><?= htmlspecialchars($chiTiet['maLenh'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Bộ phận yêu cầu nhận</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap"><?= htmlspecialchars($chiTiet['donViYeuCau'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Lý do xuất</span>
                        <strong class="text-slate-900 mt-1 block truncate" title="<?= htmlspecialchars($chiTiet['lyDoXuat'] ?? '') ?>"><?= htmlspecialchars($chiTiet['lyDoXuat'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kho xuất hàng</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap"><?= htmlspecialchars($chiTiet['tenKho'] ?? 'Kho Nguyên Liệu Khô 1') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Người lập phiếu</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap"><?= htmlspecialchars($chiTiet['tenNguoiDieuPhoi'] ?? 'Trần Văn Sang') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Quy tắc xuất kho</span>
                        <strong class="text-slate-900 mt-1 block text-emerald-600 font-semibold whitespace-nowrap">FEFO (Hạn dùng trước - Xuất trước)</strong>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Ghi chú xuất kho</label>
                        <input type="text" name="ghiChu" value="<?= htmlspecialchars($chiTiet['ghiChu'] ?? 'Xuất nguyên liệu cho ca sản xuất bánh quy Danisa.') ?>" <?= $isDaHoanTat ? 'disabled' : '' ?>
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg <?= $isDaHoanTat ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-800' ?> text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Phải: Danh sách vị trí kệ chỉ định xuất theo thuật toán FEFO -->
        <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div class="flex-1 flex flex-col min-h-0">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-rose-600 text-base"></i>
                        <h3 class="text-base font-bold text-slate-900">Chi tiết vị trí kệ lấy hàng (FEFO)</h3>
                    </div>
                </div>

                <div class="space-y-2 py-3 flex-1 overflow-y-auto max-h-[320px]">
                    <?php if (!empty($dsChiTietKiet)): ?>
                        <?php foreach ($dsChiTietKiet as $ct): ?>
                            <div class="px-3.5 py-2.5 rounded-lg border border-slate-200 bg-rose-50/30 flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-bold text-slate-900"><?= htmlspecialchars($ct['tenNL']) ?></div>
                                    <div class="text-xs text-slate-500 mt-0.5">Lô: <strong class="text-slate-800"><?= htmlspecialchars($ct['maLo']) ?></strong> | Kệ: <strong class="text-slate-900"><?= htmlspecialchars($ct['maViTri']) ?></strong> (<?= htmlspecialchars($ct['day']) ?>-<?= htmlspecialchars($ct['ke']) ?>-<?= htmlspecialchars($ct['tang']) ?>)</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">HSD: <?= date('d/m/Y', strtotime($ct['hanSuDung'])) ?></div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xs text-slate-500 block">Số lượng xuất</span>
                                    <strong class="text-rose-600 font-bold text-sm"><?= number_format($ct['soLuongPhanBo'], 0) ?> <?= htmlspecialchars($ct['donViTinh']) ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-xs text-slate-500 border border-dashed rounded-lg">Vui lòng chọn lệnh điều phối xuất để xem chi tiết kệ xuất.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Nút thao tác -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3 mt-2">
                <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>In phiếu</span>
                </button>

                <!-- 👉 ĐỔI NÚT THÀNH TRẠNG THÁI VÔ HIỆU HÓA KHI ĐÃ HOÀN TẤT XUẤT KHO -->
                <?php if ($isDaHoanTat): ?>
                    <button type="button" disabled class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap bg-slate-300 text-slate-600 cursor-not-allowed shadow-none">
                        <i class="fa-solid fa-lock"></i>
                        <span>Đã hoàn tất xuất kho</span>
                    </button>
                <?php else: ?>
                    <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap bg-rose-600 hover:bg-rose-700 text-white shadow-sm cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span>Lưu phiếu xuất kho</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>