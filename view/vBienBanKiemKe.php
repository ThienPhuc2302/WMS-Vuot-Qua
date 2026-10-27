<?php
$currentLoai = $_GET['loai'] ?? 'NL';
$currentLoai = strtoupper($currentLoai) === 'TP' ? 'TP' : 'NL';

$danhSachDot = isset($danhSachDot) ? $danhSachDot : [];
$currentDot = isset($currentDot) ? $currentDot : [];
$itemsFromDB = isset($kiemKeItems) ? $kiemKeItems : [];

$maDotHienTai = $currentDot['maDotKiemKe'] ?? ('DOT-KK-' . $currentLoai . '-' . date('mY'));
$tenKhoHienTai = ($currentDot['tenKho'] ?? 'Kho') . ' (' . ($currentDot['maKho'] ?? 'KHO') . ')';
$trangThaiDot = trim($currentDot['trangThai'] ?? 'Đang kiểm đếm');

$isCompleted = (strcasecmp($trangThaiDot, 'Đã hoàn tất') === 0 || strpos($trangThaiDot, 'Hoàn tất') !== false);
?>

<style>
@media print {
    @page { size: A4 landscape; margin: 10mm; }
    body * { visibility: hidden; }
    #printableArea, #printableArea * { visibility: visible; }
    #printableArea { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; background: white !important; }
    .no-print, input, button { display: none !important; }
    table { width: 100% !important; border-collapse: collapse !important; }
    th, td { padding: 6px 8px !important; font-size: 11px !important; }
    td input[type="number"] { border: none !important; background: transparent !important; text-align: right; font-weight: bold; padding: 0; }
}
</style>

