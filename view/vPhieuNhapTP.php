<?php
/**
 * VIEW: vPhieuNhapTP.php
 * Use Case: UC 3.2 / 3.4 - Lập phiếu nhập thành phẩm
 * Phụ trách: Nguyễn Hữu Lộc
 * Kết nối MVC Model & Controller
 */
require_once __DIR__ . '/../controller/cPhieuNhapTP.php';

$controller = new cPhieuNhapTP();
$dataFromDB = $controller->index();
$ordersFromDB = $dataFromDB['orders'] ?? [];
$shelvesFromDB = $dataFromDB['shelves'] ?? [];
?>

<!-- Container hiển thị chức năng -->
<div id="viewWorkspaceContainer"></div>

<script>
function renderStatusDot(status) {
    if (!status) return '';
    var s = String(status).trim();
    if (s === 'Đạt' || s === 'Đã hoàn thành' || s === 'Đã tiếp nhận' || s === 'Đã điều phối' || s === 'Hoàn tất nhập kho' || s === 'Hoàn tất' || s === 'Đã nhập kho' || s === '0 (Khớp)') {
        return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>' + s + '</span>';
    }
    if (s === 'Chờ kiểm định' || s === 'Chờ điều phối' || s === 'Chờ tiếp nhận' || s === 'Chờ xuất kho' || s === 'Chờ nhập kho' || s === 'Đang kiểm kê' || s === 'Đang kiểm đếm' || s === 'Khả dụng') {
        return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>' + s + '</span>';
    }
    if (s === 'Không đạt' || s === 'Hỏng' || s === 'Lỗi' || s.indexOf('Hao hụt') !== -1 || s.startsWith('+') || s.startsWith('-')) {
        return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-800 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>' + s + '</span>';
    }
    return '<span class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-slate-400 shrink-0"></span>' + s + '</span>';
}

// Chuyển đổi mã kệ kỹ thuật (ví dụ B1-K01-T01) thành định dạng Dãy - Kệ - Tầng dễ nhìn
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

// Khởi tạo danh sách kệ khả dụng (Áp dụng thuật toán ưu tiên kệ trống lên đầu)
var defaultShelves = sortShelvesByAvailability((initialDBShelves && initialDBShelves.length > 0) ? initialDBShelves.map(function(s) {
    return {
        maViTri: s.maViTri,
        sucChua: (s.sucChuaKhaDung || 500) + ' Thùng',
        trangThai: s.trangThaiSuDung || 'Trống'
    };
}) : [
    { maViTri: 'B1-K02-T01', sucChua: '500 Thùng', trangThai: 'Trống' },
    { maViTri: 'B1-K02-T02', sucChua: '500 Thùng', trangThai: 'Trống' },
    { maViTri: 'B1-K03-T01', sucChua: '600 Thùng', trangThai: 'Trống' },
    { maViTri: 'B1-K01-T01', sucChua: '400 Thùng', trangThai: 'Đang chứa' },
    { maViTri: 'B1-K01-T02', sucChua: '400 Thùng', trangThai: 'Đang chứa' }
]);

