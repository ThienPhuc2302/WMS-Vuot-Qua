<?php

$e = static function ($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
};

$hienThiNgay = static function ($value) {
    if (!$value) {
        return '—';
    }

    return date('d/m/Y', strtotime($value));
};

// Phân trang danh sách lô chờ kiểm định (6 dòng / trang)
$pageSize = 6;
$currentPage = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$totalLots = count($danhSachLo);
$totalPages = (int)ceil($totalLots / $pageSize);
if ($totalPages < 1) $totalPages = 1;

// Tự động nhảy đến trang chứa lô đang chọn nếu có
if (!empty($loDangChon) && !isset($_GET['p'])) {
    foreach ($danhSachLo as $idx => $item) {
        if ($item['maLoNL'] === $loDangChon['maLoNL']) {
            $currentPage = (int)floor($idx / $pageSize) + 1;
            break;
        }
    }
}
if ($currentPage > $totalPages) $currentPage = $totalPages;

$startIndex = ($currentPage - 1) * $pageSize;
$danhSachLoTrang = array_slice($danhSachLo, $startIndex, $pageSize);

?>

<div class="max-w-7xl mx-auto space-y-5 pb-6">

    <div>
        <h1 class="text-xl font-bold text-slate-900">
            Lập biên bản kiểm định nguyên liệu
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Kiểm tra chất lượng lô nguyên liệu thực tế trước khi điều phối nhập kho
        </p>
    </div>

    

    <!-- BẢNG DANH SÁCH LÔ CHỜ KIỂM ĐỊNH -->
    <section
        class="bg-white rounded-xl border border-slate-200
               shadow-sm overflow-hidden"
    >
        <div
            class="px-5 py-4 border-b border-slate-200
                   flex flex-col sm:flex-row
                   sm:items-center sm:justify-between gap-3"
        >
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-amber-600"></i>
                <h2 class="font-bold text-slate-900">
                    Danh sách lô nguyên liệu chờ kiểm định
                </h2>
            </div>

            <form
                method="get"
                action="index.php"
                class="flex gap-2 w-full sm:w-auto"
            >
                <input
                    type="hidden"
                    name="page"
                    value="vKiemDinhNL"
                >

                <input
                    type="text"
                    name="q"
                    value="<?= $e($tuKhoa) ?>"
                    placeholder="Tìm theo mã lô hoặc tên nguyên liệu..."
                    class="w-full sm:w-72 px-3 py-1.5 text-xs sm:text-sm
                           bg-slate-50 border border-slate-300
                           rounded-lg focus:outline-none
                           focus:ring-1 focus:ring-amber-500"
                >

                <button
                    type="submit"
                    class="px-3.5 py-1.5 rounded-lg bg-amber-600
                           hover:bg-amber-700 text-white
                           text-xs sm:text-sm font-semibold transition-all shadow-sm"
                >
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                        <th class="py-3.5 px-5">Mã lô</th>
                        <th class="py-3.5 px-4">Tên nguyên liệu</th>
                        <th class="py-3.5 px-4">Nhà cung cấp</th>
                        <th class="py-3.5 px-4 text-right">Số lượng</th>
                        <th class="py-3.5 px-4">Ngày SX</th>
                        <th class="py-3.5 px-4">Hạn sử dụng</th>
                        <th class="py-3.5 px-5">Trạng thái</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs">
                    <?php foreach ($danhSachLoTrang as $lo): ?>
                        <?php
                        $dangChon = $lo['maLoNL']
                            === ($loDangChon['maLoNL'] ?? '');

                        $urlChonLo = 'index.php?'
                            . http_build_query([
                                'page' => 'vKiemDinhNL',
                                'q' => $tuKhoa,
                                'maLo' => $lo['maLoNL'],
                                'p' => $currentPage
                            ]);
                        ?>

                        <tr
                            id="qc-row-<?= $e($lo['maLoNL']) ?>"
                            onclick="location.href='<?= $e($urlChonLo) ?>'"
                            class="<?= $dangChon
                                ? 'bg-amber-100/50 border-l-4 border-amber-500 font-semibold'
                                : 'hover:bg-slate-50 cursor-pointer' ?> transition-colors"
                        >
                            <td class="py-3 px-5 font-bold text-slate-900 whitespace-nowrap">
                                <?= $e($lo['maLoNL']) ?>
                            </td>

                            <td class="py-3 px-4 font-medium text-slate-900">
                                <?= $e($lo['tenNL']) ?>
                            </td>

                            <td class="py-3 px-4 text-slate-600">
                                <?= $e($lo['nhaCungCap'] ?: '—') ?>
                            </td>

                            <td
                                class="py-3 px-4 text-right
                                       font-semibold whitespace-nowrap text-slate-800"
                            >
                                <?= number_format(
                                    (float) $lo['soLuongLo'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                                <?= $e($lo['donViTinh']) ?>
                            </td>

                            <td
                                class="py-3 px-4 text-slate-600
                                       whitespace-nowrap"
                            >
                                <?= $e($hienThiNgay(
                                    $lo['ngaySanXuat']
                                )) ?>
                            </td>

                            <td
                                class="py-3 px-4 text-slate-600
                                       whitespace-nowrap"
                            >
                                <?= $e($hienThiNgay(
                                    $lo['hanSuDung']
                                )) ?>
                            </td>

                            <td class="py-3 px-5 whitespace-nowrap">
                                <span
                                    class="qc-status-badge inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600"
                                >
                                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>Chờ kiểm định
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($danhSachLoTrang)): ?>
                        <tr>
                            <td
                                colspan="7"
                                class="py-8 px-5 text-center
                                       text-sm text-slate-500"
                            >
                                Không tìm thấy lô nguyên liệu đang chờ kiểm định phù hợp.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang 6 dòng/trang -->
        <div
            class="px-5 py-3.5 bg-slate-50 border-t
                   border-slate-200 text-xs text-slate-600 flex flex-wrap items-center justify-between gap-3"
        >
            <div>
                Hiển thị <strong><?= count($danhSachLoTrang) ?></strong> / <strong><?= $totalLots ?></strong> lô chờ kiểm định
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php
                    $urlPrev = 'index.php?' . http_build_query([
                        'page' => 'vKiemDinhNL',
                        'q' => $tuKhoa,
                        'maLo' => $loDangChon['maLoNL'] ?? '',
                        'p' => max(1, $currentPage - 1)
                    ]);
                    $urlNext = 'index.php?' . http_build_query([
                        'page' => 'vKiemDinhNL',
                        'q' => $tuKhoa,
                        'maLo' => $loDangChon['maLoNL'] ?? '',
                        'p' => min($totalPages, $currentPage + 1)
                    ]);
                    ?>

                    <a href="<?= $e($urlPrev) ?>"
                        class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-xs font-medium <?= ($currentPage === 1) ? 'opacity-50 pointer-events-none' : '' ?>">
                        <i class="fa-solid fa-chevron-left mr-1"></i>Trước
                    </a>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                        $urlPage = 'index.php?' . http_build_query([
                            'page' => 'vKiemDinhNL',
                            'q' => $tuKhoa,
                            'maLo' => $loDangChon['maLoNL'] ?? '',
                            'p' => $i
                        ]);
                        ?>
                        <?php if ($i === $currentPage): ?>
                            <span class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center bg-amber-600 text-white shadow-sm">
                                <?= $i ?>
                            </span>
                        <?php else: ?>
                            <a href="<?= $e($urlPage) ?>"
                                class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center border border-slate-300 text-slate-700 bg-white hover:bg-slate-100">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <a href="<?= $e($urlNext) ?>"
                        class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-xs font-medium <?= ($currentPage === $totalPages) ? 'opacity-50 pointer-events-none' : '' ?>">
                        Sau<i class="fa-solid fa-chevron-right ml-1"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($loDangChon): ?>

        <form
            id="qcForm"
            method="post"
            action="index.php?page=vKiemDinhNL&amp;action=save"
            class="space-y-5"
        >
            <input
                type="hidden"
                name="csrf_token"
                value="<?= $e($_SESSION['csrf_token'] ?? '') ?>"
            >

            <input
                type="hidden"
                name="maLoNL"
                value="<?= $e($loDangChon['maLoNL']) ?>"
            >

            <!-- PHẦN 1 (DỜI LÊN TRÊN): DANH SÁCH TIÊU CHÍ KIỂM ĐỊNH -->
            <section
                class="bg-white rounded-xl border
                       border-slate-200 shadow-sm overflow-hidden"
            >
                <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-amber-600"></i>
                            <span>1. Danh sách tiêu chí kiểm định chất lượng</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Tích chọn tiêu chí áp dụng cho lô hàng, nhập kết quả đo thực tế và chọn Đánh giá (Đạt / Không đạt).
                        </p>
                    </div>
                </div>

                <?php if (empty($danhSachTieuChi)): ?>

                    <div class="p-5 text-sm text-rose-700 bg-rose-50">
                        Chưa có tiêu chí thuộc loại Nguyên liệu trong CSDL.
                    </div>

                <?php else: ?>

                    <?php
                    $maNLHienTai = $loDangChon['maNL'] ?? '';
                    $prefixSuggested = '';
                    if (in_array($maNLHienTai, ['NL001', 'NL002'])) $prefixSuggested = 'TC-BM';
                    elseif (in_array($maNLHienTai, ['NL003', 'NL005'])) $prefixSuggested = 'TC-BL';
                    elseif ($maNLHienTai === 'NL004') $prefixSuggested = 'TC-DG';
                    elseif ($maNLHienTai === 'NL006') $prefixSuggested = 'TC-TG';
                    ?>

                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left
                                   border-collapse erp-table"
                        >
                            <thead>
                                <tr class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                                    <th class="py-3 px-5 w-24">Áp dụng</th>
                                    <th class="py-3 px-4">Tên tiêu chí</th>
                                    <th class="py-3 px-4">Quy chuẩn kỹ thuật</th>
                                    <th class="py-3 px-4">Kết quả thực tế</th>
                                    <th class="py-3 px-4">Đánh giá</th>
                                    <th class="py-3 px-5">Ghi chú</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 text-xs">
                                <?php foreach (
                                    $danhSachTieuChi as $tc
                                ): ?>
                                    <?php
                                    $maTC = $e($tc['maTieuChi']);
                                    $isPreselected = empty($prefixSuggested) || (strpos($tc['maTieuChi'], $prefixSuggested) === 0);
                                    ?>

                                    <tr class="qc-tieu-chi-row transition-colors <?= $isPreselected ? 'bg-amber-50/20' : 'row-disabled opacity-60' ?>" data-matc="<?= $maTC ?>">
                                        <td class="py-3 px-5 whitespace-nowrap">
                                            <label
                                                class="flex items-center
                                                       gap-2 cursor-pointer select-none"
                                            >
                                                <input
                                                    type="checkbox"
                                                    class="qc-chon-tieu-chi
                                                           accent-amber-600 w-4 h-4 cursor-pointer"
                                                    <?= $isPreselected ? 'checked' : '' ?>
                                                >

                                                <span
                                                    class="text-xs
                                                           font-bold text-slate-700
                                                           whitespace-nowrap"
                                                >
                                                    <?= $maTC ?>
                                                </span>
                                            </label>
                                        </td>

                                        <td
                                            class="py-3 px-4
                                                   font-semibold text-slate-900 ten-tieu-chi"
                                        >
                                            <?= $e($tc['tenTieuChi']) ?>
                                        </td>

                                        <td
                                            class="py-3 px-4
                                                   text-slate-600 font-medium"
                                        >
                                            <?= $e($tc['quyChuan']) ?>
                                        </td>

                                        <td class="py-3 px-4">
                                            <input
                                                type="text"
                                                name="giaTriDoThucTe[<?= $maTC ?>]"
                                                maxlength="100"
                                                <?= $isPreselected ? '' : 'disabled' ?>
                                                placeholder="Nhập kết quả..."
                                                class="w-44 px-3 py-1.5
                                                       border border-slate-300
                                                       rounded-lg text-xs font-semibold
                                                       bg-white text-slate-900 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300
                                                       disabled:bg-slate-100 disabled:text-slate-400 qc-giatri-input"
                                            >
                                        </td>

                                        <td class="py-3 px-4">
                                            <select
                                                name="danhGia[<?= $maTC ?>]"
                                                <?= $isPreselected ? '' : 'disabled' ?>
                                                class="px-3 py-1.5 border
                                                       border-slate-300
                                                       rounded-lg bg-white
                                                       text-xs font-semibold text-slate-800 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300
                                                       disabled:bg-slate-100 disabled:text-slate-400 qc-danhgia-select cursor-pointer"
                                            >
                                                <option value="">
                                                    -- Đánh giá --
                                                </option>
                                                <option value="1">
                                                    Đạt
                                                </option>
                                                <option value="0">
                                                    Không đạt
                                                </option>
                                            </select>
                                        </td>

                                        <td class="py-3 px-5">
                                            <input
                                                type="text"
                                                name="ghiChuTieuChi[<?= $maTC ?>]"
                                                maxlength="255"
                                                <?= $isPreselected ? '' : 'disabled' ?>
                                                placeholder="Ghi chú (nếu có)"
                                                class="w-full min-w-[160px]
                                                       px-3 py-1.5 border
                                                       border-slate-300
                                                       rounded-lg text-xs
                                                       bg-white text-slate-800 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300
                                                       disabled:bg-slate-100 disabled:text-slate-400"
                                            >
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>
            </section>

            <!-- PHẦN 2 (DỜI XUỐNG DƯỚI): THÔNG TIN LÔ & KẾT LUẬN KIỂM ĐỊNH -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                <!-- 2A. Thông tin lô nguyên liệu -->
                <section
                    class="lg:col-span-7 bg-white rounded-xl
                           border border-slate-200 p-5 shadow-sm"
                >
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-amber-600"></i>
                            <span>2. Thông tin lô nguyên liệu</span>
                        </h2>
                    </div>

                    <dl
                        class="grid grid-cols-2 sm:grid-cols-3
                               gap-4 pt-4 text-sm"
                    >
                        <div>
                            <dt class="text-xs text-slate-500 uppercase font-semibold">
                                Mã lô
                            </dt>
                            <dd class="mt-1 font-bold text-slate-900 break-all">
                                <?= $e($loDangChon['maLoNL']) ?>
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-xs text-slate-500 uppercase font-semibold">
                                Tên nguyên liệu
                            </dt>
                            <dd class="mt-1 font-bold text-slate-900">
                                <?= $e($loDangChon['tenNL']) ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500 uppercase font-semibold">
                                Số lượng lô
                            </dt>
                            <dd class="mt-1 font-bold text-slate-900">
                                <?= number_format(
                                    (float) $loDangChon['soLuongLo'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                                <?= $e($loDangChon['donViTinh']) ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500 uppercase font-semibold">
                                Ngày sản xuất
                            </dt>
                            <dd class="mt-1 font-medium text-slate-800">
                                <?= $e($hienThiNgay(
                                    $loDangChon['ngaySanXuat']
                                )) ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500 uppercase font-semibold">
                                Hạn sử dụng
                            </dt>
                            <dd class="mt-1 font-medium text-slate-800">
                                <?= $e($hienThiNgay(
                                    $loDangChon['hanSuDung']
                                )) ?>
                            </dd>
                        </div>

                        <div class="col-span-2 sm:col-span-3">
                            <dt class="text-xs text-slate-500 uppercase font-semibold">
                                Nhà cung cấp
                            </dt>
                            <dd class="mt-1 font-medium text-slate-800">
                                <?= $e(
                                    $loDangChon['nhaCungCap'] ?: '—'
                                ) ?>
                            </dd>
                        </div>
                    </dl>
                </section>

                <!-- 2B. Kết luận kiểm định & Nút Lưu biên bản -->
                <section
                    class="lg:col-span-5 bg-white rounded-xl
                           border border-slate-200 p-5
                           shadow-sm flex flex-col justify-between space-y-4"
                >
                    <div>
                        <h2
                            class="font-bold text-slate-900
                                   border-b border-slate-100 pb-3 flex items-center gap-2"
                        >
                            <i class="fa-solid fa-clipboard-check text-slate-700"></i>
                            <span>3. Kết luận kiểm định</span>
                        </h2>

                        <div class="mt-4 space-y-3.5">
                            <div>
                                <label
                                    for="qcKetQuaTongThe"
                                    class="block text-xs uppercase font-bold
                                           text-slate-700 mb-1"
                                >
                                    Kết quả tổng thể
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="qcKetQuaTongThe"
                                    name="ketQuaTongThe"
                                    required
                                    class="w-full px-3 py-2
                                           border border-slate-300
                                           rounded-lg bg-white text-sm text-slate-800 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300 cursor-pointer"
                                >
                                    <option value="">-- Chọn kết luận --</option>
                                    <option value="Đạt">Đạt (Cho phép điều phối nhập kho)</option>
                                    <option value="Không đạt">Không đạt (Từ chối / Trả nhà cung cấp)</option>
                                </select>
                            </div>

                            <div id="qcLyDoWrap" class="hidden">
                                <label
                                    for="qcLyDoKhongDat"
                                    class="block text-xs uppercase font-bold
                                           text-slate-700 mb-1"
                                >
                                    Lý do không đạt
                                    <span class="text-red-500">*</span>
                                </label>

                                <textarea
                                    id="qcLyDoKhongDat"
                                    name="lyDoKhongDat"
                                    maxlength="500"
                                    rows="2"
                                    placeholder="Mô tả lý do không đạt..."
                                    class="w-full px-3 py-2 border
                                           border-slate-300 rounded-lg text-sm bg-white text-slate-800 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300"
                                ></textarea>
                            </div>

                            <div>
                                <label
                                    for="qcGhiChu"
                                    class="block text-xs uppercase font-bold
                                           text-slate-700 mb-1"
                                >
                                    Ghi chú biên bản
                                </label>

                                <textarea
                                    id="qcGhiChu"
                                    name="ghiChu"
                                    maxlength="500"
                                    rows="2"
                                    placeholder="Ghi chú thêm nếu có..."
                                    class="w-full px-3 py-2 border
                                           border-slate-300 rounded-lg text-sm bg-white text-slate-800 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Nút Lưu biên bản đặt hợp tình hợp lý ngay dưới phần Kết luận -->
                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button
                            id="qcBtnLuu"
                            type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-lg
                                   bg-amber-600 hover:bg-amber-700 active:bg-amber-800
                                   text-white text-xs sm:text-sm font-semibold flex items-center justify-center gap-2 shadow-sm cursor-pointer transition-all
                                   disabled:opacity-60
                                   disabled:cursor-not-allowed"
                        >
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Lưu biên bản kiểm định</span>
                        </button>
                    </div>
                </section>
            </div>
        </form>

    <?php else: ?>

        <div
            class="bg-white border border-slate-200
                   rounded-xl p-8 text-center text-sm text-slate-500 shadow-sm"
        >
            <i class="fa-solid fa-box-open text-3xl text-slate-300 mb-2 block"></i>
            Chạm vào một dòng lô hàng ở bảng trên để bắt đầu lập biên bản kiểm định.
        </div>

    <?php endif; ?>
</div>

<script>
(() => {
    window.wmsData = window.wmsData || {};

    window.wmsData.kiemDinhNL = <?= json_encode(
        [
            'maLoNL' => $loDangChon['maLoNL'] ?? null
        ],
        JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ) ?>;

    const form = document.getElementById('qcForm');
    

    if (!form) {
        return;
    }

    const ketQuaTongThe = document.getElementById('qcKetQuaTongThe');
    const lyDoWrap = document.getElementById('qcLyDoWrap');
    const lyDoInput = document.getElementById('qcLyDoKhongDat');
    const btnLuu = document.getElementById('qcBtnLuu');

    let dangLuu = false;
    let daLuu = false;

    function capNhatLyDo() {
        const khongDat = ketQuaTongThe.value === 'Không đạt';
        lyDoWrap.classList.toggle('hidden', !khongDat);
        lyDoInput.required = khongDat;
        if (khongDat && !lyDoInput.value.trim()) {
            lyDoInput.focus();
        }
    }

    ketQuaTongThe.addEventListener('change', capNhatLyDo);

    // Tự động đồng bộ Kết luận tổng thể từ đánh giá của các tiêu chí
    function capNhatDanhGiaTuDong() {
        const checkedRows = form.querySelectorAll('.qc-tieu-chi-row:not(.row-disabled)');
        if (checkedRows.length === 0) return;

        let coKhongDat = false;
        let tatCaDat = true;
        let coDanhGia = false;

        checkedRows.forEach((row) => {
            const selectDG = row.querySelector('.qc-danhgia-select');
            if (selectDG && selectDG.value !== '') {
                coDanhGia = true;
                if (selectDG.value === '0') {
                    coKhongDat = true;
                    tatCaDat = false;
                } else if (selectDG.value !== '1') {
                    tatCaDat = false;
                }
            } else {
                tatCaDat = false;
            }
        });

        if (coKhongDat) {
            ketQuaTongThe.value = 'Không đạt';
            capNhatLyDo();
            if (!lyDoInput.value.trim()) {
                lyDoInput.value = 'Có tiêu chí kiểm định không đạt quy chuẩn.';
            }
        } else if (tatCaDat && coDanhGia) {
            ketQuaTongThe.value = 'Đạt';
            capNhatLyDo();
        }
    }

    // Bắt sự kiện chọn đánh giá từng tiêu chí
    form.querySelectorAll('.qc-danhgia-select').forEach((select) => {
        select.addEventListener('change', capNhatDanhGiaTuDong);
    });

    // Bắt sự kiện bật/tắt checkbox tiêu chí
    form.querySelectorAll('.qc-chon-tieu-chi').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const row = checkbox.closest('.qc-tieu-chi-row');
            if (!row) return;

            const isChecked = checkbox.checked;
            row.classList.toggle('row-disabled', !isChecked);
            row.classList.toggle('opacity-60', !isChecked);
            row.classList.toggle('bg-amber-50/20', isChecked);

            row.querySelectorAll('input[name], select[name]').forEach((control) => {
                control.disabled = !isChecked;
            });

            capNhatDanhGiaTuDong();
        });
    });

    // Bấm vào bất kỳ ô input nào của dòng đang disabled thì tự động kích hoạt
    form.querySelectorAll('.qc-tieu-chi-row').forEach((row) => {
        row.addEventListener('click', (e) => {
            if (row.classList.contains('row-disabled') && e.target.type !== 'checkbox') {
                const cb = row.querySelector('.qc-chon-tieu-chi');
                if (cb) {
                    cb.checked = true;
                    cb.dispatchEvent(new Event('change'));
                }
            }
        });
    });

    if (!btnLuu) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (dangLuu || daLuu) {
            return;
        }

        if (!ketQuaTongThe.value) {
            showToast('Vui lòng chọn Kết quả kiểm định tổng thể (Đạt hoặc Không đạt)!', 'error');
            ketQuaTongThe.focus();
            return;
        }

        const checkedCheckboxes = form.querySelectorAll('.qc-chon-tieu-chi:checked');
        if (checkedCheckboxes.length === 0) {
            showToast('Vui lòng tích chọn ít nhất một tiêu chí kiểm định áp dụng cho lô hàng này!', 'error');
            return;
        }

        // Kiểm tra từng dòng tiêu chí đã chọn
        for (const cb of checkedCheckboxes) {
            const row = cb.closest('.qc-tieu-chi-row');
            if (!row) continue;

            const inputDo = row.querySelector('.qc-giatri-input');
            const selectDG = row.querySelector('.qc-danhgia-select');
            const tenTC = row.querySelector('.ten-tieu-chi')?.textContent.trim() || 'tiêu chí';

            if (!inputDo.value.trim()) {
                showToast('Vui lòng nhập Kết quả đo thực tế cho tiêu chí: ' + tenTC + '!', 'error');
                inputDo.focus();
                return;
            }

            if (!selectDG.value) {
                showToast('Vui lòng chọn Đánh giá (Đạt hoặc Không đạt) cho tiêu chí: ' + tenTC + '!', 'error');
                selectDG.focus();
                return;
            }
        }

        if (ketQuaTongThe.value === 'Không đạt' && !lyDoInput.value.trim()) {
            showToast('Vui lòng nhập Lý do không đạt khi kết luận lô không đạt chuẩn!', 'error');
            lyDoInput.focus();
            return;
        }

        const coTieuChiKhongDat = Array.from(
            form.querySelectorAll('select[name^="danhGia"]')
        ).some((select) => !select.disabled && select.value === '0');

        if (ketQuaTongThe.value === 'Đạt' && coTieuChiKhongDat) {
            showToast('Không thể kết luận lô ĐẠT khi có tiêu chí đánh giá KHÔNG ĐẠT! Vui lòng kiểm tra lại.', 'error');
            return;
        }

        const formData = new FormData(form);
        formData.set(
            'csrf_token',
            window.csrfToken || form.querySelector('input[name="csrf_token"]').value
        );

        dangLuu = true;
        btnLuu.disabled = true;
        btnLuu.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang lưu biên bản...';

        try {
            const response = await fetch(
                'index.php?page=vKiemDinhNL&action=save',
                {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Máy chủ không trả kết quả JSON. Vui lòng kiểm tra lại phiên đăng nhập.');
            }

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Không thể lưu biên bản kiểm định.');
            }

            daLuu = true;

            // Cập nhật trạng thái ngay tại dòng đang chọn trên bảng (dùng dấu chấm tròn theo chuẩn, không dùng badge màu nền)
            const row = document.getElementById('qc-row-' + data.maLoNL);
            if (row) {
                const badge = row.querySelector('.qc-status-badge');
                if (badge) {
                    const isDat = data.ketQuaTongThe === 'Đạt';
                    badge.className = 'qc-status-badge inline-flex items-center gap-1.5 text-xs font-semibold ' + (isDat ? 'text-emerald-600' : 'text-rose-600');
                    badge.innerHTML = '<span class="w-2 h-2 rounded-full ' + (isDat ? 'bg-emerald-500' : 'bg-rose-500') + ' shrink-0"></span>' + data.ketQuaTongThe;
                }
            }

            // Khóa các trường sau khi lưu thành công
            form.querySelectorAll('input, select, textarea, button').forEach((control) => {
                control.disabled = true;
            });

            btnLuu.innerHTML = '<i class="fa-solid fa-check-double mr-1"></i> Đã xác nhận: ' + data.maBienBan;
            btnLuu.className = 'w-full sm:w-auto px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-xs sm:text-sm font-semibold shadow-sm cursor-not-allowed';

            showToast('Lập biên bản kiểm định thành công! Mã: ' + data.maBienBan + ' (' + data.ketQuaTongThe + ')', 'success');
        } catch (error) {
            showToast(error.message || 'Không thể kết nối máy chủ.', 'error');
        } finally {
            dangLuu = false;
            if (!daLuu) {
                btnLuu.disabled = false;
                btnLuu.innerHTML = '<i class="fa-solid fa-floppy-disk mr-1"></i> Lưu biên bản kiểm định';
            }
        }
    });
})();
</script>
