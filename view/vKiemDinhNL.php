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

?>

<div class="max-w-7xl mx-auto space-y-5 pb-6">

    <div>
        <h1 class="text-xl font-bold text-slate-900">
            Lập biên bản kiểm định nguyên liệu
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Kiểm tra chất lượng lô nguyên liệu trước khi điều phối nhập kho.
        </p>
    </div>

    <!-- Thông báo kết quả thao tác -->
    <div
        id="qcThongBao"
        role="status"
        aria-live="polite"
        class="hidden rounded-lg border px-4 py-3 text-sm"
    ></div>

    <!-- Danh sách lô chờ kiểm định -->
    <section
        class="bg-white rounded-xl border border-slate-200
               shadow-sm overflow-hidden"
    >
        <div
            class="px-5 py-4 border-b border-slate-200
                   flex flex-col sm:flex-row
                   sm:items-center sm:justify-between gap-3"
        >
            <h2 class="font-bold text-slate-900">
                Danh sách lô chờ kiểm định
            </h2>

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
                    placeholder="Mã lô hoặc tên nguyên liệu..."
                    class="w-full sm:w-72 px-3 py-2 text-sm
                           bg-slate-50 border border-slate-300
                           rounded-lg focus:outline-none
                           focus:ring-1 focus:ring-amber-500"
                >

                <button
                    type="submit"
                    class="px-4 py-2 rounded-lg bg-amber-600
                           hover:bg-amber-700 text-white
                           text-sm font-semibold"
                >
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3 px-5">Mã lô</th>
                        <th class="py-3 px-4">Tên nguyên liệu</th>
                        <th class="py-3 px-4">Nhà cung cấp</th>
                        <th class="py-3 px-4 text-right">Số lượng</th>
                        <th class="py-3 px-4">Ngày SX</th>
                        <th class="py-3 px-4">Hạn sử dụng</th>
                        <th class="py-3 px-5">Trạng thái</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($danhSachLo as $lo): ?>
                        <?php
                        $dangChon = $lo['maLoNL']
                            === ($loDangChon['maLoNL'] ?? '');

                        $urlChonLo = 'index.php?'
                            . http_build_query([
                                'page' => 'vKiemDinhNL',
                                'q' => $tuKhoa,
                                'maLo' => $lo['maLoNL']
                            ]);
                        ?>

                        <tr
                            id="qc-row-<?= $e($lo['maLoNL']) ?>"
                            class="<?= $dangChon
                                ? 'bg-amber-50'
                                : 'hover:bg-slate-50' ?>"
                        >
                            <td class="py-3 px-5 font-semibold">
                                <a
                                    href="<?= $e($urlChonLo) ?>"
                                    class="text-amber-700 hover:underline"
                                >
                                    <?= $e($lo['maLoNL']) ?>
                                </a>
                            </td>

                            <td class="py-3 px-4 text-slate-800">
                                <?= $e($lo['tenNL']) ?>
                            </td>

                            <td class="py-3 px-4 text-slate-600">
                                <?= $e($lo['nhaCungCap'] ?: '—') ?>
                            </td>

                            <td
                                class="py-3 px-4 text-right
                                       font-semibold whitespace-nowrap"
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

                            <td class="py-3 px-5">
                                <span
                                    class="qc-status-badge inline-flex
                                           rounded-full bg-amber-100
                                           text-amber-800 px-2.5 py-1
                                           text-xs font-semibold
                                           whitespace-nowrap"
                                >
                                    Chờ kiểm định
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($danhSachLo)): ?>
                        <tr>
                            <td
                                colspan="7"
                                class="py-8 px-5 text-center
                                       text-sm text-slate-500"
                            >
                                Không tìm thấy lô nguyên liệu đang chờ
                                kiểm định phù hợp.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div
            class="px-5 py-3 bg-slate-50 border-t
                   border-slate-200 text-xs text-slate-500"
        >
            Có <?= count($danhSachLo) ?> lô trong danh sách.
            Bấm mã lô để xem và nhập kết quả kiểm định.
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

            <!-- Thông tin lô và kết luận -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                <section
                    class="lg:col-span-7 bg-white rounded-xl
                           border border-slate-200 p-5 shadow-sm"
                >
                    <h2
                        class="font-bold text-slate-900
                               border-b border-slate-100 pb-3"
                    >
                        Thông tin lô nguyên liệu
                    </h2>

                    <dl
                        class="grid grid-cols-2 sm:grid-cols-3
                               gap-4 pt-4 text-sm"
                    >
                        <div>
                            <dt class="text-xs text-slate-500">
                                Mã lô
                            </dt>
                            <dd class="mt-1 font-semibold break-all">
                                <?= $e($loDangChon['maLoNL']) ?>
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-xs text-slate-500">
                                Tên nguyên liệu
                            </dt>
                            <dd class="mt-1 font-semibold">
                                <?= $e($loDangChon['tenNL']) ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Số lượng lô
                            </dt>
                            <dd class="mt-1 font-semibold">
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
                            <dt class="text-xs text-slate-500">
                                Ngày sản xuất
                            </dt>
                            <dd class="mt-1">
                                <?= $e($hienThiNgay(
                                    $loDangChon['ngaySanXuat']
                                )) ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Hạn sử dụng
                            </dt>
                            <dd class="mt-1">
                                <?= $e($hienThiNgay(
                                    $loDangChon['hanSuDung']
                                )) ?>
                            </dd>
                        </div>

                        <div class="col-span-2 sm:col-span-3">
                            <dt class="text-xs text-slate-500">
                                Nhà cung cấp
                            </dt>
                            <dd class="mt-1">
                                <?= $e(
                                    $loDangChon['nhaCungCap'] ?: '—'
                                ) ?>
                            </dd>
                        </div>
                    </dl>
                </section>

                <section
                    class="lg:col-span-5 bg-white rounded-xl
                           border border-slate-200 p-5
                           shadow-sm space-y-4"
                >
                    <h2
                        class="font-bold text-slate-900
                               border-b border-slate-100 pb-3"
                    >
                        Kết luận kiểm định
                    </h2>

                    <div>
                        <label
                            for="qcKetQuaTongThe"
                            class="block text-sm font-medium
                                   text-slate-700 mb-1"
                        >
                            Kết quả tổng thể
                            <span class="text-rose-600">*</span>
                        </label>

                        <select
                            id="qcKetQuaTongThe"
                            name="ketQuaTongThe"
                            required
                            class="w-full px-3 py-2.5
                                   border border-slate-300
                                   rounded-lg bg-white text-sm"
                        >
                            <option value="">-- Chọn kết quả --</option>
                            <option value="Đạt">Đạt</option>
                            <option value="Không đạt">Không đạt</option>
                        </select>
                    </div>

                    <div id="qcLyDoWrap" class="hidden">
                        <label
                            for="qcLyDoKhongDat"
                            class="block text-sm font-medium
                                   text-slate-700 mb-1"
                        >
                            Lý do không đạt
                            <span class="text-rose-600">*</span>
                        </label>

                        <textarea
                            id="qcLyDoKhongDat"
                            name="lyDoKhongDat"
                            maxlength="500"
                            rows="2"
                            class="w-full px-3 py-2 border
                                   border-slate-300 rounded-lg text-sm"
                        ></textarea>
                    </div>

                    <div>
                        <label
                            for="qcGhiChu"
                            class="block text-sm font-medium
                                   text-slate-700 mb-1"
                        >
                            Ghi chú biên bản
                        </label>

                        <textarea
                            id="qcGhiChu"
                            name="ghiChu"
                            maxlength="500"
                            rows="2"
                            class="w-full px-3 py-2 border
                                   border-slate-300 rounded-lg text-sm"
                        ></textarea>
                    </div>
                </section>
            </div>

            <!-- Tiêu chí kiểm định -->
            <section
                class="bg-white rounded-xl border
                       border-slate-200 shadow-sm overflow-hidden"
            >
                <div class="px-5 py-4 border-b border-slate-200">
                    <h2 class="font-bold text-slate-900">
                        Danh sách tiêu chí kiểm định
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Tích chọn tiêu chí áp dụng cho lô này,
                        sau đó nhập kết quả và đánh giá.
                    </p>
                </div>

                <?php if (empty($danhSachTieuChi)): ?>

                    <div class="p-5 text-sm text-rose-700 bg-rose-50">
                        Chưa có tiêu chí thuộc loại Nguyên liệu
                        trong CSDL. Chưa thể xác nhận biên bản.
                    </div>

                <?php else: ?>

                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left
                                   border-collapse erp-table"
                        >
                            <thead>
                                <tr>
                                    <th class="py-3 px-5">Áp dụng</th>
                                    <th class="py-3 px-4">Tên tiêu chí</th>
                                    <th class="py-3 px-4">Quy chuẩn</th>
                                    <th class="py-3 px-4">Kết quả thực tế</th>
                                    <th class="py-3 px-4">Đánh giá</th>
                                    <th class="py-3 px-5">Ghi chú</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                <?php foreach (
                                    $danhSachTieuChi as $tc
                                ): ?>
                                    <?php
                                    $maTC = $e($tc['maTieuChi']);
                                    ?>

                                    <tr>
                                        <td class="py-3 px-5">
                                            <label
                                                class="flex items-center
                                                       gap-2 cursor-pointer"
                                            >
                                                <input
                                                    type="checkbox"
                                                    class="qc-chon-tieu-chi
                                                           accent-amber-600"
                                                >

                                                <span
                                                    class="text-xs
                                                           font-semibold
                                                           whitespace-nowrap"
                                                >
                                                    <?= $maTC ?>
                                                </span>
                                            </label>
                                        </td>

                                        <td
                                            class="py-3 px-4
                                                   font-medium text-sm"
                                        >
                                            <?= $e($tc['tenTieuChi']) ?>
                                        </td>

                                        <td
                                            class="py-3 px-4
                                                   text-slate-600 text-sm"
                                        >
                                            <?= $e($tc['quyChuan']) ?>
                                        </td>

                                        <td class="py-3 px-4">
                                            <input
                                                type="text"
                                                name="giaTriDoThucTe[<?= $maTC ?>]"
                                                maxlength="100"
                                                required
                                                disabled
                                                placeholder="Nhập kết quả"
                                                class="w-40 px-3 py-2
                                                       border border-slate-300
                                                       rounded-lg text-sm
                                                       disabled:bg-slate-100"
                                            >
                                        </td>

                                        <td class="py-3 px-4">
                                            <select
                                                name="danhGia[<?= $maTC ?>]"
                                                required
                                                disabled
                                                class="px-3 py-2 border
                                                       border-slate-300
                                                       rounded-lg bg-white
                                                       text-sm
                                                       disabled:bg-slate-100"
                                            >
                                                <option value="">
                                                    Chọn
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
                                                disabled
                                                placeholder="Có thể để trống"
                                                class="w-full min-w-[180px]
                                                       px-3 py-2 border
                                                       border-slate-300
                                                       rounded-lg text-sm
                                                       disabled:bg-slate-100"
                                            >
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="px-5 py-4 bg-slate-50
                               border-t border-slate-200
                               flex justify-end"
                    >
                        <button
                            id="qcBtnLuu"
                            type="submit"
                            class="px-5 py-2.5 rounded-lg
                                   bg-amber-600 hover:bg-amber-700
                                   text-white text-sm font-semibold
                                   disabled:opacity-60
                                   disabled:cursor-not-allowed"
                        >
                            <i class="fa-solid fa-check mr-2"></i>
                            Lưu biên bản kiểm định
                        </button>
                    </div>

                <?php endif; ?>
            </section>
        </form>

    <?php else: ?>

        <div
            class="bg-white border border-slate-200
                   rounded-xl p-6 text-sm text-slate-500"
        >
            Chọn một lô đang chờ kiểm định để bắt đầu.
        </div>

    <?php endif; ?>