// Khởi tạo danh sách lệnh nhập thành phẩm từ CSDL
if (initialDBOrders && initialDBOrders.length > 0) {
    // Tìm kệ trống đầu tiên làm mặc định cho các đơn chưa nhập
    var firstEmptyShelf = defaultShelves.find(function(s) {
        return s.trangThai === 'Trống' || s.trangThai === 'Còn trống';
    });
    var firstEmptyCode = firstEmptyShelf ? firstEmptyShelf.maViTri : 'B1-K02-T01';

    wmsData.finishedInboundOrders = initialDBOrders.map(function(item, idx) {
        var defaultPNK = item.maPNK || '';
        var rawShelf = item.viTriDaNhap;
        var defaultShelf = rawShelf || firstEmptyCode;
        var nsxStr = item.ngaySanXuat ? item.ngaySanXuat.split('-').reverse().join('/') : '20/10/2026';
        var hsdStr = item.hanSuDung ? item.hanSuDung.split('-').reverse().join('/') : '20/04/2027';
        var rawNguoiDP = item.tenNguoiDieuPhoi || item.nguoiDieuPhoi || '';
        var tenNguoiDP = 'Trần Bích Thuận';
        if (rawNguoiDP && !rawNguoiDP.startsWith('NV')) {
            tenNguoiDP = rawNguoiDP;
        } else if (rawNguoiDP === 'NV003') {
            tenNguoiDP = 'Trần Bích Thuận';
        } else if (rawNguoiDP === 'NV001') {
            tenNguoiDP = 'Trần Văn Sang';
        }

        return {
            maLenhDP: item.maLenhDieuPhoi,
            maPhieuNhap: defaultPNK,
            maLo: item.maLo,
            tenTP: item.tenTP,
            phanXuong: item.phanXuong || 'Phân xưởng Bánh quy số 1',
            soLuongPhanBo: Number(item.soLuongPhanBo) || 500,
            soLuongThucNhan: Number(item.soLuongPhanBo) || 500,
            donViTinh: item.donViTinh || 'Thùng',
            khoTiepNhan: item.tenKho || 'Kho Thành Phẩm Bánh Quy 1 (KHO-TP01)',
            nsx: nsxStr,
            hsd: hsdStr,
            quyCach: item.quyCachDongGoi || '24 hộp/thùng (Thùng 5 lớp)',
            nguoiDieuPhoi: tenNguoiDP,
            trangThaiQC: item.trangThaiKiemDinh || 'Đạt',
            trangThai: item.trangThai || 'Chờ nhập kho',
            ghiChu: item.ghiChuNhapKho || item.lyDoGiaiTrinh || 'Bao bì nguyên vẹn 100%, đã xếp lên kệ.',
            lyDoChenhLech: item.lyDoChenhLech || '',
            viTriDaChon: defaultShelf,
            viTriTrong: defaultShelves
        };
    });
    // Áp dụng thuật toán ưu tiên các đơn 'Chờ nhập kho' lên đầu
    wmsData.finishedInboundOrders = sortInboundOrdersByPriority(wmsData.finishedInboundOrders);
} else if (wmsData.finishedInboundOrders) {
    wmsData.finishedInboundOrders = sortInboundOrdersByPriority(wmsData.finishedInboundOrders);
}