<div id="printableArea" class="max-w-7xl mx-auto space-y-5 pb-0">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Lập biên bản kiểm kê</h1>
            <p class="text-sm text-slate-500 mt-1">Ghi nhận số lượng kiểm đếm thực tế tại kho và đối chiếu chênh lệch sổ sách</p>
        </div>

        <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-200 shadow-sm text-xs no-print">
            <a href="index.php?page=vBienBanKiemKe&loai=NL" class="px-3.5 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-all <?= ($currentLoai === 'NL') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
                Kiểm kê nguyên liệu
            </a>
            <a href="index.php?page=vBienBanKiemKe&loai=TP" class="px-3.5 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-all <?= ($currentLoai === 'TP') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
                Kiểm kê thành phẩm
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-slate-500 uppercase font-semibold">Mã đợt kiểm kê</span>
                <i class="fa-solid fa-clipboard-list text-amber-600 text-base"></i>
            </div>
            <div class="mt-1 flex items-center justify-between">
                <strong class="text-base text-slate-900 truncate"><?= $maDotHienTai ?></strong>
                <?php if (!empty($danhSachDot)): ?>
                    <select onchange="location.href='index.php?page=vBienBanKiemKe&loai=<?= $currentLoai ?>&madot=' + this.value" class="text-xs border border-slate-300 rounded px-1.5 py-1 bg-slate-50 text-slate-700 font-medium focus:outline-none no-print">
                        <?php foreach ($danhSachDot as $dot): ?>
                            <option value="<?= $dot['maDotKiemKe'] ?>" <?= ($dot['maDotKiemKe'] === $maDotHienTai) ? 'selected' : '' ?>>
                                <?= $dot['maDotKiemKe'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-slate-500 uppercase font-semibold">Kho thực hiện</span>
                <i class="fa-solid fa-warehouse text-amber-600 text-base"></i>
            </div>
            <strong class="text-base text-slate-900 block truncate" title="<?= $tenKhoHienTai ?>"><?= $tenKhoHienTai ?></strong>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-slate-500 uppercase font-semibold">Trạng thái biên bản</span>
                <i class="fa-solid fa-circle-info text-amber-600 text-base"></i>
            </div>
            <div class="mt-1">
                <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap">
                    <span class="w-2.5 h-2.5 rounded-full <?= $isCompleted ? 'bg-emerald-500' : 'bg-amber-500' ?> shrink-0"></span><?= $trangThaiDot ?>
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH MẶT HÀNG KIỂM KÊ CHI TIẾT</h2>
            </div>
            
            <div class="relative w-full sm:w-72 no-print">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="searchInput" placeholder="Tìm mã lô, mặt hàng, vị trí..." 
                    class="w-full pl-8 pr-7 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3.5 px-6">Mã lô</th>
                        <th class="py-3.5 px-4">Tên hàng hóa</th>
                        <th class="py-3.5 px-4">Vị trí lưu trữ</th>
                        <th class="py-3.5 px-4 text-right">Tồn sổ sách</th>
                        <th class="py-3.5 px-4 text-right">SL tốt thực tế</th>
                        <th class="py-3.5 px-4 text-right">SL hỏng thực tế</th>
                        <th class="py-3.5 px-4 text-right">Tổng thực tế</th>
                        <th class="py-3.5 px-6 text-right">Chênh lệch</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="kiemKeTableBody"></tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-1.5 no-print" id="paginationContainer"></div>

            <div class="flex items-center gap-3 ml-auto no-print">
                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>In biên bản</span>
                </button>
                <button type="button" id="btnHoanTat" <?= $isCompleted ? 'disabled' : '' ?> 
                    class="px-5 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 <?= $isCompleted ? 'bg-slate-300 text-slate-500 cursor-not-allowed shadow-none' : 'bg-amber-600 hover:bg-amber-700 text-white shadow-sm cursor-pointer' ?>">
                    <i class="fa-solid fa-check"></i>
                    <span><?= $isCompleted ? 'Đã hoàn tất kiểm kê' : 'Hoàn tất kiểm kê' ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var rawItems = <?= json_encode($itemsFromDB, JSON_UNESCAPED_UNICODE) ?>;
var isCompleted = <?= $isCompleted ? 'true' : 'false' ?>;
var currentPage = 1;
var pageSize = 5;

var inventoryData = rawItems.map(function(item) {
    var ton = Number(item.tonSoSach) || 0;
    return {
        maTonKho: item.maTonKho,
        maLo: item.maLo,
        maViTri: item.maViTri,
        maHang: item.maHang,
        tenHang: item.tenHang,
        donViTinh: item.donViTinh || '',
        viTri: (item.maKho || '') + ' / Dãy ' + (item.day || '') + ' • Kệ ' + (item.ke || '') + ' • Tầng ' + (item.tang || ''),
        tonSoSach: ton,
        slTot: ton,
        slHong: 0
    };
});

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
        tbody.innerHTML = '<tr><td colspan="8" class="py-6 text-center text-slate-500 text-xs">Không tìm thấy mặt hàng kiểm kê phù hợp.</td></tr>';
        renderPagination(0, 1);
        return;
    }

    paginatedItems.forEach(function(item) {
        var totalThucTe = item.slTot + item.slHong;
        var chenhLech = totalThucTe - item.tonSoSach;
        
        var badgeHTML = '';
        if (chenhLech === 0) {
            badgeHTML = '<span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-800"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>0 (Khớp)</span>';
        } else if (chenhLech > 0) {
            badgeHTML = '<span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-800"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>+' + chenhLech.toLocaleString() + ' (Thừa)</span>';
        } else {
            badgeHTML = '<span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-800"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>' + chenhLech.toLocaleString() + ' (Hao hụt)</span>';
        }

        var disabledAttr = isCompleted ? 'disabled' : '';
        var inputBgStyle = isCompleted ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-900';

        var tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50';
        tr.innerHTML = `
            <td class="py-3.5 px-6 font-semibold text-slate-900">${item.maLo}</td>
            <td class="py-3.5 px-4 font-medium text-slate-900">${item.tenHang}</td>
            <td class="py-3.5 px-4 text-slate-600">${item.viTri}</td>
            <td class="py-3.5 px-4 text-right font-semibold">${item.tonSoSach.toLocaleString()} ${item.donViTinh}</td>
            <td class="py-3.5 px-4 text-right">
                <input type="number" min="0" value="${item.slTot}" data-maton="${item.maTonKho}" data-type="tot" ${disabledAttr} class="w-24 text-right px-2.5 py-1.5 border border-slate-300 rounded-lg ${inputBgStyle} font-semibold text-xs focus:outline-none focus:ring-1 focus:ring-amber-500 sl-tot-input">
            </td>
            <td class="py-3.5 px-4 text-right">
                <input type="number" min="0" value="${item.slHong}" data-maton="${item.maTonKho}" data-type="hong" ${disabledAttr} class="w-20 text-right px-2.5 py-1.5 border border-slate-300 rounded-lg ${inputBgStyle} ${item.slHong > 0 ? 'text-rose-600 font-bold' : ''} text-xs focus:outline-none focus:ring-1 focus:ring-amber-500 sl-hong-input">
            </td>
            <td class="py-3.5 px-4 text-right font-bold text-slate-900">${totalThucTe.toLocaleString()} ${item.donViTinh}</td>
            <td class="py-3.5 px-6 text-right">${badgeHTML}</td>
        `;
        tbody.appendChild(tr);
    });

    renderPagination(totalPages, currentPage);
    if (!isCompleted) attachInputEvents();
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
            var val = parseFloat(this.value) || 0;

            var item = inventoryData.find(function(el) { return el.maTonKho === maTon; });
            if (item) {
                if (type === 'tot') item.slTot = val;
                if (type === 'hong') item.slHong = val;
                renderTable();
            }
        });
    });
}

var searchInputEl = document.getElementById('searchInput');
if (searchInputEl) {
    searchInputEl.addEventListener('input', function() {
        currentPage = 1;
        renderTable();
    });
}

var btnHoanTat = document.getElementById('btnHoanTat');
if (btnHoanTat && !isCompleted) {
    btnHoanTat.addEventListener('click', function() {
        if (inventoryData.length === 0) {
            alert('Không có mặt hàng nào để kiểm kê!');
            return;
        }

        if (!confirm('Bạn có chắc chắn muốn hoàn tất biên bản kiểm kê và gửi chờ Ban Giám đốc phê duyệt không?')) {
            return;
        }

        var formData = new URLSearchParams();
        formData.append('action', 'save');
        formData.append('maDotKiemKe', '<?= $maDotHienTai ?>');
        formData.append('items', JSON.stringify(inventoryData));
        formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');

        fetch('index.php?page=vBienBanKiemKe&action=save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(function(res) { return res.json(); })
        .then(function(res) {
            if (res.success) {
                alert(res.message);
                location.reload();
            } else {
                alert('Lỗi: ' + res.message);
            }
        })
        .catch(function(err) {
            console.error(err);
            alert('Đã xảy ra lỗi kết nối khi lưu biên bản!');
        });
    });
}

renderTable();
</script>