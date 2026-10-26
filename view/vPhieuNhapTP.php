<?php
/**
 * VIEW: vPhieuNhapTP.php
 * Use Case: UC 3.4 - Lập phiếu nhập thành phẩm
 * Phụ trách: Nguyễn Hữu Lộc (Giao diện đồng bộ Master chuẩn ERP)
 */
/** @var array $orders Danh sách lệnh điều phối truyền từ Controller cPhieuNhapTP */
/** @var array $shelves Danh sách vị trí kệ truyền từ Controller cPhieuNhapTP */
$ordersFromDB = $orders;
$shelvesFromDB = $shelves;
?>
<!-- Container hiển thị chức năng -->
<div id="viewWorkspaceContainer"></div>

<script>
function renderStatusDot(status) {
    if (!status) return '';
    var s = String(status).trim();

    // Bộ 3 màu trạng thái vị trí kệ: Chỉ tô màu cái ô tròn (chấm trạng thái), giữ màu chữ xám đen chuẩn ERP
    if (s === 'Trống' || s === 'Còn trống') {
        return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-sky-500 shrink-0"></span>' + s + '</span>';
    }
    if (s === 'Đang chứa') {
        return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>' + s + '</span>';
    }
    if (s === 'Đầy') {
        return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-slate-500 shrink-0"></span>' + s + '</span>';
    }

    if (s === 'Đạt' || s === 'Đã hoàn thành' || s === 'Đã tiếp nhận' || s === 'Đã điều phối' || s === 'Hoàn tất nhập kho' || s === 'Hoàn tất' || s === 'Đã nhập kho' || s === '0 (Khớp)') {
        return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>' + s + '</span>';
    }
    if (s === 'Chờ kiểm định' || s === 'Chờ điều phối' || s === 'Chờ tiếp nhận' || s === 'Chờ xuất kho' || s === 'Chờ nhập kho' || s === 'Đang kiểm kê' || s === 'Đang kiểm đếm' || s === 'Khả dụng' || s.indexOf('Chờ nhập') !== -1) {
        return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>' + s + '</span>';
    }
    if (s === 'Không đạt' || s === 'Hỏng' || s === 'Lỗi' || s.indexOf('Hao hụt') !== -1 || s.startsWith('+') || s.startsWith('-')) {
        return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>' + s + '</span>';
    }
    return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-slate-400 shrink-0"></span>' + s + '</span>';
}

// Chuyển đổi mã kệ kỹ thuật thành Dãy - Kệ - Tầng dễ nhìn
function formatShelfLocation(code) {
    if (!code) return '';
    var str = String(code).trim();
    var m = str.match(/^([A-Z0-9]+)-K(\d+)-T(\d+)$/i);
    if (m) {
        var day = m[1];
        var ke = parseInt(m[2], 10);
        var tang = parseInt(m[3], 10);
        return '<span class="font-bold text-slate-900">Dãy ' + day + ' • Kệ ' + (ke < 10 ? '0' + ke : ke) + ' • Tầng ' + tang + '</span>';
    }
    if (str.indexOf('/') !== -1) {
        str = str.split('/')[1].trim();
    }
    str = str.replace(/-/g, '•');
    return '<span class="font-bold text-slate-900">' + str + '</span>';
}

// Thuật toán sắp xếp vị trí kệ: Ưu tiên tự động các vị trí còn trống lên đầu, vị trí đang chứa đẩy xuống dưới
function sortShelvesByAvailability(shelves) {
    if (!shelves || !Array.isArray(shelves)) return [];
    return shelves.slice().sort(function(a, b) {
        var aEmpty = (a.trangThai === 'Trống' || a.trangThai === 'Còn trống');
        var bEmpty = (b.trangThai === 'Trống' || b.trangThai === 'Còn trống');
        if (aEmpty && !bEmpty) return -1;
        if (!aEmpty && bEmpty) return 1;
        return String(a.maViTri).localeCompare(String(b.maViTri));
    });
}

// Thuật toán sắp xếp lệnh điều phối: Ưu tiên tự động các lệnh 'Chờ nhập kho' lên đầu, đơn đã hoàn tất đẩy xuống dưới
function sortInboundOrdersByPriority(orders) {
    if (!orders || !Array.isArray(orders)) return [];
    return orders.slice().sort(function(a, b) {
        var aPending = (a.trangThai === 'Chờ nhập kho' || a.trangThai === 'Chờ tiếp nhận');
        var bPending = (b.trangThai === 'Chờ nhập kho' || b.trangThai === 'Chờ tiếp nhận');
        if (aPending && !bPending) return -1;
        if (!aPending && bPending) return 1;
        return String(a.maLenhDP).localeCompare(String(b.maLenhDP));
    });
}

// Nạp dữ liệu từ CSDL vào đối tượng wmsData
var initialDBOrders = <?= json_encode($ordersFromDB, JSON_UNESCAPED_UNICODE) ?>;
var initialDBShelves = <?= json_encode($shelvesFromDB, JSON_UNESCAPED_UNICODE) ?>;

// Khởi tạo danh sách kệ khả dụng từ MySQL (Ưu tiên kệ trống lên đầu)
var defaultShelves = sortShelvesByAvailability((initialDBShelves && initialDBShelves.length > 0) ? initialDBShelves.map(function(s) {
    var capStr = '';
    var capNum = (s.sucChuaKhaDung !== null && s.sucChuaKhaDung !== undefined && s.sucChuaKhaDung !== '') ? Number(s.sucChuaKhaDung) : 0;
    if (capNum > 0) {
        capStr = capNum + ' Thùng';
    } else {
        capStr = 'Chưa xác định';
    }
    return {
        maViTri: s.maViTri,
        maKho: s.maKho || '',
        tenKho: s.tenKho || '',
        sucChua: capStr,
        sucChuaSo: capNum,
        trangThai: s.trangThaiSuDung || 'Chưa xác định',
        trangThaiChuaHang: s.trangThaiChuaHang || 'Khả dụng'
    };
}) : []);