// Thiết lập ID chức năng
currentFuncId = 'inbound_finished_product';
if (typeof selectedIndex === 'undefined') {
    selectedIndex = 0;
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
    const isDone = (tpOrder.trangThai === 'Hoàn tất nhập kho' || tpOrder.trangThai === 'Hoàn tất nhập kho - Hao hụt');
    const isLech = (Number(tpOrder.soLuongThucNhan) < Number(tpOrder.soLuongPhanBo));

    container.innerHTML = `
<div class="max-w-7xl mx-auto space-y-5 pb-0">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Lập phiếu nhập thành phẩm</h1>
        <p class="text-sm text-slate-500 mt-1">Tiếp nhận lô thành phẩm từ phân xưởng theo lệnh điều phối và lưu vị trí kệ</p>
    </div>

    <!-- Danh sách lệnh điều phối thành phẩm -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH LỆNH ĐIỀU PHỐI TIẾP NHẬN THÀNH PHẨM</h2>
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
                    ${wmsData.finishedInboundOrders.map((item, idx) => `
                        <tr onclick="selectListItem(${idx})" class="cursor-pointer transition ${idx === selectedIndex ? 'bg-slate-100 font-medium' : 'hover:bg-slate-50'}">
                            <td class="py-3.5 px-6 font-semibold text-slate-900">${item.maLenhDP}</td>
                            <td class="py-3.5 px-4">${item.maLo}</td>
                            <td class="py-3.5 px-4">${item.tenTP}</td>
                            <td class="py-3.5 px-4 text-slate-600">${item.phanXuong}</td>
                            <td class="py-3.5 px-4 text-right font-semibold">${item.soLuongPhanBo} ${item.donViTinh}</td>
                            <td class="py-3.5 px-6">
                                ${renderStatusDot(item.trangThai)}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
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
                        <strong class="text-slate-900 mt-1 block">${tpOrder.quyCach}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kho tiếp nhận</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${tpOrder.khoTiepNhan}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Hạn sử dụng</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${tpOrder.nsx} – ${tpOrder.hsd}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Người điều phối</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${tpOrder.nguoiDieuPhoi}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kiểm định chất lượng</span>
                        <div class="mt-1 flex items-center gap-2">
                            ${renderStatusDot('Đạt')}
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1.5 text-xs whitespace-nowrap">Số lượng phân bổ</label>
                        <input type="text" disabled value="${tpOrder.soLuongPhanBo} ${tpOrder.donViTinh}" 
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold text-sm">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Số lượng thực nhận (${tpOrder.donViTinh})</label>
                        <input type="number" id="inputSoLuongThucNhan" oninput="handleQuantityChange(this.value)" ${isDone ? 'disabled' : ''} value="${tpOrder.soLuongThucNhan}" 
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
            <div class="flex-1 flex flex-col min-h-0">
                <div class="border-b border-slate-100 pb-3.5">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-table-cells text-amber-600 text-lg"></i>
                        <h3 class="text-base font-bold text-slate-900">Chọn vị trí lưu trữ còn trống</h3>
                    </div>
                </div>
                <!-- Danh sách kệ: thuật toán ưu tiên kệ trống lên đầu, thanh cuộn tự động -->
                <div class="space-y-2.5 overflow-y-auto pr-1 flex-1 min-h-0 py-3" style="max-height: 315px;">
                    ${sortShelvesByAvailability(tpOrder.viTriTrong).map(vt => {
                        const isOccupied = (vt.trangThai === 'Đang chứa');
                        const isSelected = (tpOrder.viTriDaChon === vt.maViTri);
                        let itemClass = 'p-3.5 rounded-lg border transition flex items-center justify-between gap-3 ';
                        if (isSelected) {
                            itemClass += 'border-brand-600 bg-amber-50/40 shadow-sm ';
                        } else if (isOccupied && !isDone) {
                            itemClass += 'border-slate-200 bg-slate-50/70 opacity-80 cursor-not-allowed ';
                        } else {
                            itemClass += 'border-slate-200 hover:bg-slate-50 cursor-pointer ';
                        }

                        return `
                        <div onclick="${isDone ? '' : `chooseFinishedShelf('${vt.maViTri}')`}"
                            class="${itemClass}">
                            <div class="min-w-0 flex-1">
                                <div class="text-sm">${formatShelfLocation(vt.maViTri)}</div>
                                <div class="text-xs text-slate-500 mt-1 whitespace-nowrap">Sức chứa tối đa: ${vt.sucChua}</div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                ${renderStatusDot(vt.trangThai)}
                                <i class="fa-solid ${isSelected ? 'fa-circle-check text-brand-600 text-lg' : 'fa-regular fa-circle text-slate-300 text-lg'}"></i>
                            </div>
                        </div>
                        `;
                    }).join('')}
                </div>
            </div>

            <!-- 2 Nút trả về vị trí cũ ở đây: Kéo xích lên ngang bằng đáy bảng bên trái -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3 mt-2">
                <button type="button" onclick="openPrintPreview('FINISHED_INBOUND')"
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

// Chuyển chọn dòng lệnh điều phối
function selectListItem(idx) {
    selectedIndex = idx;
    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    if (tpOrder) {
        const isDone = (tpOrder.trangThai === 'Hoàn tất nhập kho' || tpOrder.trangThai === 'Hoàn tất nhập kho - Hao hụt');
        if (!isDone) {
            // Nếu kệ đang chọn hiện tại đã bị chiếm (Đang chứa), tự động chuyển sang kệ trống tiếp theo
            const currentChosen = defaultShelves.find(s => s.maViTri === tpOrder.viTriDaChon);
            if (!currentChosen || currentChosen.trangThai === 'Đang chứa') {
                const nextEmpty = defaultShelves.find(s => s.trangThai === 'Trống' || s.trangThai === 'Còn trống');
                if (nextEmpty) {
                    tpOrder.viTriDaChon = nextEmpty.maViTri;
                }
            }
        }
    }
    renderCurrentView();
}

// Xử lý khi thay đổi số lượng thực nhận (Theo Use Case 3.2 Luồng 5.1 & Ngoại lệ 5.2)
function handleQuantityChange(val) {
    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    if (!tpOrder) return;
    const num = Number(val);
    tpOrder.soLuongThucNhan = num;
    const rowChenhLech = document.getElementById('rowLyDoChenhLech');
    if (rowChenhLech) {
        if (num < tpOrder.soLuongPhanBo) {
            rowChenhLech.classList.remove('hidden');
        } else {
            rowChenhLech.classList.add('hidden');
        }
    }
}

// Chọn kệ lưu kho: CẬP NHẬT TRẠNG THÁI NGAY TỨC THÌ (KHÔNG CẦN NHẤN CHỖ KHÁC)
function chooseFinishedShelf(maViTri) {
    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    if (!tpOrder) return;
    
    // Nếu đơn hàng đã hoàn tất nhập kho thì không cho đổi
    if (tpOrder.trangThai === 'Hoàn tất nhập kho' || tpOrder.trangThai === 'Hoàn tất nhập kho - Hao hụt') {
        return;
    }

    // Kiểm tra xem vị trí này có đang chứa hàng không
    const targetShelf = defaultShelves.find(s => s.maViTri === maViTri);
    if (targetShelf && targetShelf.trangThai === 'Đang chứa') {
        showToast('Vị trí ' + maViTri + ' đang chứa hàng! Vui lòng chọn vị trí kệ còn trống.', 'error');
        return;
    }

    tpOrder.viTriDaChon = maViTri;
    // Cập nhật lại giao diện ngay tức khắc
    renderCurrentView();
}

// Xác nhận lưu phiếu nhập kho: KẾT NỐI CONTROLLER & MODEL CƠ SỞ DỮ LIỆU
async function saveFinishedInbound() {
    const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
    if (!tpOrder) return;
    if (tpOrder.trangThai === 'Hoàn tất nhập kho' || tpOrder.trangThai === 'Hoàn tất nhập kho - Hao hụt') return;

    if (!tpOrder.viTriDaChon) {
        showToast('Vui lòng chọn vị trí lưu kho (kệ)!', 'error');
        return;
    }

    const slThuc = Number(document.getElementById('inputSoLuongThucNhan')?.value) || tpOrder.soLuongPhanBo;
    const ghiChu = document.getElementById('inputGhiChu')?.value || tpOrder.ghiChu || '';
    const lyDoChenhLech = document.getElementById('inputLyDoChenhLech')?.value || tpOrder.lyDoChenhLech || '';

    // Kiểm tra ngoại lệ 5.2: Số lượng thực nhận lớn hơn điều phối
    if (slThuc > tpOrder.soLuongPhanBo) {
        showToast('Số lượng thực nhận không được vượt quá số lượng điều phối!', 'error');
        return;
    }

    // Kiểm tra ngoại lệ 10.1: Số lượng <= 0
    if (slThuc <= 0) {
        showToast('Số lượng thực nhận phải lớn hơn 0!', 'error');
        return;
    }

    // Bắt buộc nhập lý do nếu có hao hụt (Luồng 5.1)
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
        formData.append('action', 'save_tp_inbound');
        formData.append('maLenhDieuPhoi', tpOrder.maLenhDP);
        formData.append('maViTri', tpOrder.viTriDaChon);
        formData.append('slThucNhap', slThuc);
        formData.append('ghiChu', ghiChu);
        formData.append('lyDoChenhLech', lyDoChenhLech);

        const response = await fetch('controller/cPhieuNhapTP.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const res = await response.json();

        if (res.success) {
            tpOrder.trangThai = (slThuc < tpOrder.soLuongPhanBo) ? 'Hoàn tất nhập kho - Hao hụt' : 'Hoàn tất nhập kho';
            if (res.maPNK) {
                tpOrder.maPhieuNhap = res.maPNK;
            }
            tpOrder.soLuongThucNhan = slThuc;
            tpOrder.ghiChu = ghiChu;
            tpOrder.lyDoChenhLech = lyDoChenhLech;
            tpOrder.viTriDaNhap = tpOrder.viTriDaChon;

            // ĐỒNG BỘ HIỆU ỨNG TỨC THÌ: Chuyển vị trí kệ đã chọn sang 'Đang chứa' ngay lập tức!
            const savedShelfCode = tpOrder.viTriDaChon;
            defaultShelves.forEach(s => {
                if (s.maViTri === savedShelfCode) {
                    s.trangThai = 'Đang chứa';
                }
            });

            // Đồng bộ trạng thái kệ trên toàn bộ các đơn hàng
            wmsData.finishedInboundOrders.forEach(order => {
                if (order.viTriTrong) {
                    order.viTriTrong.forEach(s => {
                        if (s.maViTri === savedShelfCode) {
                            s.trangThai = 'Đang chứa';
                        }
                    });
                }
                // Nếu các đơn hàng chờ khác đang tạm trỏ vào kệ này, tự động trỏ sang kệ trống tiếp theo
                if (order !== tpOrder && order.trangThai === 'Chờ nhập kho' && order.viTriDaChon === savedShelfCode) {
                    const nextFree = defaultShelves.find(s => s.trangThai === 'Trống' || s.trangThai === 'Còn trống');
                    if (nextFree) {
                        order.viTriDaChon = nextFree.maViTri;
                    }
                }
            });

            // HIỂN THỊ THÔNG BÁO THÀNH CÔNG VỚI TICK XANH Ở GÓC TRÊN BÊN PHẢI!
            showToast('Lập phiếu nhập kho thành phẩm thành công. Đã lưu vào vị trí ' + savedShelfCode, 'success');

            // Cập nhật giao diện ngay lập tức (Bảng trên thành Hoàn tất, Kệ dưới thành Đang chứa)
            renderCurrentView();
        } else {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
            // HIỂN THỊ THÔNG BÁO THẤT BẠI VỚI X ĐỎ Ở GÓC TRÊN BÊN PHẢI!
            showToast(res.message || 'Lỗi lưu phiếu nhập kho!', 'error');
        }
    } catch(err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
        showToast('Lỗi kết nối máy chủ cơ sở dữ liệu!', 'error');
    }
}

// Tự động render ngay khi nạp trang
document.addEventListener('DOMContentLoaded', renderCurrentView);
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    renderCurrentView();
}
</script>
