<?php
/**
 * VIEW: vDieuPhoiXuatTP.php
 * Chức năng: Điều phối xuất thành phẩm (FEFO)
 * Phụ trách: Quản lý kho (Role: VT02 / QuanLyKho)
 * Giao diện Master chuẩn ERP, đồng bộ 100% CSDL MySQL
 */

// Biến được Controller cDieuPhoiXuatTP truyền sang:
// $danhSachPhieu: Danh sách các phiếu yêu cầu xuất TP
// $selectedPYCId: Mã phiếu yêu cầu đang được chọn
// $fefoData: Dữ liệu tính toán phân bổ FEFO của phiếu được chọn
// $errorFefo: Thông báo lỗi nếu có
?>
<div class="max-w-7xl mx-auto space-y-6 pb-6">
    <!-- Tiêu đề trang -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                <i class="fa-solid fa-boxes-packing text-amber-600"></i>
                <span>Điều phối xuất thành phẩm</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Tự động đề xuất phân bổ lô thành phẩm có hạn sử dụng gần nhất đáp ứng đơn hàng theo nguyên tắc <strong>FEFO (First Expired, First Out)</strong>
            </p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" onclick="window.location.reload()" 
                class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1.5 shadow-sm transition cursor-pointer">
                <i class="fa-solid fa-rotate text-slate-400"></i>
                <span>Làm mới</span>
            </button>
        </div>
    </div>

    <!-- KHỐI 1: DANH SÁCH PHIẾU YÊU CẦU XUẤT THÀNH PHẨM -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 bg-slate-50/60">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-base"></i>
                <h2 class="text-sm sm:text-base font-bold text-slate-900 uppercase tracking-tight">
                    DANH SÁCH PHIẾU YÊU CẦU XUẤT THÀNH PHẨM
                </h2>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                    <?= count($danhSachPhieu ?? []) ?> phiếu
                </span>
            </div>
            
            <!-- Bộ lọc & Thanh tìm kiếm -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full md:w-auto">
                <!-- Lọc trạng thái -->
                <select id="filterTrangThai" onchange="filterPYC()" 
                    class="py-1.5 px-3 text-xs bg-white border border-slate-300 rounded-lg text-slate-700 font-medium focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Chờ điều phối" <?= ($trangThaiFilter ?? '') === 'Chờ điều phối' ? 'selected' : '' ?>>Chờ điều phối (Chờ xử lý)</option>
                    <option value="Đã điều phối" <?= ($trangThaiFilter ?? '') === 'Đã điều phối' ? 'selected' : '' ?>>Đã điều phối</option>
                </select>

                <!-- Thanh tìm kiếm -->
                <div class="relative w-full sm:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="searchKeyword" value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        onkeyup="if(event.key === 'Enter') filterPYC()"
                        placeholder="Tìm mã PYC, khách hàng, mặt hàng..." 
                        class="w-full pl-8 pr-8 py-1.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                    <?php if (!empty($keyword)): ?>
                        <button onclick="document.getElementById('searchKeyword').value=''; filterPYC();" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table text-xs">
                <thead>
                    <tr class="bg-slate-100/80 text-slate-700 uppercase font-semibold text-[11px] tracking-wider border-b border-slate-200">
                        <th class="py-3 px-5">Mã phiếu yêu cầu</th>
                        <th class="py-3 px-4">Ngày yêu cầu</th>
                        <th class="py-3 px-4">Khách hàng / Đơn vị nhận</th>
                        <th class="py-3 px-4">Thành phẩm & Số lượng</th>
                        <th class="py-3 px-4 text-right">Tổng SL cần xuất</th>
                        <th class="py-3 px-5 text-center">Trạng thái</th>
                        <th class="py-3 px-4 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="pycTableBody">
                    <?php if (empty($danhSachPhieu)): ?>
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-3xl mb-2 block text-slate-300"></i>
                                <span>Hiện không có phiếu yêu cầu xuất nào phù hợp điều kiện lọc.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSachPhieu as $p): 
                            $isSelected = ($p['maPhieuYeuCau'] === $selectedPYCId);
                            $isChoDieuPhoi = in_array($p['trangThai'], ['Chờ điều phối', 'Chờ xử lý']);
                        ?>
                            <tr class="cursor-pointer transition-colors pyc-row <?= $isSelected ? 'bg-amber-50/80 font-semibold ring-1 ring-inset ring-amber-300' : 'hover:bg-slate-50' ?>"
                                data-pyc="<?= htmlspecialchars($p['maPhieuYeuCau'], ENT_QUOTES, 'UTF-8') ?>"
                                onclick="selectPYC('<?= htmlspecialchars($p['maPhieuYeuCau'], ENT_QUOTES, 'UTF-8') ?>')">
                                <td class="py-3.5 px-5 font-bold text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <?php if ($isSelected): ?>
                                            <i class="fa-solid fa-play text-[10px] text-amber-600"></i>
                                        <?php endif; ?>
                                        <span class="font-mono text-amber-900"><?= htmlspecialchars($p['maPhieuYeuCau']) ?></span>
                                    </div>
                                    <?php if (!empty($p['maLenhDieuPhoi'])): ?>
                                        <span class="block text-[10px] font-mono font-normal text-emerald-700 mt-0.5">
                                            Lệnh: <?= htmlspecialchars($p['maLenhDieuPhoi']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    <?= date('d/m/Y', strtotime($p['ngayYeuCau'])) ?>
                                </td>
                                <td class="py-3.5 px-4 text-slate-800">
                                    <div class="font-medium text-slate-900"><?= htmlspecialchars($p['donViYeuCau']) ?></div>
                                    <div class="text-[11px] text-slate-500 truncate max-w-xs" title="<?= htmlspecialchars($p['lyDoXuat'] ?? '') ?>">
                                        <?= htmlspecialchars($p['lyDoXuat'] ?? 'Xuất theo đơn đặt hàng') ?>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <div class="line-clamp-2 max-w-sm" title="<?= htmlspecialchars($p['danhSachMatHang'] ?? '') ?>">
                                        <?= htmlspecialchars($p['danhSachMatHang'] ?? '') ?>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900 whitespace-nowrap">
                                    <?= number_format($p['tongSoLuong'], 0) ?>
                                    <span class="text-[11px] font-normal text-slate-500">sp</span>
                                </td>
                                <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                    <?php if ($isChoDieuPhoi): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            <span>Chờ điều phối</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>Đã điều phối</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                        onclick="event.stopPropagation(); selectPYC('<?= htmlspecialchars($p['maPhieuYeuCau'], ENT_QUOTES, 'UTF-8') ?>')"
                                        class="px-2.5 py-1 rounded-md text-xs font-semibold transition <?= $isSelected ? 'bg-amber-600 text-white' : 'bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800' ?>">
                                        <?= $isSelected ? 'Đang xem' : 'Xem & Phân bổ' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- KHỐI 2: CHI TIẾT PHÂN BỔ LÔ THÀNH PHẨM XUẤT KHO (THUẬT TOÁN FEFO) -->
    <div id="allocationContainer" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <?php if (empty($selectedPYCId) || empty($fefoData)): ?>
            <div class="p-12 text-center text-slate-400">
                <i class="fa-solid fa-hand-pointer text-4xl mb-3 block text-slate-300"></i>
                <p class="text-sm font-medium text-slate-600">Vui lòng chọn một phiếu yêu cầu ở danh sách phía trên để xem phân bổ FEFO</p>
                <p class="text-xs text-slate-400 mt-1">Hệ thống sẽ tự động quét các lô cận hạn nhất trong kho đáp ứng đủ số lượng xuất</p>
            </div>
        <?php else: 
            $phieu = $fefoData['phieu'];
            $isDaDieuPhoi = $fefoData['isDaDieuPhoi'];
            $isDuTonToanBo = $fefoData['isDuTonToanBo'];
            $lenhSaved = $phieu['lenhDieuPhoi'] ?? null;
        ?>
            <!-- Header chi tiết phiếu -->
            <div class="px-6 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/70">
                <div>
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-truck-fast text-amber-600 text-lg"></i>
                        <h3 class="text-base font-bold text-slate-900">
                            Chi tiết phân bổ lô thành phẩm xuất kho (Thuật toán FEFO)
                        </h3>
                        <?php if ($isDaDieuPhoi): ?>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <i class="fa-solid fa-check-double mr-1"></i>Đã lập Lệnh điều phối
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300 animate-pulse">
                                <i class="fa-solid fa-bolt mr-1"></i>FEFO Tự động đề xuất
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 mt-1.5">
                        <span>Mã PYC: <strong class="text-slate-900 font-mono"><?= htmlspecialchars($phieu['maPhieuYeuCau']) ?></strong></span>
                        <span class="text-slate-300">•</span>
                        <span>Khách hàng: <strong class="text-slate-900"><?= htmlspecialchars($phieu['donViYeuCau']) ?></strong></span>
                        <span class="text-slate-300">•</span>
                        <span>Mục đích: <strong class="text-slate-700"><?= htmlspecialchars($phieu['lyDoXuat'] ?? 'Xuất kho theo hợp đồng') ?></strong></span>
                        <?php if (!empty($lenhSaved['maLenh'])): ?>
                            <span class="text-slate-300">•</span>
                            <span>Mã lệnh điều phối: <strong class="text-emerald-700 font-mono"><?= htmlspecialchars($lenhSaved['maLenh']) ?></strong></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Khung tóm tắt Tổng tồn khả dụng & Cần xuất -->
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-lg border <?= $isDuTonToanBo ? 'border-emerald-300 bg-emerald-50/40' : 'border-rose-300 bg-rose-50/50' ?> text-xs text-slate-700 self-start md:self-auto shrink-0 shadow-sm">
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-semibold">Tổng tồn khả dụng</span>
                        <strong class="text-sm <?= $isDuTonToanBo ? 'text-slate-900' : 'text-rose-600' ?>">
                            <?= number_format($fefoData['tongSoLuongKhaDung'], 0) ?> sp
                        </strong>
                    </div>
                    <span class="w-px h-6 bg-slate-200"></span>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-semibold">Cần xuất</span>
                        <strong class="text-sm text-slate-900">
                            <?= number_format($fefoData['tongSoLuongYeuCau'], 0) ?> sp
                        </strong>
                    </div>
                    <span class="w-px h-6 bg-slate-200"></span>
                    <div class="flex items-center gap-1.5">
                        <?php if ($isDuTonToanBo): ?>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="font-bold text-emerald-700">Đủ hàng 100%</span>
                        <?php else: ?>
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
                            <span class="font-bold text-rose-700">Thiếu tồn kho</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Cảnh báo thiếu tồn kho nếu không đủ hàng (Alternative flow 4.1) -->
            <?php if (!$isDuTonToanBo && !$isDaDieuPhoi): ?>
                <div class="mx-6 mt-4 p-4 rounded-xl border border-rose-200 bg-rose-50/80 text-rose-900 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg mt-0.5 shrink-0"></i>
                    <div class="text-xs space-y-1">
                        <strong class="font-bold text-sm text-rose-800">CẢNH BÁO: TỔNG TỒN KHẢ DỤNG KHÔNG ĐỦ ĐÁP ỨNG PHIẾU XUẤT!</strong>
                        <p class="text-rose-700 leading-relaxed">
                            Theo quy định quản lý kho (Use Case 4.1), hệ thống tạm thời vô hiệu hóa nút <em>"Xác nhận điều phối xuất kho"</em> đối với phiếu yêu cầu này do không đủ số lượng thành phẩm đạt chuẩn. Phiếu yêu cầu được giữ nguyên trạng thái <strong>"Chờ điều phối"</strong>.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Danh sách các mặt hàng và Bảng phân bổ FEFO từng mặt hàng -->
            <div class="p-6 space-y-6">
                <?php foreach ($fefoData['danhSachMatHang'] as $idx => $mh): ?>
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                        <!-- Tiêu đề mặt hàng -->
                        <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-amber-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                                    <?= $idx + 1 ?>
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">
                                        <?= htmlspecialchars($mh['tenMatHang']) ?>
                                    </h4>
                                    <span class="text-xs text-slate-500">
                                        Mã TP: <code class="text-slate-700 font-mono"><?= htmlspecialchars($mh['maMatHang']) ?></code> • Đơn vị tính: <strong><?= htmlspecialchars($mh['donViTinh']) ?></strong>
                                    </span>
                                </div>
                            </div>

                            <!-- Chỉ số đáp ứng của mặt hàng -->
                            <div class="flex items-center gap-3 text-xs">
                                <span>Cần: <strong class="text-slate-900"><?= number_format($mh['soLuongYeuCau'], 0) ?> <?= htmlspecialchars($mh['donViTinh']) ?></strong></span>
                                <span class="text-slate-300">•</span>
                                <span>Tồn khả dụng: <strong class="<?= $mh['isDuTon'] ? 'text-slate-900' : 'text-rose-600 font-bold' ?>"><?= number_format($mh['tongTonKhaDung'], 0) ?> <?= htmlspecialchars($mh['donViTinh']) ?></strong></span>
                                <span class="text-slate-300">•</span>
                                <span class="inline-flex items-center gap-1 font-bold <?= $mh['isDuTon'] ? 'text-emerald-700' : 'text-rose-600' ?>">
                                    <?php if ($mh['isDuTon']): ?>
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        <span>Đã phân bổ đủ <?= number_format($mh['soLuongDaPhanBo'], 0) ?> (100%)</span>
                                    <?php else: ?>
                                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                                        <span>Thiếu <?= number_format($mh['soLuongThieu'], 0) ?> <?= htmlspecialchars($mh['donViTinh']) ?> (Đạt <?= $mh['tyLePhanBo'] ?>%)</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Bảng danh sách các lô phân bổ theo FEFO -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse erp-table text-xs">
                                <thead>
                                    <tr class="bg-white text-slate-600 uppercase font-semibold text-[11px] tracking-wider border-b border-slate-200">
                                        <th class="py-3 px-5">Thứ tự ưu tiên (FEFO)</th>
                                        <th class="py-3 px-4">Mã lô TP</th>
                                        <th class="py-3 px-4">Hạn sử dụng</th>
                                        <th class="py-3 px-4">Vị trí lưu trữ kệ kho</th>
                                        <th class="py-3 px-4 text-right">Tồn khả dụng</th>
                                        <th class="py-3 px-5 text-right font-bold text-slate-800">Số lượng phân bổ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php if (empty($mh['danhSachLo'])): ?>
                                        <tr>
                                            <td colspan="6" class="py-6 text-center text-rose-500 font-medium">
                                                <i class="fa-solid fa-box-open mr-1.5"></i>
                                                Không tìm thấy lô thành phẩm nào trong kho đạt chuẩn kiểm định và còn hạn sử dụng!
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($mh['danhSachLo'] as $lo): 
                                            $isAllocated = ($lo['soLuongPhanBo'] > 0);
                                        ?>
                                            <tr class="<?= $isAllocated ? 'bg-amber-50/30' : 'bg-slate-50/50 opacity-60' ?>">
                                                <td class="py-3 px-5 text-slate-800 whitespace-nowrap">
                                                    <span class="font-bold text-slate-900 mr-1.5"><?= $lo['stt'] ?>.</span>
                                                    <span class="font-medium <?= $lo['stt'] === 1 ? 'text-amber-800' : 'text-slate-700' ?>">
                                                        <?= htmlspecialchars($lo['ghiChuFEFO']) ?>
                                                    </span>
                                                    <?php if ($lo['stt'] === 1): ?>
                                                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                            HSD gần nhất
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                                    <?= htmlspecialchars($lo['maLo']) ?>
                                                    <?php if (!empty($lo['phanXuong'])): ?>
                                                        <span class="block font-sans text-[11px] font-normal text-slate-500">
                                                            <?= htmlspecialchars($lo['phanXuong']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="py-3 px-4 text-slate-800 whitespace-nowrap">
                                                    <span class="font-semibold text-slate-900">
                                                        <?= $lo['hanSuDung'] ? date('d/m/Y', strtotime($lo['hanSuDung'])) : 'N/A' ?>
                                                    </span>
                                                    <?php if (isset($lo['soNgayConHan'])): ?>
                                                        <span class="text-slate-500 text-[11px] ml-1">
                                                            (Còn <?= $lo['soNgayConHan'] ?> ngày)
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="py-3 px-4 text-slate-700">
                                                    <div class="font-medium text-slate-900"><?= htmlspecialchars($lo['viTriHienThi']) ?></div>
                                                    <div class="text-[11px] text-slate-500 font-mono">Mã vị trí: <?= htmlspecialchars($lo['maViTri']) ?></div>
                                                </td>
                                                <td class="py-3 px-4 text-right font-medium text-slate-800 whitespace-nowrap">
                                                    <?= number_format($lo['slKhaDung'], 0) ?> <?= htmlspecialchars($mh['donViTinh']) ?>
                                                </td>
                                                <td class="py-3 px-5 text-right whitespace-nowrap">
                                                    <?php if ($isAllocated): ?>
                                                        <span class="font-bold text-slate-900 text-sm">
                                                            <?= number_format($lo['soLuongPhanBo'], 0) ?>
                                                        </span>
                                                        <span class="text-xs font-normal text-slate-500 ml-1">
                                                            <?= htmlspecialchars($mh['donViTinh']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-slate-400 font-medium italic">0 (Dự phòng)</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- FOOTER ĐIỀU KHIỂN & XÁC NHẬN ĐIỀU PHỐI -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <!-- Trạng thái tổng quan -->
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-slate-500">Trạng thái phân bổ:</span>
                    <?php if ($isDaDieuPhoi): ?>
                        <span class="inline-flex items-center gap-1.5 font-bold text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full border border-emerald-300">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span>Đã điều phối thành công (Lệnh: <?= htmlspecialchars($lenhSaved['maLenh'] ?? '') ?>)</span>
                        </span>
                    <?php elseif ($isDuTonToanBo): ?>
                        <span class="inline-flex items-center gap-1.5 font-bold text-slate-900">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                            <span>Đã đáp ứng đủ 100% (<?= number_format($fefoData['tongSoLuongPhanBo'], 0) ?> / <?= number_format($fefoData['tongSoLuongYeuCau'], 0) ?> sp)</span>
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 font-bold text-rose-700 bg-rose-100 px-3 py-1 rounded-full border border-rose-300">
                            <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                            <span>Thiếu tồn kho (Đạt <?= round(($fefoData['tongSoLuongPhanBo'] / max(1, $fefoData['tongSoLuongYeuCau'])) * 100, 1) ?>%)</span>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Nút thao tác -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Ghi chú xuất kho nếu chưa điều phối -->
                    <?php if (!$isDaDieuPhoi): ?>
                        <input type="text" id="ghiChuDieuPhoi" placeholder="Ghi chú điều phối (tùy chọn)..."
                            class="px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500 w-full sm:w-64">
                    <?php endif; ?>

                    <!-- In bảng phân bổ -->
                    <button type="button" onclick="printAllocation('<?= htmlspecialchars($phieu['maPhieuYeuCau']) ?>', '<?= htmlspecialchars($lenhSaved['maLenh'] ?? '') ?>')" 
                        class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-2 whitespace-nowrap shadow-xs transition cursor-pointer">
                        <i class="fa-solid fa-print text-slate-500"></i>
                        <span>In bảng phân bổ (A4)</span>
                    </button>

                    <!-- Xác nhận điều phối xuất kho -->
                    <?php if ($isDaDieuPhoi): ?>
                        <button type="button" disabled
                            class="px-5 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap bg-slate-200 text-slate-500 cursor-not-allowed border border-slate-300">
                            <i class="fa-solid fa-lock"></i>
                            <span>Phiếu đã điều phối</span>
                        </button>
                    <?php elseif ($isDuTonToanBo): ?>
                        <button type="button" id="btnConfirmDispatch" onclick="confirmDispatch('<?= htmlspecialchars($phieu['maPhieuYeuCau']) ?>')" 
                            class="px-5 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap bg-amber-600 hover:bg-amber-700 text-white shadow-sm transition active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-check"></i>
                            <span>Xác nhận điều phối xuất kho</span>
                        </button>
                    <?php else: ?>
                        <!-- Nút bị vô hiệu hóa khi không đủ tồn kho theo Use Case 4.1 -->
                        <button type="button" disabled 
                            title="Không đủ số lượng tồn kho khả dụng để thực hiện điều phối xuất kho"
                            class="px-5 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap bg-rose-200 text-rose-700 border border-rose-300 cursor-not-allowed">
                            <i class="fa-solid fa-ban"></i>
                            <span>Không đủ tồn kho để điều phối</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==================== JAVASCRIPT XỬ LÝ NGHIỆP VỤ & TƯƠNG TÁC ==================== -->
<script>
// Chuyển đổi chọn phiếu yêu cầu
function selectPYC(maPYC) {
    if (!maPYC) return;
    const kw = document.getElementById('searchKeyword') ? document.getElementById('searchKeyword').value.trim() : '';
    const tt = document.getElementById('filterTrangThai') ? document.getElementById('filterTrangThai').value.trim() : '';
    
    let url = 'index.php?page=vDieuPhoiXuatTP&pyc=' + encodeURIComponent(maPYC);
    if (kw) url += '&keyword=' + encodeURIComponent(kw);
    if (tt) url += '&trangThai=' + encodeURIComponent(tt);
    
    window.location.href = url;
}

// Lọc phiếu yêu cầu theo từ khóa và trạng thái
function filterPYC() {
    const kw = document.getElementById('searchKeyword') ? document.getElementById('searchKeyword').value.trim() : '';
    const tt = document.getElementById('filterTrangThai') ? document.getElementById('filterTrangThai').value.trim() : '';
    
    let url = 'index.php?page=vDieuPhoiXuatTP';
    const params = [];
    if (kw) params.push('keyword=' + encodeURIComponent(kw));
    if (tt) params.push('trangThai=' + encodeURIComponent(tt));
    
    if (params.length > 0) {
        url += '&' + params.join('&');
    }
    window.location.href = url;
}

// Xác nhận điều phối xuất kho (AJAX)
function confirmDispatch(maPYC) {
    if (!maPYC) {
        if (typeof showToast === 'function') showToast('Vui lòng chọn phiếu yêu cầu cần điều phối!', 'error');
        return;
    }

    const ghiChu = document.getElementById('ghiChuDieuPhoi') ? document.getElementById('ghiChuDieuPhoi').value.trim() : '';
    const btn = document.getElementById('btnConfirmDispatch');

    if (!confirm('Bạn có chắc chắn muốn xác nhận ban hành Lệnh điều phối xuất kho cho phiếu ' + maPYC + ' theo thuật toán FEFO không?')) {
        return;
    }

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Đang xử lý điều phối...';
    }

    const formData = new FormData();
    formData.append('action', 'confirmDispatch');
    formData.append('maPhieuYeuCau', maPYC);
    formData.append('ghiChu', ghiChu);
    formData.append('csrf_token', window.csrfToken || '');

    fetch('index.php?page=vDieuPhoiXuatTP&action=confirmDispatch', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': window.csrfToken || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (typeof showToast === 'function') {
                showToast(data.message || 'Điều phối xuất kho thành công!', 'success');
            } else {
                alert(data.message || 'Điều phối xuất kho thành công!');
            }
            setTimeout(() => {
                selectPYC(maPYC);
            }, 1200);
        } else {
            if (typeof showToast === 'function') {
                showToast(data.message || 'Lỗi khi điều phối xuất kho!', 'error');
            } else {
                alert(data.message || 'Lỗi khi điều phối xuất kho!');
            }
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Xác nhận điều phối xuất kho</span>';
            }
        }
    })
    .catch(err => {
        console.error('Lỗi fetch confirmDispatch:', err);
        if (typeof showToast === 'function') {
            showToast('Lỗi kết nối máy chủ khi điều phối: ' + err.message, 'error');
        } else {
            alert('Lỗi kết nối máy chủ: ' + err.message);
        }
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Xác nhận điều phối xuất kho</span>';
        }
    });
}

