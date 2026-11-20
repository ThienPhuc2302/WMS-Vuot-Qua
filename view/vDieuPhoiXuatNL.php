<?php
/** @var array<int, array<string, mixed>> $danhSachYeuCau */
/** @var string $maPhieuYeuCau */
/** @var array<string, mixed>|null $yeuCauDangChon */
/** @var array<int, array<string, mixed>> $deXuatFefo */
/** @var array<string, string>|null $thongBao */
$escape = static function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
$formatQuantity = static function ($value) {
    return number_format((float)$value, 2, ',', '.');
};
$statusClasses = [
    'Chờ điều phối' => 'bg-amber-100 text-amber-800',
    'Đã điều phối' => 'bg-sky-100 text-sky-800',
    'Đã xuất kho' => 'bg-emerald-100 text-emerald-800'
];
$tongCanXuat = 0.0;
$tongTonKhaDung = 0.0;
$daTinhTonTheoNguyenLieu = [];
$conTheDieuPhoi = !empty($yeuCauDangChon['chiTiet']) && $yeuCauDangChon['trangThai'] === 'Chờ điều phối';
foreach ($deXuatFefo as $dong) {
    $tongCanXuat += (float)$dong['soLuongYeuCau'];
    if (!isset($daTinhTonTheoNguyenLieu[$dong['maMatHang']])) {
        $tongTonKhaDung += (float)$dong['tongTonKhaDung'];
        $daTinhTonTheoNguyenLieu[$dong['maMatHang']] = true;
    }
    if ($dong['conThieu'] > 0) {
        $conTheDieuPhoi = false;
    }
}
?>
<div class="flex-1 overflow-y-auto p-6 bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-5 pb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Điều phối xuất nguyên liệu</h1>
            <p class="text-sm text-slate-500 mt-1">Phân bổ tồn kho theo FEFO (hạn sử dụng gần nhất được xuất trước) và
                giữ tồn cho lệnh xuất.</p>
        </div>

        <?php if (!empty($thongBao)): ?>
        <?php $isSuccess = $thongBao['type'] === 'success'; ?>
        <div class="rounded-lg border px-4 py-3 text-sm <?= $isSuccess ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800' ?>"
            role="alert">
            <i
                class="fa-solid <?= $isSuccess ? 'fa-circle-check' : 'fa-circle-exclamation' ?> mr-2"></i><?= $escape($thongBao['message']) ?>
        </div>
        <?php endif; ?>

        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div
                class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                    <h2 class="text-base font-bold text-slate-900">Phiếu yêu cầu xuất nguyên liệu</h2>
                </div>
                <label class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input id="requestSearch" type="search" placeholder="Tìm mã phiếu, phân xưởng, nguyên liệu..."
                        class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                </label>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse erp-table">
                    <thead>
                        <tr>
                            <th class="py-3 px-5">Mã phiếu</th>
                            <th class="py-3 px-4">Đơn vị yêu cầu</th>
                            <th class="py-3 px-4">Nguyên liệu / số lượng</th>
                            <th class="py-3 px-4">Ngày yêu cầu</th>
                            <th class="py-3 px-5">Trạng thái</th>
                            <th class="py-3 px-5 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="requestRows" class="divide-y divide-slate-100">
                        <?php foreach ($danhSachYeuCau as $yeuCau): ?>
                        <?php
                            $chiTietTimKiem = [];
                            foreach ($yeuCau['chiTiet'] as $chiTiet) {
                                $chiTietTimKiem[] = $chiTiet['tenMatHang'] . ' ' . $chiTiet['soLuongYeuCau'] . ' ' . $chiTiet['donViTinh'];
                            }
                            $tuKhoa = implode(' ', [
                                $yeuCau['maPhieuYeuCau'],
                                $yeuCau['donViYeuCau'],
                                implode(' ', $chiTietTimKiem)
                            ]);
                            $statusClass = $statusClasses[$yeuCau['trangThai']] ?? 'bg-slate-100 text-slate-700';
                            ?>
                        <tr class="request-row hover:bg-slate-50 <?= $yeuCauDangChon && $yeuCauDangChon['maPhieuYeuCau'] === $yeuCau['maPhieuYeuCau'] ? 'bg-amber-50' : '' ?>"
                            data-search="<?= $escape(mb_strtolower($tuKhoa, 'UTF-8')) ?>">
                            <td class="py-3 px-5 font-semibold text-slate-900"><?= $escape($yeuCau['maPhieuYeuCau']) ?>
                            </td>
                            <td class="py-3 px-4 text-slate-700"><?= $escape($yeuCau['donViYeuCau']) ?></td>
                            <td class="py-3 px-4 whitespace-normal min-w-64">
                                <?php if (!$yeuCau['chiTiet']): ?>
                                <span class="text-slate-400">Chưa có chi tiết</span>
                                <?php else: ?>
                                <ul class="space-y-1">
                                    <?php foreach ($yeuCau['chiTiet'] as $chiTiet): ?>
                                    <li class="text-sm text-slate-700">
                                        <?= $escape($chiTiet['tenMatHang']) ?>
                                        <span class="font-semibold text-slate-900">·
                                            <?= $formatQuantity($chiTiet['soLuongYeuCau']) ?>
                                            <?= $escape($chiTiet['donViTinh']) ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                <?= $escape(date('d/m/Y', strtotime($yeuCau['ngayYeuCau']))) ?></td>
                            <td class="py-3 px-5">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?= $statusClass ?>"><?= $escape($yeuCau['trangThai']) ?></span>
                            </td>
                            <td class="py-3 px-5 text-right">
                                <a href="index.php?page=vDieuPhoiXuatNL&amp;maPhieuYeuCau=<?= rawurlencode($yeuCau['maPhieuYeuCau']) ?>"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-amber-50 hover:border-amber-300">
                                    <i class="fa-solid fa-boxes-stacked text-amber-600"></i>
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (!$danhSachYeuCau): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-500">Chưa có phiếu yêu cầu xuất
                                nguyên liệu.</td>
                        </tr>
                        <?php endif; ?>
                        <tr id="noSearchResults" class="hidden">
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">Không tìm thấy phiếu phù hợp.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <?php if (isset($maPhieuYeuCau) && $maPhieuYeuCau !== '' && !$yeuCauDangChon): ?>
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            Không tìm thấy phiếu yêu cầu xuất nguyên liệu có mã <strong><?= $escape($maPhieuYeuCau) ?></strong>.
        </div>
        <?php elseif ($yeuCauDangChon): ?>
        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-boxes-stacked text-amber-600 text-lg"></i>
                        <h2 class="text-base font-bold text-slate-900">Phân bổ FEFO ·
                            <?= $escape($yeuCauDangChon['maPhieuYeuCau']) ?></h2>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">
                        <?= $escape($yeuCauDangChon['donViYeuCau']) ?>
                        · Ngày yêu cầu <?= $escape(date('d/m/Y', strtotime($yeuCauDangChon['ngayYeuCau']))) ?>
                        <?php if (!empty($yeuCauDangChon['nguoiYeuCau'])): ?>
                        · Người yêu cầu <?= $escape($yeuCauDangChon['nguoiYeuCau']) ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($yeuCauDangChon['lyDoXuat'])): ?>
                    <p class="mt-1 text-sm text-slate-600">Lý do: <?= $escape($yeuCauDangChon['lyDoXuat']) ?></p>
                    <?php endif; ?>
                </div>
                <span
                    class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold <?= $statusClasses[$yeuCauDangChon['trangThai']] ?? 'bg-slate-100 text-slate-700' ?>">
                    <?= $escape($yeuCauDangChon['trangThai']) ?>
                </span>
            </div>

            <?php if ($yeuCauDangChon['trangThai'] !== 'Chờ điều phối'): ?>
            <div class="m-6 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                Phiếu này đã được điều phối hoặc xuất kho; không thể tạo thêm lệnh điều phối.
            </div>
            <?php elseif (!$yeuCauDangChon['chiTiet']): ?>
            <div class="m-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Phiếu chưa có dòng nguyên liệu nên chưa thể điều phối.
            </div>
            <?php else: ?>
            <div class="px-6 py-4 border-b border-slate-200 flex flex-wrap gap-3 text-sm">
                <div class="rounded-lg border border-slate-300 px-3.5 py-2 text-slate-700">
                    Tổng tồn khả dụng: <strong class="text-slate-900"><?= $formatQuantity($tongTonKhaDung) ?></strong>
                </div>
                <div class="rounded-lg border border-slate-300 px-3.5 py-2 text-slate-700">
                    Tổng cần xuất: <strong class="text-slate-900"><?= $formatQuantity($tongCanXuat) ?></strong>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse erp-table">
                    <thead>
                        <tr>
                            <th class="px-5 py-3">Nguyên liệu</th>
                            <th class="px-4 py-3">Mã lô / kho / vị trí</th>
                            <th class="px-4 py-3">Hạn sử dụng</th>
                            <th class="px-4 py-3 text-right">Tồn khả dụng</th>
                            <th class="px-5 py-3 text-right">Phân bổ FEFO</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($deXuatFefo as $dong): ?>
                        <?php if (!$dong['phanBo']): ?>
                        <tr>
                            <td class="px-5 py-3 whitespace-normal">
                                <div class="font-semibold text-slate-900"><?= $escape($dong['tenMatHang']) ?></div>
                                <div class="text-xs text-slate-500">Cần <?= $formatQuantity($dong['soLuongYeuCau']) ?>
                                    <?= $escape($dong['donViTinh']) ?></div>
                            </td>
                            <td colspan="3" class="px-4 py-3 text-rose-700">Không có lô đạt kiểm định, còn hạn và có tồn
                                khả dụng.</td>
                            <td class="px-5 py-3 text-right font-semibold text-rose-700">0,00
                                <?= $escape($dong['donViTinh']) ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($dong['phanBo'] as $index => $phanBo): ?>
                        <tr>
                            <?php if ($index === 0): ?>
                            <td rowspan="<?= count($dong['phanBo']) ?>" class="px-5 py-3 align-top whitespace-normal">
                                <div class="font-semibold text-slate-900"><?= $escape($dong['tenMatHang']) ?></div>
                                <div class="text-xs text-slate-500">Cần <?= $formatQuantity($dong['soLuongYeuCau']) ?>
                                    <?= $escape($dong['donViTinh']) ?></div>
                            </td>
                            <?php endif; ?>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900"><?= $escape($phanBo['maLoNL']) ?></div>
                                <div class="text-xs text-slate-500"><?= $escape($phanBo['tenKho']) ?> ·
                                    <?= $escape($phanBo['viTri']) ?></div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <?= $escape(date('d/m/Y', strtotime($phanBo['hanSuDung']))) ?></td>
                            <td class="px-4 py-3 text-right text-slate-700">
                                <?= $formatQuantity($phanBo['soLuongKhaDung']) ?> <?= $escape($dong['donViTinh']) ?>
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-slate-900">
                                <?= $formatQuantity($phanBo['soLuongPhanBo']) ?> <?= $escape($dong['donViTinh']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($dong['conThieu'] > 0): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-2 text-right text-sm font-medium text-rose-700">
                                Còn thiếu <?= $formatQuantity($dong['conThieu']) ?> <?= $escape($dong['donViTinh']) ?>
                                nguyên liệu này.
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div
                class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-sm <?= $conTheDieuPhoi ? 'text-emerald-700' : 'text-rose-700' ?>">
                    <i class="fa-solid <?= $conTheDieuPhoi ? 'fa-circle-check' : 'fa-triangle-exclamation' ?> mr-1"></i>
                    <?= $conTheDieuPhoi ? 'Đã đủ tồn khả dụng để điều phối toàn bộ yêu cầu.' : 'Chưa đủ tồn khả dụng; không thể xác nhận điều phối.' ?>
                </div>
                <form method="post" action="index.php?page=vDieuPhoiXuatNL"
                    onsubmit="return confirm('Xác nhận tạo lệnh điều phối FEFO và giữ tồn cho phiếu này?');">
                    <input type="hidden" name="action" value="confirm">
                    <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="maPhieuYeuCau" value="<?= $escape($yeuCauDangChon['maPhieuYeuCau']) ?>">
                    <button type="submit" <?= $conTheDieuPhoi ? '' : 'disabled' ?>
                        class="px-5 py-2.5 rounded-lg text-sm font-semibold inline-flex items-center gap-2 <?= $conTheDieuPhoi ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-slate-200 text-slate-400 cursor-not-allowed' ?>">
                        <i class="fa-solid fa-check"></i>
                        Xác nhận điều phối xuất kho
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    </div>
</div>

<script>
const requestSearch = document.getElementById('requestSearch');
if (requestSearch) {
    requestSearch.addEventListener('input', () => {
        const query = requestSearch.value.trim().toLocaleLowerCase('vi');
        const rows = Array.from(document.querySelectorAll('.request-row'));
        let visibleCount = 0;
        rows.forEach((row) => {
            const visible = row.dataset.search.includes(query);
            row.classList.toggle('hidden', !visible);
            if (visible) visibleCount++;
        });
        const noResults = document.getElementById('noSearchResults');
        if (noResults) noResults.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
    });
}
</script>