// Khởi tạo danh sách lệnh nhập thành phẩm từ CSDL
if (initialDBOrders && initialDBOrders.length > 0) {
    wmsData.finishedInboundOrders = initialDBOrders.map(function(item, idx) {
        var defaultPNK = item.maPNK || '';
        var isOrderDone = (item.trangThai === 'Hoàn tất nhập kho' || item.trangThai === 'Hoàn tất nhập kho - Hao hụt' || item.trangThai === 'Hoàn tất');
        
        // Vị trí lưu kho:
        // - Với lệnh ĐÃ HOÀN TẤT: lấy đúng mã kệ đã lưu từ CSDL (item.viTriDaNhap).
        // - Với lệnh CHƯA HOÀN TẤT: để trống (''), bắt buộc thủ kho phải tự chọn kệ trống từ danh sách!
        var defaultShelf = isOrderDone ? (item.viTriDaNhap || '') : '';
        // Số lượng thực nhận:
        // - Với lệnh ĐÃ HOÀN TẤT: lấy đúng số lượng đã nhập thực tế từ CSDL. Nếu CSDL null thì để null, không bịa số 0.
        // - Với lệnh CHƯA HOÀN TẤT: để trống (''), bắt buộc thủ kho phải kiểm đếm và tự nhập thực tế!
        var defaultSlThuc = isOrderDone 
            ? ((item.slThucNhap !== null && item.slThucNhap !== undefined && item.slThucNhap !== '') ? Number(item.slThucNhap) : null)
            : '';

        var nsxStr = item.ngaySanXuat ? item.ngaySanXuat.split('-').reverse().join('/') : null;
        var hsdStr = item.hanSuDung ? item.hanSuDung.split('-').reverse().join('/') : null;
        var tenNguoiDP = item.tenNguoiDieuPhoi || item.nguoiDieuPhoi || null;

        return {
            maLenhDP: item.maLenhDieuPhoi,
            maPhieuNhap: defaultPNK,
            maLo: item.maLo,
            tenTP: item.tenTP,
            phanXuong: item.phanXuong !== null && item.phanXuong !== undefined && item.phanXuong !== '' ? item.phanXuong : null,
            soLuongPhanBo: (item.soLuongPhanBo !== null && item.soLuongPhanBo !== undefined && item.soLuongPhanBo !== '') ? Number(item.soLuongPhanBo) : null,
            soLuongThucNhan: defaultSlThuc,
            donViTinh: item.donViTinh !== null && item.donViTinh !== undefined && item.donViTinh !== '' ? item.donViTinh : null,
            maKhoTiepNhan: item.maKhoTiepNhan || null,
            khoTiepNhan: item.tenKho !== null && item.tenKho !== undefined && item.tenKho !== '' ? item.tenKho : null,
            nsx: nsxStr,
            hsd: hsdStr,
            quyCach: item.quyCachDongGoi !== null && item.quyCachDongGoi !== undefined && item.quyCachDongGoi !== '' ? item.quyCachDongGoi : null,
            nguoiDieuPhoi: tenNguoiDP,
            trangThaiQC: item.trangThaiKiemDinh !== null && item.trangThaiKiemDinh !== undefined && item.trangThaiKiemDinh !== '' ? item.trangThaiKiemDinh : null,
            trangThai: item.trangThai !== null && item.trangThai !== undefined ? item.trangThai : '',
            ghiChu: item.ghiChuNhapKho || item.lyDoGiaiTrinh || '',
            lyDoChenhLech: item.lyDoChenhLech || '',
            viTriDaChon: defaultShelf,
            viTriTrong: defaultShelves
        };
    });
    wmsData.finishedInboundOrders = sortInboundOrdersByPriority(wmsData.finishedInboundOrders);
} else {
    wmsData.finishedInboundOrders = [];
}

currentFuncId = 'inbound_finished_product';
if (typeof selectedIndex === 'undefined') {
    selectedIndex = 0;
}

let orderCurrentPage = 1;
const orderPageSize = 5;
let orderSearchKeyword = '';

let shelfCurrentPage = 1;
const shelfPageSize = 4; // 4 vị trí / trang giúp 2 khối 'Chi tiết phiếu nhập' và 'Vị trí lưu trữ' cân đối ngang bằng chuẩn xác
let selectedShelfDay = '';
let selectedShelfKe = '';
let selectedShelfTang = '';
let pauseShelfSort = false;
let cachedSortedShelves = null;

// Hàm phân tích mã kệ (VD: B1-K01-T01 -> day: B1, ke: 1, tang: 1)
function parseShelfCode(code) {
    const str = String(code || '').trim();
    const m = str.match(/^([A-Z0-9]+)-K(\d+)-T(\d+)$/i);
    if (m) {
        return {
            day: m[1].toUpperCase(),
            ke: parseInt(m[2], 10),
            tang: parseInt(m[3], 10)
        };
    }
    return { day: '', ke: 0, tang: 0 };
}

