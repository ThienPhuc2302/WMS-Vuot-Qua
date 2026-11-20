<?php
/**
 * VIEW: vPhieuNhapNL.php
 * Use Case: UC 3.2 - Lập phiếu nhập nguyên liệu
 * Phụ trách: Trần Văn Sang
 */
$isDaHoanTat = (isset($chiTiet['trangThai']) && $chiTiet['trangThai'] === 'Hoàn tất nhập kho');
?>
<div class="max-w-7xl mx-auto space-y-5 pb-0">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Lập phiếu nhập nguyên liệu</h1>
        <p class="text-sm text-slate-500 mt-1">Tiếp nhận lệnh điều phối nguyên liệu từ nhà cung cấp và chỉ định vị trí kệ lưu trữ</p>
    </div>

    <?php if (isset($thongBao)): ?>
        <div class="p-4 rounded-lg text-sm font-semibold <?= $thongBao['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
            <?= htmlspecialchars($thongBao['msg']) ?>
        </div>
    <?php endif; ?>

    <!-- Banner Thông báo Thông minh khi chọn Lệnh đã hoàn tất -->
    <?php if ($isDaHoanTat): ?>
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                <div>
                    <strong class="font-bold text-sm block">Lệnh điều phối [<?= htmlspecialchars($chiTiet['maLenhDieuPhoi']) ?>] đã hoàn tất nhập kho!</strong>
                    <span class="text-xs text-emerald-700">Dữ liệu lô hàng đã được cập nhật vào thẻ kho và vị trí lưu trữ. Biểu mẫu đang ở chế độ xem (Read-only).</span>
                </div>
            </div>
            <span class="px-3 py-1 bg-emerald-600 text-white font-bold text-xs rounded-full">Chế độ Chỉ Xem</span>
        </div>
    <?php endif; ?>

    <!-- Danh sách lệnh điều phối nhập nguyên liệu -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH LỆNH ĐIỀU PHỐI TIẾP NHẬN NGUYÊN LIỆU</h2>
            </div>
            
            <form method="GET" class="relative w-full sm:w-72">
                <input type="hidden" name="page" value="vPhieuNhapNL">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Tìm mã lệnh, mã lô, nguyên liệu..." 
                    class="w-full pl-8 pr-7 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
            </form>
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
                    <?php if (!empty($dsDieuPhoi)): ?>
                        <?php foreach ($dsDieuPhoi as $dp): 
                            $isSelected = (isset($chiTiet['maLenhDieuPhoi']) && $chiTiet['maLenhDieuPhoi'] == $dp['maLenhDieuPhoi']);
                        ?>
                            <tr onclick="window.location.href='index.php?page=vPhieuNhapNL&maLenhDieuPhoi=<?= $dp['maLenhDieuPhoi'] ?>'" 
                                class="cursor-pointer <?= $isSelected ? 'bg-amber-50/70 font-medium' : 'hover:bg-slate-50' ?>">
                                <td class="py-3.5 px-6 font-semibold text-slate-900"><?= htmlspecialchars($dp['maLenhDieuPhoi']) ?></td>
                                <td class="py-3.5 px-4"><?= htmlspecialchars($dp['maLo']) ?></td>
                                <td class="py-3.5 px-4"><?= htmlspecialchars($dp['tenNL']) ?></td>
                                <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($dp['tenKho'] ?? 'Kho Nguyên Liệu') ?></td>
                                <td class="py-3.5 px-4 text-right font-semibold"><?= number_format($dp['soLuongPhanBo'], 0) ?> <?= htmlspecialchars($dp['donViTinh'] ?? 'Kg') ?></td>
                                <td class="py-3.5 px-6">
                                    <?php if ($dp['trangThai'] === 'Hoàn tất nhập kho'): ?>
                                        <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>Hoàn tất nhập kho</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span><?= htmlspecialchars($dp['trangThai']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-4 px-6 text-center text-slate-500">Chưa tìm thấy lệnh điều phối nhập nguyên liệu nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2 Cột: Chi tiết phiếu nhập & Chọn vị trí kệ -->
    <form method="POST" action="index.php?page=vPhieuNhapNL" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ($_SESSION['token'] ?? '') ?>">
        <input type="hidden" name="action" value="luuPhieuNhap">
        <input type="hidden" name="maLenhDieuPhoi" value="<?= htmlspecialchars($chiTiet['maLenhDieuPhoi'] ?? '') ?>">
        <input type="hidden" name="soLuongPhanBo" value="<?= htmlspecialchars($chiTiet['soLuongPhanBo'] ?? 0) ?>">

        <!-- Cột Trái: Chi Tiết Phiếu Nhập Nguyên Liệu -->
        <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 gap-2">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-file-invoice text-amber-600 text-lg"></i>
                        <h3 class="text-base font-bold text-slate-900 whitespace-nowrap">Chi Tiết Phiếu Nhập Nguyên Liệu</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-xs font-semibold text-slate-700 tracking-wider">
                        PNK-NL-<?= date('ym') ?>-<?= str_pad(substr($chiTiet['maLenhDieuPhoi'] ?? '1', -3), 3, '0', STR_PAD_LEFT) ?>
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-x-5 gap-y-4 text-sm pt-3.5">
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lệnh điều phối</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap"><?= htmlspecialchars($chiTiet['maLenhDieuPhoi'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lô nguyên liệu</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap"><?= htmlspecialchars($chiTiet['maLo'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Tên nguyên liệu</span>
                        <strong class="text-slate-900 mt-1 block truncate" title="<?= htmlspecialchars($chiTiet['tenNL'] ?? '') ?>"><?= htmlspecialchars($chiTiet['tenNL'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Nhà cung cấp</span>
                        <strong class="text-slate-900 mt-1 block truncate" title="<?= htmlspecialchars($chiTiet['nhaCungCap'] ?? '') ?>"><?= htmlspecialchars($chiTiet['nhaCungCap'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kho tiếp nhận</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap"><?= htmlspecialchars($chiTiet['tenKho'] ?? 'Kho Nguyên Liệu Khô 1') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Hạn sử dụng</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap"><?= isset($chiTiet['hanSuDung']) ? date('d/m/Y', strtotime($chiTiet['hanSuDung'])) : 'N/A' ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Người điều phối</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap"><?= htmlspecialchars($chiTiet['tenNguoiDieuPhoi'] ?? 'Trần Văn Sang') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kiểm định chất lượng</span>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span><?= htmlspecialchars($chiTiet['ketQuaKiemDinh'] ?? 'Đạt') ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1.5 text-xs whitespace-nowrap">Số lượng yêu cầu (Phân bổ)</label>
                        <input type="text" disabled value="<?= number_format($chiTiet['soLuongPhanBo'] ?? 0, 0) ?> <?= htmlspecialchars($chiTiet['donViTinh'] ?? 'Kg') ?>" 
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold text-sm">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Số lượng thực nhập (<?= htmlspecialchars($chiTiet['donViTinh'] ?? 'Kg') ?>) (*)</label>
                        <input type="number" step="0.01" name="slThucNhap" value="<?= htmlspecialchars($chiTiet['soLuongPhanBo'] ?? 0) ?>" required <?= $isDaHoanTat ? 'disabled' : '' ?>
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg <?= $isDaHoanTat ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-900' ?> font-bold text-sm">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Ghi chú nhập kho</label>
                        <input type="text" name="ghiChu" value="Hàng nguyên tem niêm phong, kiểm định đạt chuẩn 100%." <?= $isDaHoanTat ? 'disabled' : '' ?>
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg <?= $isDaHoanTat ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-800' ?> text-sm">
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

                <div class="space-y-2 py-3 flex-1 overflow-y-auto max-h-[320px]">
                    <?php if (!empty($dsViTri)): ?>
                        <?php foreach ($dsViTri as $idx => $vt): ?>
                            <label class="px-3.5 py-2.5 rounded-lg border border-slate-200 <?= $isDaHoanTat ? 'opacity-60 cursor-not-allowed' : 'hover:border-amber-500 hover:bg-amber-50/40 cursor-pointer' ?> flex items-center justify-between gap-3 transition-all">
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm"><span class="font-bold text-slate-900">Mã vị trí: <?= htmlspecialchars($vt['maViTri']) ?></span></div>
                                    <div class="text-xs text-slate-500 mt-0.5 whitespace-nowrap">Dãy <?= htmlspecialchars($vt['day']) ?> • Kệ <?= htmlspecialchars($vt['ke']) ?> • Tầng <?= htmlspecialchars($vt['tang']) ?> | Sức chứa: <?= number_format($vt['sucChuaKhaDung'] ?? 600) ?> Kg</div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <input type="radio" name="maViTri" value="<?= htmlspecialchars($vt['maViTri']) ?>" <?= $idx === 0 ? 'checked' : '' ?> <?= $isDaHoanTat ? 'disabled' : '' ?> class="w-4 h-4 text-amber-600 focus:ring-amber-500 border-slate-300">
                                </div>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-xs text-slate-500 border border-dashed rounded-lg">Không tìm thấy vị trí kệ trống phù hợp trong kho chỉ định.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Nút thao tác -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3 mt-2">
                <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>In phiếu</span>
                </button>

                <!--  ĐỔI NÚT LƯU PHIẾU THÀNH TRẠNG THÁI VÔ HIỆU HÓA KHI ĐÃ HOÀN TẤT -->
                <?php if ($isDaHoanTat): ?>
                    <button type="button" disabled class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap bg-slate-300 text-slate-600 cursor-not-allowed shadow-none">
                        <i class="fa-solid fa-lock"></i>
                        <span>Đã hoàn tất nhập kho</span>
                    </button>
                <?php else: ?>
                    <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap bg-amber-600 hover:bg-amber-700 text-white shadow-sm cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span>Lưu phiếu nhập kho</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>