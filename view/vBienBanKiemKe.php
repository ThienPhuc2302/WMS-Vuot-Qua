<?php
$currentLoai = $_GET['loai'] ?? 'NL';
$currentLoai = strtoupper($currentLoai) === 'TP' ? 'TP' : 'NL';

$danhSachDot = isset($danhSachDot) ? $danhSachDot : [];
$danhSachKho = isset($danhSachKho) ? $danhSachKho : [];
$currentDot = isset($currentDot) ? $currentDot : [];
$itemsFromDB = isset($kiemKeItems) ? $kiemKeItems : [];

$maDotHienTai = $currentDot['maDotKiemKe'] ?? ('DOT-KK-' . $currentLoai . '-' . date('mY'));
$tenKhoHienTai = ($currentDot['tenKho'] ?? 'Kho') . ' (' . ($currentDot['maKho'] ?? 'KHO') . ')';
$trangThaiDot = trim($currentDot['trangThai'] ?? 'Đang thực hiện');

$isCompleted = (strcasecmp($trangThaiDot, 'Đã hoàn tất') === 0 || strpos($trangThaiDot, 'Hoàn tất') !== false);
?>

<style>
@media print {
    @page { size: A4 landscape; margin: 10mm; }
    body * { visibility: hidden; }
    #printableArea, #printableArea * { visibility: visible; }
    #printableArea { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; background: white !important; }
    .no-print, input, button, select { display: none !important; }
    table { width: 100% !important; border-collapse: collapse !important; }
    th, td { padding: 6px 8px !important; font-size: 11px !important; border: 1px solid #cbd5e1 !important; }
    .print-only { display: block !important; }
}
.print-only { display: none; }
</style>

<div id="printableArea" class="max-w-7xl mx-auto space-y-5 pb-0">
    <!-- Tiêu đề & chuyển tab -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Lập biên bản kiểm kê kho</h1>
            <p class="text-sm text-slate-500 mt-1">Kiểm đếm thực tế từng lô hàng tại vị trí kệ, lưu tiến độ tức thì và đối chiếu chênh lệch sổ sách</p>
        </div>

        <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-200 shadow-sm text-xs no-print">
            <a href="index.php?page=vBienBanKiemKe&loai=NL" class="px-3.5 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-all <?= ($currentLoai === 'NL') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
                <i class="fa-solid fa-wheat-awn mr-1"></i> Kiểm kê nguyên liệu
            </a>
            <a href="index.php?page=vBienBanKiemKe&loai=TP" class="px-3.5 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-all <?= ($currentLoai === 'TP') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
                <i class="fa-solid fa-cookie-bite mr-1"></i> Kiểm kê thành phẩm
            </a>
        </div>
    </div>

    <!-- Thông tin đợt kiểm kê & Tiến độ (Ý tưởng 1: Clean Card Layout) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Card 1: Đợt kiểm kê -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm hover:border-amber-300 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1">
                <label for="selectDotKiemKe" class="text-xs text-slate-500 uppercase font-bold tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-clipboard-list text-amber-600 text-sm"></i> Đợt kiểm kê
                </label>
                <span class="text-xs font-bold text-slate-700">
                    <?= $maDotHienTai ?>
                </span>
            </div>
            <div class="mt-2.5">
                <select id="selectDotKiemKe" onchange="location.href='index.php?page=vBienBanKiemKe&loai=<?= $currentLoai ?>&madot=' + this.value" 
                    class="w-full text-xs sm:text-sm font-bold text-slate-800 bg-slate-50 hover:bg-slate-100/80 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white cursor-pointer transition-all shadow-sm no-print">
                    <?php foreach ($danhSachDot as $dot): ?>
                        <option value="<?= $dot['maDotKiemKe'] ?>" <?= ($dot['maDotKiemKe'] === $maDotHienTai) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dot['tenDotKiemKe']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="print-only text-sm font-bold text-slate-900 mt-1"><?= $maDotHienTai ?> — <?= htmlspecialchars($currentDot['tenDotKiemKe'] ?? '') ?></div>
            </div>
        </div>

        <!-- Card 2: Kho thực hiện (Thông tin hiển thị cố định của đợt) -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm hover:border-amber-300 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-slate-500 uppercase font-bold tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-warehouse text-amber-600 text-sm"></i> Kho thực hiện
                </span>
                <span class="text-xs font-bold text-slate-700">
                    <?= htmlspecialchars($currentDot['maKho'] ?? 'KHO') ?>
                </span>
            </div>
            <div class="mt-2.5 py-1.5">
                <div class="text-sm sm:text-base font-bold text-slate-900">
                    <?= htmlspecialchars($currentDot['tenKho'] ?? $tenKhoHienTai) ?>
                </div>
            </div>
        </div>

        <!-- Card 3: Trạng thái & Tiến độ kiểm kê -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm hover:border-amber-300 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-slate-500 uppercase font-bold tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-pie text-amber-600 text-sm"></i> Trạng thái & Tiến độ
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                    <span class="w-2 h-2 rounded-full <?= $isCompleted ? 'bg-emerald-500' : 'bg-amber-500' ?> shrink-0"></span>
                    <?= $trangThaiDot ?>
                </span>
            </div>
            <div class="mt-2.5">
                <div class="flex items-center justify-between text-xs mb-1.5 font-medium text-slate-600">
                    <span>Tiến độ kiểm đếm:</span>
                    <span id="progressText" class="font-bold text-slate-900">Đang tính...</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden border border-slate-200/60">
                    <div id="progressBar" class="bg-amber-500 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng danh sách kiểm kê chi tiết -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH LÔ HÀNG KIỂM KÊ THỰC TẾ</h2>
            </div>
            
            <div class="flex items-center gap-2.5 w-full sm:w-auto no-print">
                <div class="relative w-full sm:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="searchInput" placeholder="Tìm theo mã lô, mặt hàng, vị trí kệ..." 
                        class="w-full pl-8 pr-7 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                        <th class="py-3.5 px-5">Mã lô</th>
                        <th class="py-3.5 px-4">Tên hàng hóa</th>
                        <th class="py-3.5 px-4">Vị trí lưu trữ</th>
                        <th class="py-3.5 px-4 text-right">Tồn sổ sách</th>
                        <th class="py-3.5 px-4 text-right">SL tốt thực tế</th>
                        <th class="py-3.5 px-4 text-right">SL hỏng thực tế</th>
                        <th class="py-3.5 px-4 text-right">Tổng thực tế</th>
                        <th class="py-3.5 px-4 text-right">Chênh lệch</th>
                        <th class="py-3.5 px-5 text-center no-print">Thao tác kiểm đếm</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs" id="kiemKeTableBody"></tbody>
            </table>
        </div>

        <!-- Thanh điều hướng & Các nút hoàn tất / in ấn -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-1.5 no-print" id="paginationContainer"></div>

            <div class="flex items-center gap-3 ml-auto no-print">
                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-medium flex items-center gap-2 cursor-pointer transition-colors shadow-sm">
                    <i class="fa-solid fa-print"></i>
                    <span>In biên bản</span>
                </button>
                
                <button type="button" id="btnHoanTat" <?= $isCompleted ? 'disabled' : '' ?> 
                    class="px-5 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition-all <?= $isCompleted ? 'bg-slate-200 text-slate-500 cursor-not-allowed shadow-none' : 'bg-amber-600 hover:bg-amber-700 text-white shadow-sm cursor-pointer' ?>">
                    <i class="fa-solid fa-file-circle-check"></i>
                    <span><?= $isCompleted ? 'Đã hoàn tất kiểm kê' : 'Hoàn tất đợt kiểm kê' ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var rawItems = <?= json_encode($itemsFromDB, JSON_UNESCAPED_UNICODE) ?>;
var isCompleted = <?= $isCompleted ? 'true' : 'false' ?>;
var maDotHienTai = '<?= $maDotHienTai ?>';
var csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';
var currentPage = 1;
var pageSize = 6;

// Khởi tạo danh sách lô hàng kiểm kê với trạng thái thực tế từ CSDL
var inventoryData = rawItems.map(function(item) {
    var ton = Number(item.tonSoSach) || 0;
    var daKiem = Number(item.daKiem) === 1;
    
    // Nếu lô này đã được xác nhận kiểm đếm trước đó, lấy số liệu đã lưu trong CSDL
    // Nếu chưa kiểm đếm: để null để nhân viên tự nhập số lượng thực tế ngoài kho
    var slTot = daKiem ? (Number(item.soLuongTotThucTe) || 0) : null;
    var slHong = daKiem ? (Number(item.soLuongHongThucTe) || 0) : 0;
    
    return {
        maTonKho: item.maTonKho,
        maLo: item.maLo,
        maViTri: item.maViTri,
        maHang: item.maHang,
        tenHang: item.tenHang,
        donViTinh: item.donViTinh || '',
        viTri: (item.tenKho || item.maKho || '') + ' • Dãy ' + (item.day || '') + '-Kệ ' + (item.ke || '') + '-Tầng ' + (item.tang || ''),
        tonSoSach: ton,
        slTot: slTot,
        slHong: slHong,
        daKiem: daKiem,
        tinhTrang: item.tinhTrang || ''
    };
});

// Cập nhật thanh tiến độ kiểm đếm
function updateProgressBar() {
    var totalLots = inventoryData.length;
    var completedLots = inventoryData.filter(function(i) { return i.daKiem; }).length;
    var percent = totalLots > 0 ? Math.round((completedLots / totalLots) * 100) : 0;

    var progressText = document.getElementById('progressText');
    var progressBar = document.getElementById('progressBar');

    if (progressText) {
        progressText.innerHTML = `Đã xác nhận: <strong>${completedLots}/${totalLots} lô</strong> (${percent}%)`;
    }
    if (progressBar) {
        progressBar.style.width = percent + '%';
        if (percent === 100) {
            progressBar.className = 'bg-emerald-500 h-2.5 rounded-full transition-all duration-300';
        } else {
            progressBar.className = 'bg-amber-500 h-2.5 rounded-full transition-all duration-300';
        }
    }
}

function renderTable() {
    var searchInput = document.getElementById('searchInput');
    var keyword = searchInput ? searchInput.value.toLowerCase().trim() : '';
    
    var filtered = inventoryData.filter(function(item) {
        return item.maLo.toLowerCase().includes(keyword) ||
               item.tenHang.toLowerCase().includes(keyword) ||
               item.viTri.toLowerCase().includes(keyword);
    });

    var totalPages = Math.ceil(filtered.length / pageSize) || 1;
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    var start = (currentPage - 1) * pageSize;
    var paginatedItems = filtered.slice(start, start + pageSize);

    var tbody = document.getElementById('kiemKeTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (paginatedItems.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="py-8 text-center text-slate-500 text-xs">Không tìm thấy lô hàng kiểm kê nào phù hợp.</td></tr>';
        renderPagination(0, 1);
        updateProgressBar();
        return;
    }

    paginatedItems.forEach(function(item) {
        var hasEnteredTot = (item.slTot !== null && item.slTot !== '' && !isNaN(item.slTot));
        var valTot = hasEnteredTot ? Number(item.slTot) : 0;
        var valHong = (item.slHong !== null && item.slHong !== '' && !isNaN(item.slHong)) ? Number(item.slHong) : 0;
        var totalThucTe = valTot + valHong;
        var chenhLech = totalThucTe - item.tonSoSach;
        
        var totalDisplay = '';
        var badgeChenhLech = '';

        if (!hasEnteredTot && !item.daKiem) {
            totalDisplay = '<span class="text-slate-400 font-normal italic">—</span>';
            badgeChenhLech = '<span class="inline-flex items-center gap-1.5 text-xs text-amber-600 font-medium"><i class="fa-regular fa-clock"></i> Chờ kiểm đếm</span>';
        } else {
            totalDisplay = `<span class="font-bold text-slate-900">${totalThucTe.toLocaleString()} ${item.donViTinh}</span>`;
            if (chenhLech === 0) {
                badgeChenhLech = '<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>0 (Khớp)</span>';
            } else if (chenhLech > 0) {
                badgeChenhLech = '<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600"><span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>+' + chenhLech.toLocaleString() + ' (Thừa)</span>';
            } else {
                badgeChenhLech = '<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600"><span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>' + chenhLech.toLocaleString() + ' (Hao hụt)</span>';
            }
        }

        var disabledAttr = isCompleted ? 'disabled' : '';
        var inputBgStyle = isCompleted ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-900';

        var tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50 transition-colors' + (item.daKiem ? ' bg-blue-50/20' : '');
        tr.innerHTML = `
            <td class="py-3 px-5 font-semibold text-slate-900 whitespace-nowrap">
                ${item.maLo}
                <div class="print-only text-[10px] text-slate-500 font-normal">${item.daKiem ? 'Đã kiểm' : 'Chưa kiểm'}</div>
            </td>
            <td class="py-3 px-4 font-medium text-slate-900">${item.tenHang}</td>
            <td class="py-3 px-4 text-slate-600 text-xs">${item.viTri}</td>
            <td class="py-3 px-4 text-right font-semibold text-slate-800">${item.tonSoSach.toLocaleString()} ${item.donViTinh}</td>
            <td class="py-3 px-4 text-right">
                <input type="number" min="0" step="any" value="${hasEnteredTot ? item.slTot : ''}" placeholder="Nhập SL..." data-maton="${item.maTonKho}" data-type="tot" ${disabledAttr} 
                    class="w-24 text-right px-2.5 py-1 border border-slate-300 rounded-lg ${inputBgStyle} font-semibold text-xs focus:outline-none focus:ring-1 focus:ring-amber-500 sl-tot-input">
            </td>
            <td class="py-3 px-4 text-right">
                <input type="number" min="0" step="any" value="${(item.slHong !== null && item.slHong !== '') ? item.slHong : 0}" placeholder="0" data-maton="${item.maTonKho}" data-type="hong" ${disabledAttr} 
                    class="w-20 text-right px-2.5 py-1 border border-slate-300 rounded-lg ${inputBgStyle} ${Number(item.slHong) > 0 ? 'text-rose-600 font-bold' : ''} text-xs focus:outline-none focus:ring-1 focus:ring-amber-500 sl-hong-input">
            </td>
            <td class="py-3 px-4 text-right whitespace-nowrap">${totalDisplay}</td>
            <td class="py-3 px-4 text-right whitespace-nowrap">${badgeChenhLech}</td>
            <td class="py-3 px-5 text-center no-print whitespace-nowrap">
                ${isCompleted ? 
                    '<span class="inline-flex items-center gap-1 text-xs text-emerald-600 font-medium"><i class="fa-solid fa-lock"></i> Đã chốt</span>' : 
                    `<button type="button" data-maton="${item.maTonKho}" class="btn-xac-nhan-lo px-3 py-1.5 rounded-lg ${item.daKiem ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800'} text-white text-xs font-semibold shadow-sm flex items-center justify-center gap-1.5 cursor-pointer transition-all mx-auto">
                        <i class="fa-solid ${item.daKiem ? 'fa-check-double' : 'fa-floppy-disk'}"></i>
                        <span>${item.daKiem ? 'Cập nhật lại' : 'Xác nhận lô này'}</span>
                    </button>`
                }
            </td>
        `;
        tbody.appendChild(tr);
    });

    renderPagination(totalPages, currentPage);
    updateProgressBar();
    if (!isCompleted) {
        attachInputEvents();
        attachConfirmEvents();
    }
}

function renderPagination(totalPages, page) {
    var container = document.getElementById('paginationContainer');
    if (!container || totalPages <= 1) {
        if (container) container.innerHTML = '';
        return;
    }

    var html = `<button type="button" onclick="changePage(${page - 1})" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-xs font-medium ${page === 1 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}"><i class="fa-solid fa-chevron-left mr-1"></i>Trước</button>`;
    
    for (var i = 1; i <= totalPages; i++) {
        if (i === page) {
            html += `<button type="button" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center bg-amber-600 text-white shadow-sm">${i}</button>`;
        } else {
            html += `<button type="button" onclick="changePage(${i})" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 cursor-pointer">${i}</button>`;
        }
    }

    html += `<button type="button" onclick="changePage(${page + 1})" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-xs font-medium ${page === totalPages ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}">Sau<i class="fa-solid fa-chevron-right ml-1"></i></button>`;
    container.innerHTML = html;
}

function changePage(p) {
    currentPage = p;
    renderTable();
}

function attachInputEvents() {
    document.querySelectorAll('.sl-tot-input, .sl-hong-input').forEach(function(input) {
        input.addEventListener('input', function() {
            var maTon = this.getAttribute('data-maton');
            var type = this.getAttribute('data-type');
            var rawVal = this.value.trim();

            var item = inventoryData.find(function(el) { return el.maTonKho === maTon; });
            if (!item) return;

            if (type === 'tot') {
                if (rawVal === '') {
                    item.slTot = null;
                } else {
                    var v = parseFloat(rawVal);
                    item.slTot = (isNaN(v) || v < 0) ? 0 : v;
                }
            } else if (type === 'hong') {
                if (rawVal === '') {
                    item.slHong = 0;
                } else {
                    var v = parseFloat(rawVal);
                    item.slHong = (isNaN(v) || v < 0) ? 0 : v;
                }
            }

            var tr = input.closest('tr');
            if (!tr) return;
            var cells = tr.querySelectorAll('td');
            if (cells.length < 8) return;

            var hasTot = (item.slTot !== null && item.slTot !== '');
            if (!hasTot) {
                cells[6].innerHTML = '<span class="text-slate-400 font-normal italic">—</span>';
                cells[7].innerHTML = '<span class="inline-flex items-center gap-1.5 text-xs text-amber-600 font-medium"><i class="fa-regular fa-clock"></i> Chờ kiểm đếm</span>';
            } else {
                var totalThucTe = Number(item.slTot) + Number(item.slHong || 0);
                var chenhLech = totalThucTe - item.tonSoSach;
                cells[6].innerHTML = '<span class="font-bold text-slate-900">' + totalThucTe.toLocaleString() + ' ' + item.donViTinh + '</span>';
                if (chenhLech === 0) {
                    cells[7].innerHTML = '<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>0 (Khớp)</span>';
                } else if (chenhLech > 0) {
                    cells[7].innerHTML = '<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600"><span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>+' + chenhLech.toLocaleString() + ' (Thừa)</span>';
                } else {
                    cells[7].innerHTML = '<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600"><span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>' + chenhLech.toLocaleString() + ' (Hao hụt)</span>';
                }
            }
        });
    });
}

// Bắt sự kiện bấm nút "Xác nhận lô này" (Nút xanh dương chữ trắng)
function attachConfirmEvents() {
    document.querySelectorAll('.btn-xac-nhan-lo').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var maTon = this.getAttribute('data-maton');
            var item = inventoryData.find(function(el) { return el.maTonKho === maTon; });
            if (!item) return;

            if (item.slTot === null || item.slTot === '') {
                showToast('Vui lòng nhập Số lượng tốt thực tế cho lô ' + item.maLo + ' trước khi bấm xác nhận!', 'error');
                return;
            }

            var btnElement = this;
            var originalText = btnElement.innerHTML;
            btnElement.disabled = true;
            btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

            var formData = new URLSearchParams();
            formData.append('action', 'xacNhanLo');
            formData.append('maDotKiemKe', maDotHienTai);
            formData.append('maLo', item.maLo);
            formData.append('maViTri', item.maViTri);
            formData.append('slTot', item.slTot);
            formData.append('slHong', (item.slHong !== null && item.slHong !== '') ? item.slHong : 0);
            formData.append('csrf_token', csrfToken);

            fetch('index.php?page=vBienBanKiemKe&action=xacNhanLo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
            .then(function(res) { return res.json(); })
            .then(function(res) {
                if (res.success) {
                    item.daKiem = true;
                    btnElement.className = 'btn-xac-nhan-lo px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm flex items-center justify-center gap-1.5 cursor-pointer transition-all mx-auto';
                    btnElement.innerHTML = '<i class="fa-solid fa-check-double"></i> Đã lưu CSDL';
                    updateProgressBar();
                    setTimeout(function() {
                        renderTable();
                    }, 400);
                } else {
                    showToast('Lỗi: ' + res.message, 'error');
                    btnElement.disabled = false;
                    btnElement.innerHTML = originalText;
                }
            })
            .catch(function(err) {
                console.error(err);
                showToast('Đã xảy ra lỗi kết nối khi lưu lô hàng!', 'error');
                btnElement.disabled = false;
                btnElement.innerHTML = originalText;
            });
        });
    });
}