</div>

<script>
(() => {
    // Giữ dữ liệu của chức năng trong một thuộc tính riêng.
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
    const thongBao = document.getElementById('qcThongBao');

    if (!form) {
        return;
    }

    const ketQuaTongThe = document.getElementById(
        'qcKetQuaTongThe'
    );

    const lyDoWrap = document.getElementById('qcLyDoWrap');
    const lyDoInput = document.getElementById('qcLyDoKhongDat');
    const btnLuu = document.getElementById('qcBtnLuu');

    let dangLuu = false;
    let daLuu = false;

    function hienThiThongBao(message, thanhCong) {
        thongBao.className = thanhCong
            ? 'rounded-lg border border-emerald-200 '
                + 'bg-emerald-50 text-emerald-800 px-4 py-3 text-sm'
            : 'rounded-lg border border-rose-200 '
                + 'bg-rose-50 text-rose-800 px-4 py-3 text-sm';

        thongBao.textContent = message;
    }

    function capNhatLyDo() {
        const khongDat = ketQuaTongThe.value === 'Không đạt';

        lyDoWrap.classList.toggle('hidden', !khongDat);
        lyDoInput.required = khongDat;
    }

    ketQuaTongThe.addEventListener('change', capNhatLyDo);
    capNhatLyDo();

    // Các input bị disabled sẽ không được gửi trong FormData.
    form.querySelectorAll('.qc-chon-tieu-chi').forEach(
        (checkbox) => {
            checkbox.addEventListener('change', () => {
                const row = checkbox.closest('tr');

                row.querySelectorAll(
                    'input[name], select[name]'
                ).forEach((control) => {
                    control.disabled = !checkbox.checked;
                });

                row.classList.toggle(
                    'bg-amber-50',
                    checkbox.checked
                );
            });
        }
    );

    if (!btnLuu) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (dangLuu || daLuu) {
            return;
        }

        const tieuChiDaChon = form.querySelectorAll(
            '.qc-chon-tieu-chi:checked'
        );

        if (tieuChiDaChon.length === 0) {
            hienThiThongBao(
                'Hãy chọn ít nhất một tiêu chí phù hợp với lô.',
                false
            );
            return;
        }

        // Kiểm tra lại nếu sự kiện được kích hoạt từ JavaScript.
        if (!form.reportValidity()) {
            return;
        }

        const coTieuChiKhongDat = Array.from(
            form.querySelectorAll('select[name^="danhGia"]')
        ).some((select) => {
            return !select.disabled && select.value === '0';
        });

        if (
            ketQuaTongThe.value === 'Đạt'
            && coTieuChiKhongDat
        ) {
            hienThiThongBao(
                'Không thể kết luận Đạt khi có tiêu chí Không đạt.',
                false
            );
            return;
        }

        const formData = new FormData(form);

        // Token lấy từ layout chung của dự án.
        formData.set(
            'csrf_token',
            window.csrfToken
                || form.querySelector(
                    'input[name="csrf_token"]'
                ).value
        );

        dangLuu = true;
        btnLuu.disabled = true;
        btnLuu.textContent = 'Đang lưu...';

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

            const contentType = response.headers.get(
                'content-type'
            ) || '';

            if (!contentType.includes('application/json')) {
                throw new Error(
                    'Máy chủ không trả JSON. '
                    + 'Kiểm tra phiên đăng nhập và nhật ký PHP.'
                );
            }

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.message || 'Không thể lưu biên bản.'
                );
            }

            daLuu = true;

            // Đổi trạng thái ngay tại dòng đang được chọn.
            // Giữ nguyên dòng, không sắp xếp lại danh sách.
            const row = document.getElementById(
                'qc-row-' + data.maLoNL
            );

            if (row) {
                const badge = row.querySelector(
                    '.qc-status-badge'
                );

                badge.textContent = data.ketQuaTongThe;

                badge.className = data.ketQuaTongThe === 'Đạt'
                    ? 'qc-status-badge inline-flex rounded-full '
                        + 'bg-emerald-100 text-emerald-800 '
                        + 'px-2.5 py-1 text-xs font-semibold '
                        + 'whitespace-nowrap'
                    : 'qc-status-badge inline-flex rounded-full '
                        + 'bg-rose-100 text-rose-800 '
                        + 'px-2.5 py-1 text-xs font-semibold '
                        + 'whitespace-nowrap';
            }

            // Biên bản đã xác nhận được khóa trên giao diện.
            form.querySelectorAll(
                'input, select, textarea, button'
            ).forEach((control) => {
                control.disabled = true;
            });

            btnLuu.textContent = 'Đã xác nhận: ' + data.maBienBan;

            hienThiThongBao(
                data.message
                    + ' Mã biên bản: ' + data.maBienBan
                    + '. Trạng thái lô: ' + data.trangThaiLo + '.',
                true
            );
        } catch (error) {
            hienThiThongBao(
                error.message || 'Không thể kết nối máy chủ.',
                false
            );
        } finally {
            dangLuu = false;

            if (!daLuu) {
                btnLuu.disabled = false;
                btnLuu.innerHTML =
                    '<i class="fa-solid fa-check mr-2"></i>'
                    + 'Lưu biên bản kiểm định';
            }
        }
    });
})();
</script>