function renderCurrentView() {
    const container = document.getElementById('viewWorkspaceContainer');
    if (!container) return;

    if (!wmsData.finishedInboundOrders || wmsData.finishedInboundOrders.length === 0) {
        container.innerHTML = '<div class="p-8 text-center text-slate-500">Không có dữ liệu lệnh điều phối nhập thành phẩm.</div>';
        return;
    }

    if (selectedIndex >= wmsData.finishedInboundOrders.length) {
        selectedIndex = 0;
    }

    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    const isDone = (tpOrder.trangThai === 'Hoàn tất nhập kho' || tpOrder.trangThai === 'Hoàn tất nhập kho - Hao hụt' || tpOrder.trangThai === 'Hoàn tất');
    const isLech = (tpOrder.soLuongThucNhan !== '' && tpOrder.soLuongThucNhan !== null && Number(tpOrder.soLuongThucNhan) > 0 && tpOrder.soLuongPhanBo !== null && Number(tpOrder.soLuongThucNhan) < Number(tpOrder.soLuongPhanBo));

    // Lọc danh sách lệnh điều phối theo từ khóa tìm kiếm (Khoanh đỏ 1)
    const kw = orderSearchKeyword.toLowerCase().trim();
    const filteredOrders = wmsData.finishedInboundOrders.filter(function(orderItem) {
        if (!kw) return true;
        const maLenh = String(orderItem.maLenhDP || '').toLowerCase();
        const maLo = String(orderItem.maLo || '').toLowerCase();
        const tenTP = String(orderItem.tenTP || '').toLowerCase();
        const phanXuong = String(orderItem.phanXuong || '').toLowerCase();
        const trangThai = String(orderItem.trangThai || '').toLowerCase();
        return maLenh.includes(kw) || maLo.includes(kw) || tenTP.includes(kw) || phanXuong.includes(kw) || trangThai.includes(kw);
    });

    // Tính toán phân trang danh sách lệnh điều phối (5 lệnh / trang)
    const totalOrders = filteredOrders.length;
    const totalOrderPages = Math.max(1, Math.ceil(totalOrders / orderPageSize));
    if (orderCurrentPage > totalOrderPages) orderCurrentPage = totalOrderPages;
    if (orderCurrentPage < 1) orderCurrentPage = 1;
    const startOrderIdx = (orderCurrentPage - 1) * orderPageSize;
    const endOrderIdx = Math.min(startOrderIdx + orderPageSize, totalOrders);
    const displayedOrders = filteredOrders.slice(startOrderIdx, endOrderIdx);

    // Danh sách kệ khả dụng: Khi vừa lưu phiếu (pauseShelfSort = true), thuật toán sắp xếp khoan một nhịp để giữ nguyên hiển thị
    let rawShelves;
    if (pauseShelfSort && cachedSortedShelves) {
        rawShelves = cachedSortedShelves;
    } else {
        rawShelves = sortShelvesByAvailability(tpOrder.viTriTrong || defaultShelves);
        cachedSortedShelves = rawShelves;
    }
    let targetShelves = rawShelves;
    if (tpOrder.maKhoTiepNhan) {
        const khoFiltered = rawShelves.filter(s => !s.maKho || s.maKho === tpOrder.maKhoTiepNhan);
        if (khoFiltered.length > 0) {
            targetShelves = khoFiltered;
        }
    }

    // Trích xuất các Dãy, Kệ, Tầng duy nhất từ dữ liệu kệ khả dụng để đổ vào Selection (Khoanh đỏ 2)
    const uniqueDays = [];
    const uniqueKes = [];
    const uniqueTangs = [];
    targetShelves.forEach(s => {
        const p = parseShelfCode(s.maViTri);
        if (p.day && !uniqueDays.includes(p.day)) uniqueDays.push(p.day);
        if (p.ke && !uniqueKes.includes(p.ke)) uniqueKes.push(p.ke);
        if (p.tang && !uniqueTangs.includes(p.tang)) uniqueTangs.push(p.tang);
    });
    uniqueDays.sort();
    uniqueKes.sort((a,b) => a - b);
    uniqueTangs.sort((a,b) => a - b);

    // Lọc danh sách vị trí kệ theo Selection (Dãy, Kệ, Tầng)
    const allShelves = targetShelves.filter(s => {
        const p = parseShelfCode(s.maViTri);
        if (selectedShelfDay && p.day !== selectedShelfDay) return false;
        if (selectedShelfKe && String(p.ke) !== selectedShelfKe) return false;
        if (selectedShelfTang && String(p.tang) !== selectedShelfTang) return false;
        return true;
    });

    const totalShelves = allShelves.length;
    const totalShelfPages = Math.max(1, Math.ceil(totalShelves / shelfPageSize));
    if (shelfCurrentPage > totalShelfPages) shelfCurrentPage = totalShelfPages;
    if (shelfCurrentPage < 1) shelfCurrentPage = 1;
    const startShelfIdx = (shelfCurrentPage - 1) * shelfPageSize;
    const endShelfIdx = Math.min(startShelfIdx + shelfPageSize, totalShelves);
    const displayedShelves = allShelves.slice(startShelfIdx, endShelfIdx);
    const hasShelfFilter = !!(selectedShelfDay || selectedShelfKe || selectedShelfTang);

    container.innerHTML = `
<div class="max-w-7xl mx-auto space-y-5 pb-0">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Lập phiếu nhập thành phẩm</h1>
        <p class="text-sm text-slate-500 mt-1">Tiếp nhận lô thành phẩm từ phân xưởng theo lệnh điều phối và lưu vị trí kệ</p>
    </div>

    <!-- Danh sách lệnh điều phối thành phẩm (Phân trang 5 lệnh / trang & Thanh tìm kiếm) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH LỆNH ĐIỀU PHỐI TIẾP NHẬN THÀNH PHẨM</h2>
            </div>
            
            <!-- Thanh tìm kiếm lệnh điều phối (Khoanh đỏ 1) -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="orderSearchInput" oninput="handleOrderSearch(this.value)" value="${orderSearchKeyword || ''}"
                    placeholder="Tìm mã lệnh, mã lô, bánh..." 
                    class="w-full pl-8 pr-7 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 transition">
                ${orderSearchKeyword ? `
                    <button type="button" onclick="clearOrderSearch()" title="Xóa tìm kiếm" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                ` : ''}
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse erp-table">
                <thead>
                    <tr>
                        <th class="py-3.5 px-6">Mã lệnh ĐP</th>
                        <th class="py-3.5 px-4">Mã lô</th>
                        <th class="py-3.5 px-4">Tên thành phẩm</th>
                        <th class="py-3.5 px-4">Phân xưởng</th>
                        <th class="py-3.5 px-4 text-right">Số lượng phân bổ</th>
                        <th class="py-3.5 px-6">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    ${displayedOrders.length === 0 ? `
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 text-sm">
                                <i class="fa-solid fa-magnifying-glass text-slate-300 text-xl mb-2 block"></i>
                                Không tìm thấy lệnh điều phối nào phù hợp với từ khóa "<strong class="text-slate-700">${orderSearchKeyword}</strong>".
                                <button type="button" onclick="clearOrderSearch()" class="text-amber-600 underline font-medium ml-1.5 hover:text-amber-700 cursor-pointer">
                                    Xóa tìm kiếm
                                </button>
                            </td>
                        </tr>
                    ` : displayedOrders.map(function(orderItem) {
                        const globalIdx = wmsData.finishedInboundOrders.indexOf(orderItem);
                        return `
                        <tr onclick="selectListItem(${globalIdx})" class="cursor-pointer transition ${globalIdx === selectedIndex ? 'bg-slate-100 font-medium' : 'hover:bg-slate-50'}">
                            <td class="py-3.5 px-6 font-semibold text-slate-900">${orderItem.maLenhDP}</td>
                            <td class="py-3.5 px-4">${orderItem.maLo}</td>
                            <td class="py-3.5 px-4">${orderItem.tenTP}</td>
                            <td class="py-3.5 px-4 text-slate-600">${orderItem.phanXuong ? orderItem.phanXuong : '<span class="text-slate-400 italic">Chưa xác định</span>'}</td>
                            <td class="py-3.5 px-4 text-right font-semibold">${orderItem.soLuongPhanBo !== null ? Number(orderItem.soLuongPhanBo).toLocaleString('vi-VN') + (orderItem.donViTinh ? ' ' + orderItem.donViTinh : '') : '<span class="text-rose-600 italic">Thiếu CSDL</span>'}</td>
                            <td class="py-3.5 px-6">
                                ${renderStatusDot(orderItem.trangThai)}
                            </td>
                        </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>

        <!-- Phân trang bảng lệnh điều phối (Đã bỏ dòng chữ hiển thị theo yêu cầu đánh dấu X) -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="changeOrderPage(${orderCurrentPage - 1})" ${orderCurrentPage === 1 ? 'disabled class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 bg-white cursor-not-allowed text-xs font-medium"' : 'class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-xs font-medium cursor-pointer transition"'}>
                    <i class="fa-solid fa-chevron-left mr-1"></i>Trước
                </button>
                ${Array.from({length: totalOrderPages}, (_, p) => p + 1).map(p => `
                    <button type="button" onclick="changeOrderPage(${p})" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center transition cursor-pointer ${p === orderCurrentPage ? 'bg-amber-600 text-white shadow-sm' : 'border border-slate-300 text-slate-700 bg-white hover:bg-slate-100'}">
                        ${p}
                    </button>
                `).join('')}
                <button type="button" onclick="changeOrderPage(${orderCurrentPage + 1})" ${orderCurrentPage === totalOrderPages ? 'disabled class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 bg-white cursor-not-allowed text-xs font-medium"' : 'class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-xs font-medium cursor-pointer transition"'}>
                    Sau<i class="fa-solid fa-chevron-right ml-1"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- 2 Cột: Chi tiết phiếu nhập (7 cột) & Chọn vị trí kệ (5 cột) - 2 bảng nhìn ngang bằng nhau chuẩn xác -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Cột Trái: Chi Tiết Phiếu Nhập Thành Phẩm -->
        <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 gap-2">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-file-invoice text-amber-600 text-lg"></i>
                        <h3 class="text-base font-bold text-slate-900 whitespace-nowrap">Chi Tiết Phiếu Nhập Thành Phẩm</h3>
                    </div>
                    ${isDone && tpOrder.maPhieuNhap ? `<span class="px-2.5 py-1 rounded-md bg-slate-100 text-xs font-semibold text-slate-700 tracking-wider">${tpOrder.maPhieuNhap}</span>` : ''}
                </div>
                
                <!-- Tất cả các trường thông tin: bố cục thanh lịch, dài xuống vừa phải -->
                <div class="grid grid-cols-2 gap-x-5 gap-y-4 text-sm pt-3.5">
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lệnh điều phối</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${tpOrder.maLenhDP}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lô thành phẩm</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${tpOrder.maLo}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Tên thành phẩm</span>
                        <strong class="text-slate-900 mt-1 block truncate" title="${tpOrder.tenTP}">${tpOrder.tenTP}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Quy cách đóng gói</span>
                        <strong class="text-slate-900 mt-1 block">${tpOrder.quyCach ? tpOrder.quyCach : '<span class="text-slate-400 italic font-normal">Chưa xác định</span>'}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kho tiếp nhận</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${tpOrder.khoTiepNhan ? tpOrder.khoTiepNhan : '<span class="text-slate-400 italic font-normal">Chưa xác định</span>'}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Hạn sử dụng</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${(tpOrder.nsx && tpOrder.hsd) ? tpOrder.nsx + ' – ' + tpOrder.hsd : (tpOrder.hsd || '<span class="text-slate-400 italic font-normal">Chưa có HSD</span>')}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Người điều phối</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${tpOrder.nguoiDieuPhoi ? tpOrder.nguoiDieuPhoi : '<span class="text-slate-400 italic font-normal">Chưa xác định</span>'}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kiểm định chất lượng</span>
                        <div class="mt-1 flex items-center gap-2">
                            ${tpOrder.trangThaiQC ? renderStatusDot(tpOrder.trangThaiQC) : '<span class="text-slate-400 italic text-sm">Chưa kiểm định</span>'}
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1.5 text-xs whitespace-nowrap">Số lượng phân bổ</label>
                        <input type="text" disabled value="${tpOrder.soLuongPhanBo !== null ? Number(tpOrder.soLuongPhanBo).toLocaleString('vi-VN') + (tpOrder.donViTinh ? ' ' + tpOrder.donViTinh : '') : 'Chưa có dữ liệu trong CSDL'}" 
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold text-sm">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Số lượng thực nhận ${tpOrder.donViTinh ? '(' + tpOrder.donViTinh + ')' : ''} (*)</label>
                        <input type="number" id="inputSoLuongThucNhan" oninput="handleQuantityChange(this.value)" ${isDone ? 'disabled' : ''} value="${tpOrder.soLuongThucNhan !== '' && tpOrder.soLuongThucNhan !== null ? tpOrder.soLuongThucNhan : ''}" placeholder="${isDone && tpOrder.soLuongThucNhan === null ? 'Chưa ghi nhận số lượng thực nhận' : 'Nhập số lượng thực nhận...'}" 
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white text-slate-900 font-bold text-sm">
                    </div>

                    <!-- Ô Nhập Lý do chênh lệch khi hàng bị hao hụt theo Use Case 3.2 Luồng 5.1 -->
                    <div id="rowLyDoChenhLech" class="${isLech ? '' : 'hidden'} col-span-2">
                        <label class="block text-rose-700 font-semibold mb-1.5 text-xs whitespace-nowrap">Lý do chênh lệch hao hụt / hàng vỡ (*)</label>
                        <input type="text" id="inputLyDoChenhLech" oninput="tpOrder.lyDoChenhLech = this.value" ${isDone ? 'disabled' : ''} value="${tpOrder.lyDoChenhLech || ''}" 
                            placeholder="Nhập lý do hàng hỏng do vận chuyển bốc dỡ..."
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white text-slate-800 text-sm">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Ghi chú nhập kho</label>
                        <input type="text" id="inputGhiChu" oninput="tpOrder.ghiChu = this.value" ${isDone ? 'disabled' : ''} value="${tpOrder.ghiChu}" 
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white text-slate-800 text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Phải: Chọn vị trí lưu trữ còn trống -->
        <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div class="flex-1 flex flex-col">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-table-cells text-amber-600 text-base"></i>
                        <h3 class="text-base font-bold text-slate-900">Chọn vị trí lưu trữ còn trống</h3>
                    </div>
                </div>

                <!-- Thanh Selection: Chọn Dãy • Kệ • Tầng • Nhấn nút Tìm kiếm (Khoanh đỏ 2) -->
                <div class="py-2.5 border-b border-slate-100 flex items-center gap-1.5">
                    <div class="grid grid-cols-3 gap-1.5 flex-1">
                        <select id="shelfFilterDay" class="py-1 px-1.5 bg-slate-50 border border-slate-300 rounded text-slate-700 text-xs focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <option value="">Tất cả dãy</option>
                            ${uniqueDays.map(d => `<option value="${d}" ${selectedShelfDay === d ? 'selected' : ''}>Dãy ${d}</option>`).join('')}
                        </select>
                        <select id="shelfFilterKe" class="py-1 px-1.5 bg-slate-50 border border-slate-300 rounded text-slate-700 text-xs focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <option value="">Tất cả kệ</option>
                            ${uniqueKes.map(k => `<option value="${k}" ${selectedShelfKe === String(k) ? 'selected' : ''}>Kệ ${k < 10 ? '0' + k : k}</option>`).join('')}
                        </select>
                        <select id="shelfFilterTang" class="py-1 px-1.5 bg-slate-50 border border-slate-300 rounded text-slate-700 text-xs focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <option value="">Tất cả tầng</option>
                            ${uniqueTangs.map(t => `<option value="${t}" ${selectedShelfTang === String(t) ? 'selected' : ''}>Tầng ${t}</option>`).join('')}
                        </select>
                    </div>
                    <button type="button" onclick="applyShelfFilter()" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs font-semibold flex items-center gap-1 transition shadow-sm cursor-pointer whitespace-nowrap">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                        <span>Tìm</span>
                    </button>
                    ${hasShelfFilter ? `
                        <button type="button" onclick="clearShelfFilter()" title="Xóa bộ lọc vị trí" class="p-1 text-slate-400 hover:text-rose-600 rounded transition cursor-pointer">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                        </button>
                    ` : ''}
                </div>

                <!-- Danh sách kệ phân trang (4 vị trí / trang) -->
                <div class="space-y-2 py-2 flex-1">
                    ${(!displayedShelves || displayedShelves.length === 0) ? `
                        <div class="p-5 text-center text-slate-500 bg-slate-50 rounded-lg border border-dashed border-slate-300 my-2">
                            <i class="fa-solid fa-filter-circle-xmark text-amber-500 text-lg mb-1.5 block"></i>
                            <span class="text-xs font-medium">Không tìm thấy vị trí kệ nào phù hợp.</span>
                            <div class="mt-1.5">
                                <button type="button" onclick="clearShelfFilter()" class="text-xs text-amber-600 hover:text-amber-700 underline font-semibold cursor-pointer">
                                    Xem tất cả vị trí
                                </button>
                            </div>
                        </div>
                    ` : displayedShelves.map(vt => {
                        const isAvailable = (vt.trangThai === 'Trống' || vt.trangThai === 'Còn trống') && (!vt.trangThaiChuaHang || vt.trangThaiChuaHang === 'Khả dụng');
                        const isSelected = (tpOrder.viTriDaChon === vt.maViTri);
                        let itemClass = 'px-3.5 py-2 rounded-lg border transition flex items-center justify-between gap-3 ';
                        if (isSelected) {
                            itemClass += 'border-brand-600 bg-amber-50/50 shadow-sm ';
                        } else if (!isAvailable && !isDone) {
                            itemClass += 'border-slate-200 bg-slate-50/70 opacity-60 cursor-not-allowed ';
                        } else {
                            itemClass += 'border-slate-200 hover:bg-slate-50 cursor-pointer ';
                        }

                        let statusBadge = renderStatusDot(vt.trangThai);
                        if (vt.trangThaiChuaHang && vt.trangThaiChuaHang !== 'Khả dụng') {
                            statusBadge += `<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-700 whitespace-nowrap ml-1">${vt.trangThaiChuaHang}</span>`;
                        }

                        return `
                        <div onclick="${(isDone || !isAvailable) ? '' : `chooseFinishedShelf('${vt.maViTri}')`}"
                            class="${itemClass}"
                            title="${!isAvailable ? 'Vị trí không khả dụng (' + vt.trangThai + (vt.trangThaiChuaHang && vt.trangThaiChuaHang !== 'Khả dụng' ? ' - ' + vt.trangThaiChuaHang : '') + ')' : 'Nhấp để chọn vị trí'}">
                            <div class="min-w-0 flex-1">
                                <div class="text-sm">${formatShelfLocation(vt.maViTri)}</div>
                                <div class="text-xs text-slate-500 mt-0.5 whitespace-nowrap">Sức chứa tối đa: ${vt.sucChua}</div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                ${statusBadge}
                                <i class="fa-solid ${isSelected ? 'fa-circle-check text-brand-600 text-lg' : 'fa-regular fa-circle text-slate-300 text-lg'}"></i>
                            </div>
                        </div>
                        `;
                    }).join('')}
                </div>

                <!-- Thanh phân trang vị trí kệ (Đã xóa dòng text theo yêu cầu) -->
                <div class="pt-2 pb-1 border-t border-slate-100 flex items-center justify-end text-xs text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="changeShelfPage(${shelfCurrentPage - 1})" ${shelfCurrentPage === 1 ? 'disabled class="px-2.5 py-1 rounded border border-slate-200 text-slate-300 bg-white cursor-not-allowed text-xs"' : 'class="px-2.5 py-1 rounded border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-xs font-medium cursor-pointer transition"'}>
                            <i class="fa-solid fa-chevron-left mr-1 text-[10px]"></i>Trước
                        </button>
                        <span class="px-2 py-1 font-semibold text-slate-700 bg-slate-100 rounded text-xs">${shelfCurrentPage} / ${totalShelfPages}</span>
                        <button type="button" onclick="changeShelfPage(${shelfCurrentPage + 1})" ${shelfCurrentPage === totalShelfPages ? 'disabled class="px-2.5 py-1 rounded border border-slate-200 text-slate-300 bg-white cursor-not-allowed text-xs"' : 'class="px-2.5 py-1 rounded border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 text-xs font-medium cursor-pointer transition"'}>
                            Sau<i class="fa-solid fa-chevron-right ml-1 text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2 Nút thao tác ở đáy: Kéo xích lên ngang bằng đáy bảng bên trái -->
            <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-3 mt-auto">
                <button type="button" onclick="window.print()"
                    class="px-4 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>In phiếu</span>
                </button>
                <button type="button" id="btnSaveFinishedInbound" onclick="saveFinishedInbound()" ${isDone ? 'disabled' : ''}
                    class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap transition cursor-pointer ${isDone ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-brand-600 hover:bg-brand-700 text-white shadow-sm'}">
                    <i class="fa-solid fa-check"></i>
                    <span>${isDone ? 'Đã lưu phiếu nhập' : 'Xác nhận lưu phiếu nhập kho'}</span>
                </button>
            </div>
        </div>
    </div>
</div>
    `;
}

// Chuyển chọn dòng lệnh điều phối (Thuật toán sắp xếp hoạt động lại khi chọn phiếu trong danh sách)
function selectListItem(idx) {
    selectedIndex = idx;
    pauseShelfSort = false;
    cachedSortedShelves = null;
    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    if (tpOrder) {
        const isDone = (tpOrder.trangThai === 'Hoàn tất nhập kho' || tpOrder.trangThai === 'Hoàn tất nhập kho - Hao hụt' || tpOrder.trangThai === 'Hoàn tất');
        if (!isDone) {
            // Kiểm tra xem vị trí đang chọn có thực sự còn trống trong CSDL không
            const currentChosen = defaultShelves.find(s => s.maViTri === tpOrder.viTriDaChon);
            const isCurrentlyEmpty = currentChosen && (currentChosen.trangThai === 'Trống' || currentChosen.trangThai === 'Còn trống');
            
            // Nếu vị trí hiện tại không hợp lệ hoặc không còn trống:
            if (!isCurrentlyEmpty) {
                // Chỉ gán nếu có kệ thực sự trống; nếu KHÔNG có kệ trống, bắt buộc để rỗng ('')
                const nextEmpty = defaultShelves.find(s => s.trangThai === 'Trống' || s.trangThai === 'Còn trống');
                tpOrder.viTriDaChon = nextEmpty ? nextEmpty.maViTri : '';
            }
        }
        // Tự động chuyển trang vị trí kệ về trang chứa vị trí đang chọn (nếu có)
        if (tpOrder.viTriDaChon) {
            const rawShelves = sortShelvesByAvailability(tpOrder.viTriTrong || []);
            let targetShelves = rawShelves;
            if (tpOrder.maKhoTiepNhan) {
                const khoFiltered = rawShelves.filter(s => !s.maKho || s.maKho === tpOrder.maKhoTiepNhan);
                if (khoFiltered.length > 0) {
                    targetShelves = khoFiltered;
                }
            }
            const allShelves = targetShelves.filter(s => {
                const p = parseShelfCode(s.maViTri);
                if (selectedShelfDay && p.day !== selectedShelfDay) return false;
                if (selectedShelfKe && String(p.ke) !== selectedShelfKe) return false;
                if (selectedShelfTang && String(p.tang) !== selectedShelfTang) return false;
                return true;
            });
            const pos = allShelves.findIndex(s => s.maViTri === tpOrder.viTriDaChon);
            if (pos !== -1) {
                shelfCurrentPage = Math.floor(pos / shelfPageSize) + 1;
            }
        }
    }
    renderCurrentView();
}

function changeOrderPage(p) {
    const kw = orderSearchKeyword.toLowerCase().trim();
    const filteredOrders = wmsData.finishedInboundOrders.filter(function(orderItem) {
        if (!kw) return true;
        const maLenh = String(orderItem.maLenhDP || '').toLowerCase();
        const maLo = String(orderItem.maLo || '').toLowerCase();
        const tenTP = String(orderItem.tenTP || '').toLowerCase();
        const phanXuong = String(orderItem.phanXuong || '').toLowerCase();
        const trangThai = String(orderItem.trangThai || '').toLowerCase();
        return maLenh.includes(kw) || maLo.includes(kw) || tenTP.includes(kw) || phanXuong.includes(kw) || trangThai.includes(kw);
    });
    const totalPages = Math.max(1, Math.ceil(filteredOrders.length / orderPageSize));
    if (p < 1 || p > totalPages) return;
    orderCurrentPage = p;
    renderCurrentView();
}

function handleOrderSearch(val) {
    orderSearchKeyword = String(val || '');
    orderCurrentPage = 1;
    renderCurrentView();
    setTimeout(() => {
        const input = document.getElementById('orderSearchInput');
        if (input) {
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }
    }, 10);
}

function clearOrderSearch() {
    orderSearchKeyword = '';
    orderCurrentPage = 1;
    renderCurrentView();
}

function applyShelfFilter() {
    const dayEl = document.getElementById('shelfFilterDay');
    const keEl = document.getElementById('shelfFilterKe');
    const tangEl = document.getElementById('shelfFilterTang');
    selectedShelfDay = dayEl ? dayEl.value : '';
    selectedShelfKe = keEl ? keEl.value : '';
    selectedShelfTang = tangEl ? tangEl.value : '';
    shelfCurrentPage = 1;
    renderCurrentView();
}

function clearShelfFilter() {
    selectedShelfDay = '';
    selectedShelfKe = '';
    selectedShelfTang = '';
    shelfCurrentPage = 1;
    renderCurrentView();
}

function changeShelfPage(p) {
    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    const rawShelves = sortShelvesByAvailability(tpOrder ? tpOrder.viTriTrong : []);
    const allShelves = rawShelves.filter(s => {
        const p = parseShelfCode(s.maViTri);
        if (selectedShelfDay && p.day !== selectedShelfDay) return false;
        if (selectedShelfKe && String(p.ke) !== selectedShelfKe) return false;
        if (selectedShelfTang && String(p.tang) !== selectedShelfTang) return false;
        return true;
    });
    const totalPages = Math.max(1, Math.ceil(allShelves.length / shelfPageSize));
    if (p < 1 || p > totalPages) return;
    shelfCurrentPage = p;
    renderCurrentView();
}

// Xử lý khi thay đổi số lượng thực nhận (Theo Use Case 3.2 Luồng 5.1 & Ngoại lệ 5.2)
function handleQuantityChange(val) {
    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    if (!tpOrder) return;
    const str = String(val).trim();
    if (str === '') {
        tpOrder.soLuongThucNhan = '';
        const rowChenhLech = document.getElementById('rowLyDoChenhLech');
        if (rowChenhLech) rowChenhLech.classList.add('hidden');
        return;
    }
    const num = Number(val);
    tpOrder.soLuongThucNhan = isNaN(num) ? '' : num;
    const rowChenhLech = document.getElementById('rowLyDoChenhLech');
    if (rowChenhLech) {
        if (!isNaN(num) && num > 0 && tpOrder.soLuongPhanBo !== null && num < tpOrder.soLuongPhanBo) {
            rowChenhLech.classList.remove('hidden');
        } else {
            rowChenhLech.classList.add('hidden');
        }
    }
}

// Chọn kệ lưu kho: CẬP NHẬT TRẠNG THÁI NGAY TỨC THÌ
function chooseFinishedShelf(maViTri) {
    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    if (!tpOrder) return;
    
    if (tpOrder.trangThai === 'Hoàn tất nhập kho' || tpOrder.trangThai === 'Hoàn tất nhập kho - Hao hụt' || tpOrder.trangThai === 'Hoàn tất') {
        return;
    }

    const targetShelf = defaultShelves.find(s => s.maViTri === maViTri);
    if (!targetShelf) {
        showToast('Vị trí ' + maViTri + ' không tồn tại trong danh sách kệ CSDL!', 'error');
        return;
    }

    // CHẶN TẤT CẢ CÁC TRẠNG THÁI KHÔNG PHẢI LÀ "Trống" hoặc "Còn trống"
    // Bao gồm: "Đang chứa", "Đầy", "Chưa xác định", "Bảo trì", v.v.
    const isAvailable = (targetShelf.trangThai === 'Trống' || targetShelf.trangThai === 'Còn trống');
    if (!isAvailable) {
        let msg = 'Vị trí ' + maViTri + ' không khả dụng (' + targetShelf.trangThai + ')!';
        if (targetShelf.trangThai === 'Đầy') {
            msg = 'Vị trí ' + maViTri + ' đã đầy!';
        } else if (targetShelf.trangThai === 'Đang chứa') {
            msg = 'Vị trí ' + maViTri + ' đang chứa hàng!';
        }
        showToast(msg + ' Vui lòng chọn vị trí kệ còn trống.', 'error');
        return;
    }

    tpOrder.viTriDaChon = maViTri;
    renderCurrentView();
}

// Xác nhận lưu phiếu nhập kho: KẾT NỐI CONTROLLER & MODEL CƠ SỞ DỮ LIỆU
async function saveFinishedInbound() {
    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    if (!tpOrder) return;
    if (tpOrder.trangThai === 'Hoàn tất nhập kho' || tpOrder.trangThai === 'Hoàn tất nhập kho - Hao hụt' || tpOrder.trangThai === 'Hoàn tất') return;

    if (!tpOrder.viTriDaChon) {
        showToast('Vui lòng chọn vị trí lưu kho (kệ còn trống)!', 'error');
        return;
    }

    // Kiểm tra vị trí kệ đã chọn có thực sự còn trống không
    const chosenShelf = defaultShelves.find(s => s.maViTri === tpOrder.viTriDaChon);
    if (!chosenShelf || (chosenShelf.trangThai !== 'Trống' && chosenShelf.trangThai !== 'Còn trống')) {
        const shelfStatus = chosenShelf ? chosenShelf.trangThai : 'Không tồn tại';
        showToast('Vị trí ' + tpOrder.viTriDaChon + ' không khả dụng (' + shelfStatus + ')! Vui lòng chọn vị trí kệ còn trống.', 'error');
        return;
    }

    // [LỖI 1]: Chặn chọn kệ sai kho tiếp nhận
    if (tpOrder.maKhoTiepNhan && chosenShelf.maKho && chosenShelf.maKho !== tpOrder.maKhoTiepNhan) {
        showToast('Vị trí ' + tpOrder.viTriDaChon + ' thuộc kho "' + chosenShelf.maKho + '", không khớp với kho tiếp nhận của lệnh ("' + tpOrder.maKhoTiepNhan + '")!', 'error');
        return;
    }

    // [LỖI 3]: Chặn vị trí kệ đang bị Khóa hoặc Bảo trì
    if (chosenShelf.trangThaiChuaHang && chosenShelf.trangThaiChuaHang !== 'Khả dụng') {
        showToast('Vị trí ' + tpOrder.viTriDaChon + ' đang ở trạng thái "' + chosenShelf.trangThaiChuaHang + '"! Không thể lưu hàng.', 'error');
        return;
    }

    const rawSlInput = document.getElementById('inputSoLuongThucNhan')?.value;
    if (rawSlInput === undefined || rawSlInput === null || String(rawSlInput).trim() === '') {
        showToast('Vui lòng nhập số lượng thực nhận!', 'error');
        document.getElementById('inputSoLuongThucNhan')?.focus();
        return;
    }

    const slThuc = Number(rawSlInput);
    if (isNaN(slThuc) || slThuc <= 0) {
        showToast('Số lượng thực nhận phải là số lớn hơn 0!', 'error');
        document.getElementById('inputSoLuongThucNhan')?.focus();
        return;
    }

    // [LỖI 4]: Chặn nhập số lượng vượt quá sức chứa tối đa của kệ
    if (chosenShelf.sucChuaSo > 0 && slThuc > chosenShelf.sucChuaSo) {
        showToast('Số lượng thực nhận (' + slThuc.toLocaleString('vi-VN') + ') vượt quá sức chứa tối đa của vị trí ' + tpOrder.viTriDaChon + ' (' + chosenShelf.sucChuaSo.toLocaleString('vi-VN') + ' Thùng)!', 'error');
        document.getElementById('inputSoLuongThucNhan')?.focus();
        return;
    }

    if (tpOrder.soLuongPhanBo === null) {
        showToast('Lỗi dữ liệu CSDL: Lệnh điều phối này chưa có số lượng phân bổ!', 'error');
        return;
    }

    if (slThuc > tpOrder.soLuongPhanBo) {
        showToast('Số lượng thực nhận không được vượt quá số lượng điều phối (' + tpOrder.soLuongPhanBo.toLocaleString('vi-VN') + ')!', 'error');
        document.getElementById('inputSoLuongThucNhan')?.focus();
        return;
    }

    const ghiChu = document.getElementById('inputGhiChu')?.value || tpOrder.ghiChu || '';
    const lyDoChenhLech = document.getElementById('inputLyDoChenhLech')?.value || tpOrder.lyDoChenhLech || '';

    if (slThuc < tpOrder.soLuongPhanBo && !lyDoChenhLech.trim()) {
        showToast('Vui lòng nhập lý do chênh lệch hao hụt / hàng lỗi!', 'error');
        document.getElementById('inputLyDoChenhLech')?.focus();
        return;
    }

    const btn = document.getElementById('btnSaveFinishedInbound');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i><span>Đang lưu vào CSDL...</span>';
    }

    try {
        const formData = new FormData();
        formData.append('action', 'save');
        formData.append('csrf_token', window.csrfToken || '');
        formData.append('maLenhDieuPhoi', tpOrder.maLenhDP);
        formData.append('maViTri', tpOrder.viTriDaChon);
        formData.append('slThucNhap', slThuc);
        formData.append('ghiChu', ghiChu);
        formData.append('lyDoChenhLech', lyDoChenhLech);

        const response = await fetch('index.php?page=vPhieuNhapTP&action=save', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': window.csrfToken || ''
            }
        });

        if (!response.ok) {
            const errHtml = await response.text();
            throw new Error(`Máy chủ phản hồi lỗi HTTP ${response.status}: ${errHtml.substring(0, 120)}`);
        }

        let res;
        try {
            res = await response.json();
        } catch (jsonErr) {
            throw new Error('Dữ liệu máy chủ trả về không đúng định dạng JSON: ' + jsonErr.message);
        }

        if (res.success) {
            tpOrder.trangThai = (slThuc < tpOrder.soLuongPhanBo) ? 'Hoàn tất nhập kho - Hao hụt' : 'Hoàn tất nhập kho';
            if (res.maPNK) {
                tpOrder.maPhieuNhap = res.maPNK;
            }
            tpOrder.soLuongThucNhan = slThuc;
            tpOrder.ghiChu = ghiChu;
            tpOrder.lyDoChenhLech = lyDoChenhLech;
            tpOrder.viTriDaNhap = tpOrder.viTriDaChon;

            // Chuyển vị trí kệ đã chọn sang 'Đang chứa' ngay lập tức
            const savedShelfCode = tpOrder.viTriDaChon;
            defaultShelves.forEach(s => {
                if (s.maViTri === savedShelfCode) {
                    s.trangThai = 'Đang chứa';
                }
            });
            if (cachedSortedShelves) {
                cachedSortedShelves.forEach(s => {
                    if (s.maViTri === savedShelfCode) {
                        s.trangThai = 'Đang chứa';
                    }
                });
            }

            // Cập nhật các đơn hàng khác: nếu đơn chưa hoàn tất đang trỏ vào kệ vừa lưu, phải chuyển sang kệ trống khác HOẶC xóa rỗng (nếu không còn kệ trống)
            wmsData.finishedInboundOrders.forEach(order => {
                if (order.viTriTrong) {
                    order.viTriTrong.forEach(s => {
                        if (s.maViTri === savedShelfCode) {
                            s.trangThai = 'Đang chứa';
                        }
                    });
                }
                const isOtherDone = (order.trangThai === 'Hoàn tất nhập kho' || order.trangThai === 'Hoàn tất nhập kho - Hao hụt' || order.trangThai === 'Hoàn tất');
                if (order !== tpOrder && !isOtherDone && order.viTriDaChon === savedShelfCode) {
                    const nextFree = defaultShelves.find(s => s.trangThai === 'Trống' || s.trangThai === 'Còn trống');
                    order.viTriDaChon = nextFree ? nextFree.maViTri : '';
                }
            });

            // Khoan một nhịp: KHÔNG sắp xếp lại kệ ngay lúc này để giữ nguyên hiển thị vị trí trên màn hình
            pauseShelfSort = true;

            showToast('Lập phiếu nhập kho thành phẩm thành công. Đã lưu vào vị trí ' + savedShelfCode, 'success');
            renderCurrentView();
        } else {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
            showToast(res.message || 'Lỗi lưu phiếu nhập kho!', 'error');
        }
    } catch(err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
        showToast(err.message || 'Lỗi kết nối máy chủ cơ sở dữ liệu!', 'error');
    }
}

// Tự động render ngay khi nạp trang
document.addEventListener('DOMContentLoaded', renderCurrentView);
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    renderCurrentView();
}
</script>