// Tìm kiếm nhanh theo ô input
var searchInputEl = document.getElementById('searchInput');
if (searchInputEl) {
    searchInputEl.addEventListener('input', function() {
        currentPage = 1;
        renderTable();
    });
}

// Bắt sự kiện bấm nút "Hoàn tất đợt kiểm kê"
var btnHoanTat = document.getElementById('btnHoanTat');
if (btnHoanTat && !isCompleted) {
    btnHoanTat.addEventListener('click', function() {
        if (inventoryData.length === 0) {
            showToast('Không có mặt hàng nào để hoàn tất kiểm kê!', 'error');
            return;
        }

        var uncountedLots = inventoryData.filter(function(i) { return i.slTot === null || i.slTot === ''; });
        if (uncountedLots.length > 0) {
            showToast('Không thể hoàn tất! Còn ' + uncountedLots.length + ' lô chưa nhập số lượng tốt thực tế.', 'error');
            return;
        }

        var unconfirmedLots = inventoryData.filter(function(i) { return !i.daKiem; });
        var confirmMsg = 'Bạn có chắc chắn muốn CHỐT HOÀN TẤT đợt kiểm kê này không?\n\n';
        if (unconfirmedLots.length > 0) {
            confirmMsg += 'Toàn bộ số lượng thực tế bạn đã kiểm đếm trên màn hình sẽ được chốt và lưu chính thức vào CSDL.\n\n';
        }
        confirmMsg += 'Sau khi chốt hoàn tất, biên bản sẽ được khóa sổ để Quản lý kho xử lý chênh lệch.';

        if (!confirm(confirmMsg)) {
            return;
        }

        var formData = new URLSearchParams();
        formData.append('action', 'hoanTat');
        formData.append('maDotKiemKe', maDotHienTai);
        formData.append('items', JSON.stringify(inventoryData));
        formData.append('csrf_token', csrfToken);

        btnHoanTat.disabled = true;
        btnHoanTat.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang chốt biên bản...';

        fetch('index.php?page=vBienBanKiemKe&action=hoanTat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(function(res) { return res.json(); })
        .then(function(res) {
            if (res.success) {
                showToast(res.message, 'success');
                setTimeout(function() {
                    location.reload();
                }, 800);
            } else {
                showToast('Lỗi: ' + res.message, 'error');
                btnHoanTat.disabled = false;
                btnHoanTat.innerHTML = '<i class="fa-solid fa-file-circle-check"></i> Hoàn tất đợt kiểm kê';
            }
        })
        .catch(function(err) {
            console.error(err);
            showToast('Đã xảy ra lỗi kết nối khi chốt biên bản kiểm kê!', 'error');
            btnHoanTat.disabled = false;
            btnHoanTat.innerHTML = '<i class="fa-solid fa-file-circle-check"></i> Hoàn tất đợt kiểm kê';
        });
    });
}

// Khởi tạo bảng ban đầu
renderTable();
</script>