// In bảng phân bổ FEFO chuẩn khổ A4
function printAllocation(maPYC, maLenh) {
    const modal = document.getElementById('printModal');
    const body = document.getElementById('printModalBody');
    if (!modal || !body) {
        window.print();
        return;
    }

    body.innerHTML = '<div class="py-12 text-center text-slate-500"><i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i><br>Đang tải dữ liệu in ấn...</div>';
    modal.classList.remove('hidden');

    let url = 'index.php?page=vDieuPhoiXuatTP&action=getPrintData';
    if (maLenh) {
        url += '&maLenh=' + encodeURIComponent(maLenh);
    } else {
        url += '&maPhieuYeuCau=' + encodeURIComponent(maPYC);
    }

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if (!res.success) {
            body.innerHTML = '<div class="p-6 text-center text-rose-600 font-bold">' + (res.message || 'Lỗi tải bản in') + '</div>';
            return;
        }

        renderPrintA4(res);
    })
    .catch(err => {
        body.innerHTML = '<div class="p-6 text-center text-rose-600 font-bold">Lỗi kết nối: ' + err.message + '</div>';
    });
}

// Đóng modal in
function closePrintModal() {
    const modal = document.getElementById('printModal');
    if (modal) modal.classList.add('hidden');
}

// Render nội dung A4 đẹp mắt
function renderPrintA4(response) {
    const body = document.getElementById('printModalBody');
    if (!body) return;

    const isSaved = (response.type === 'saved');
    let title = isSaved ? 'LỆNH ĐIỀU PHỐI XUẤT KHO THÀNH PHẨM' : 'BẢNG ĐỀ XUẤT PHÂN BỔ XUẤT KHO (FEFO)';
    let data = response.data;

    let html = `
        <div class="bg-white p-8 w-full max-w-2xl text-slate-800 text-xs font-sans shadow-sm border border-slate-300">
            <!-- Header công ty -->
            <div class="flex justify-between items-start border-b border-slate-300 pb-4 mb-4">
                <div>
                    <h2 class="text-sm font-bold uppercase text-slate-900 tracking-wider">CÔNG TY CỔ PHẦN BÁNH KẸO BISCUITWMS</h2>
                    <p class="text-[11px] text-slate-500">Hệ thống Quản trị & Điều phối Kho Thành Phẩm Nhà Máy</p>
                    <p class="text-[11px] text-slate-500">Địa chỉ: Lô B1 - Khu Công Nghiệp Tân Bình, TP. Hồ Chí Minh</p>
                </div>
                <div class="text-right">
                    <p class="text-[11px] font-mono text-slate-600">Mã PYC: <strong>${isSaved ? data.maPhieuYeuCau : data.phieu.maPhieuYeuCau}</strong></p>
                    ${isSaved && data.maLenh ? `<p class="text-[11px] font-mono text-emerald-800 font-bold">Lệnh: ${data.maLenh}</p>` : ''}
                    <p class="text-[10px] text-slate-400 mt-0.5">Ngày in: ${new Date().toLocaleDateString('vi-VN')} ${new Date().toLocaleTimeString('vi-VN')}</p>
                </div>
            </div>

            <!-- Tên chứng từ -->
            <div class="text-center my-4">
                <h1 class="text-base font-extrabold uppercase text-slate-900 tracking-wide">${title}</h1>
                <p class="text-[11px] text-slate-500 italic mt-0.5">Tiêu chuẩn xuất kho theo nguyên tắc Hạn sử dụng trước - Xuất trước (FEFO)</p>
            </div>

            <!-- Thông tin chung -->
            <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50 rounded-lg border border-slate-200 mb-4 text-[11px]">
                <div>
                    <p>Khách hàng/Đơn vị nhận: <strong>${isSaved ? (data.donViYeuCau || '') : (data.phieu.donViYeuCau || '')}</strong></p>
                    <p class="mt-1">Lý do xuất: <span>${isSaved ? (data.lyDoXuat || '') : (data.phieu.lyDoXuat || '')}</span></p>
                </div>
                <div>
                    <p>Kho xuất hàng: <strong>${isSaved ? (data.tenKho || 'Kho Thành Phẩm') : 'Kho Thành Phẩm Bánh Quy'}</strong></p>
                    <p class="mt-1">Người điều phối: <strong>${isSaved ? (data.tenNguoiDieuPhoi || 'Quản lý kho') : '<?= htmlspecialchars($_SESSION['user']['hoTen'] ?? 'Quản lý kho') ?>'}</strong></p>
                </div>
            </div>

            <!-- Bảng chi tiết phân bổ -->
            <div class="mb-6">
                <table class="w-full text-left border-collapse border border-slate-300 text-[11px]">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 font-bold border-b border-slate-300">
                            <th class="p-2 border-r border-slate-300 text-center w-8">STT</th>
                            <th class="p-2 border-r border-slate-300">Tên thành phẩm</th>
                            <th class="p-2 border-r border-slate-300">Mã lô TP</th>
                            <th class="p-2 border-r border-slate-300 text-center">Hạn sử dụng</th>
                            <th class="p-2 border-r border-slate-300">Vị trí kệ kho</th>
                            <th class="p-2 text-right">SL phân bổ</th>
                        </tr>
                    </thead>
                    <tbody>
    `;

    let stt = 1;
    let tongSL = 0;

    if (isSaved && data.chiTiet) {
        data.chiTiet.forEach(item => {
            tongSL += parseFloat(item.soLuongPhanBo || 0);
            html += `
                <tr class="border-b border-slate-200">
                    <td class="p-2 border-r border-slate-200 text-center">${stt++}</td>
                    <td class="p-2 border-r border-slate-200 font-semibold">${item.tenTP}</td>
                    <td class="p-2 border-r border-slate-200 font-mono">${item.maLo}</td>
                    <td class="p-2 border-r border-slate-200 text-center">${item.hanSuDung ? item.hanSuDung.split('-').reverse().join('/') : ''}</td>
                    <td class="p-2 border-r border-slate-200">${item.day ? `Dãy ${item.day} • Kệ ${item.ke} • Tầng ${item.tang}` : item.maViTri}</td>
                    <td class="p-2 text-right font-bold">${parseFloat(item.soLuongPhanBo).toLocaleString('vi-VN')} ${item.donViTinh || ''}</td>
                </tr>
            `;
        });
    } else if (data.danhSachMatHang) {
        data.danhSachMatHang.forEach(mh => {
            if (mh.danhSachLo) {
                mh.danhSachLo.forEach(lo => {
                    if (lo.soLuongPhanBo > 0) {
                        tongSL += parseFloat(lo.soLuongPhanBo);
                        html += `
                            <tr class="border-b border-slate-200">
                                <td class="p-2 border-r border-slate-200 text-center">${stt++}</td>
                                <td class="p-2 border-r border-slate-200 font-semibold">${mh.tenMatHang}</td>
                                <td class="p-2 border-r border-slate-200 font-mono">${lo.maLo}</td>
                                <td class="p-2 border-r border-slate-200 text-center">${lo.hanSuDung ? lo.hanSuDung.split('-').reverse().join('/') : ''}</td>
                                <td class="p-2 border-r border-slate-200">${lo.viTriHienThi}</td>
                                <td class="p-2 text-right font-bold">${parseFloat(lo.soLuongPhanBo).toLocaleString('vi-VN')} ${mh.donViTinh || ''}</td>
                            </tr>
                        `;
                    }
                });
            }
        });
    }

    html += `
                        <tr class="bg-slate-50 font-bold border-t border-slate-300">
                            <td colspan="5" class="p-2 text-right border-r border-slate-300 uppercase">Tổng cộng số lượng xuất kho:</td>
                            <td class="p-2 text-right text-sm text-slate-900">${tongSL.toLocaleString('vi-VN')}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Chữ ký xác nhận -->
            <div class="grid grid-cols-3 gap-4 text-center mt-8 pt-4 border-t border-slate-200 text-[11px]">
                <div>
                    <p class="font-bold text-slate-900">NGƯỜI YÊU CẦU</p>
                    <p class="text-[10px] text-slate-400 italic">(Ký và ghi rõ họ tên)</p>
                    <div class="h-16"></div>
                    <p class="font-medium text-slate-700">${isSaved ? (data.nguoiYeuCau || '') : (data.phieu.nguoiYeuCau || '')}</p>
                </div>
                <div>
                    <p class="font-bold text-slate-900">QUẢN LÝ KHO (ĐIỀU PHỐI)</p>
                    <p class="text-[10px] text-slate-400 italic">(Ký và ghi rõ họ tên)</p>
                    <div class="h-16"></div>
                    <p class="font-medium text-slate-700">${isSaved ? (data.tenNguoiDieuPhoi || 'Quản lý kho') : '<?= htmlspecialchars($_SESSION['user']['hoTen'] ?? 'Quản lý kho') ?>'}</p>
                </div>
                <div>
                    <p class="font-bold text-slate-900">THỦ KHO XUẤT THỰC TẾ</p>
                    <p class="text-[10px] text-slate-400 italic">(Ký nhận bàn giao)</p>
                    <div class="h-16"></div>
                    <p class="font-medium text-slate-500">Chờ nhận lệnh</p>
                </div>
            </div>
        </div>
    `;

    body.innerHTML = html;
}
</script>
