<?php
/**
 * VIEW: vPhieuNhapNL.php
 * Use Case: UC 3.2 - Lập phiếu nhập nguyên liệu
 * Phụ trách: Thành viên nhóm (Giao diện đồng bộ Master chuẩn ERP)
 */
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
    // Bỏ tiền tố tên kho nếu có
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

// Bổ sung dữ liệu kệ mẫu đầy đủ (kệ trống và kệ đang chứa) cho nguyên liệu nếu cần
if (wmsData.rawInboundOrders && wmsData.rawInboundOrders.length > 0) {
    wmsData.rawInboundOrders.forEach(order => {
        if (!order.viTriGoiY || order.viTriGoiY.length <= 3) {
            order.viTriGoiY = [
                { maViTri: 'KHO-NL01 / Dãy A - Kệ 02 - Tầng 1', sucChua: '2,000 Kg', trangThai: 'Còn trống' },
                { maViTri: 'KHO-NL01 / Dãy A - Kệ 03 - Tầng 1', sucChua: '2,000 Kg', trangThai: 'Còn trống' },
                { maViTri: 'KHO-NL01 / Dãy B - Kệ 01 - Tầng 2', sucChua: '1,800 Kg', trangThai: 'Còn trống' },
                { maViTri: 'KHO-NL01 / Dãy A - Kệ 01 - Tầng 1', sucChua: '2,500 Kg', trangThai: 'Đang chứa' },
                { maViTri: 'KHO-NL01 / Dãy A - Kệ 01 - Tầng 2', sucChua: '2,500 Kg', trangThai: 'Đang chứa' }
            ];
        }
    });
}

// Thiết lập ID chức năng và nạp view
currentFuncId = 'inbound_raw_material';
if (typeof selectedIndex === 'undefined') {
    selectedIndex = 0;
}

function renderCurrentView() {
    const container = document.getElementById('viewWorkspaceContainer');
    if (!container) return;

    if (!wmsData.rawInboundOrders || wmsData.rawInboundOrders.length === 0) {
        container.innerHTML = '<div class="p-8 text-center text-slate-500">Không có dữ liệu lệnh điều phối nhập nguyên liệu.</div>';
        return;
    }

    if (selectedIndex >= wmsData.rawInboundOrders.length) {
        selectedIndex = 0;
    }

    const order = wmsData.rawInboundOrders[selectedIndex];
    const isCompleted = (order.trangThai === 'Hoàn tất' || order.trangThai === 'Hoàn tất nhập kho');

    container.innerHTML = `
<div class="max-w-7xl mx-auto space-y-5 pb-0">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Lập phiếu nhập nguyên liệu</h1>
        <p class="text-sm text-slate-500 mt-1">Tiếp nhận lệnh điều phối nguyên liệu từ nhà cung cấp và chỉ định vị trí kệ lưu trữ</p>
    </div>

    <!-- Danh sách lệnh điều phối nhập nguyên liệu -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">DANH SÁCH LỆNH ĐIỀU PHỐI TIẾP NHẬN NGUYÊN LIỆU</h2>
            </div>
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
                    ${wmsData.rawInboundOrders.map((item, idx) => `
                        <tr onclick="selectListItem(${idx})" class="cursor-pointer transition ${idx === selectedIndex ? 'bg-slate-100 font-medium' : 'hover:bg-slate-50'}">
                            <td class="py-3.5 px-6 font-semibold text-slate-900">${item.maLenhDP}</td>
                            <td class="py-3.5 px-4">${item.maLo}</td>
                            <td class="py-3.5 px-4">${item.tenNL}</td>
                            <td class="py-3.5 px-4 text-slate-600">${item.khoChiDinh}</td>
                            <td class="py-3.5 px-4 text-right font-semibold">${item.soLuongYeuCau.toLocaleString()} ${item.donViTinh}</td>
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
        <!-- Cột Trái: Chi Tiết Phiếu Nhập Nguyên Liệu -->
        <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 gap-2">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-file-invoice text-amber-600 text-lg"></i>
                        <h3 class="text-base font-bold text-slate-900 whitespace-nowrap">Chi Tiết Phiếu Nhập Nguyên Liệu</h3>
                    </div>
                    ${isCompleted && order.maPhieuNhap ? `<span class="px-2.5 py-1 rounded-md bg-slate-100 text-xs font-semibold text-slate-700 tracking-wider">${order.maPhieuNhap}</span>` : ''}
                </div>
                
                <!-- Tất cả các trường thông tin: bố cục thanh lịch, dài xuống vừa phải -->
                <div class="grid grid-cols-2 gap-x-5 gap-y-4 text-sm pt-3.5">
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lệnh điều phối</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${order.maLenhDP}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Mã lô nguyên liệu</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${order.maLo}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Tên nguyên liệu</span>
                        <strong class="text-slate-900 mt-1 block truncate" title="${order.tenNL}">${order.tenNL}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Nhà cung cấp</span>
                        <strong class="text-slate-900 mt-1 block truncate" title="${order.nhaCungCap || 'Công ty CP Xay Lúa Mì Việt Nam'}">${order.nhaCungCap || 'Công ty CP Xay Lúa Mì Việt Nam'}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kho tiếp nhận</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${order.khoChiDinh}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Hạn sử dụng</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${order.nsx || '01/10/2026'} – ${order.hsd || '05/11/2026'}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Người điều phối</span>
                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">Trần Văn Sang</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs whitespace-nowrap">Kiểm định chất lượng</span>
                        <div class="mt-1 flex items-center gap-2">
                            ${renderStatusDot('Đạt')}
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1.5 text-xs whitespace-nowrap">Số lượng yêu cầu</label>
                        <input type="text" disabled value="${order.soLuongYeuCau.toLocaleString()} ${order.donViTinh}" 
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold text-sm">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Số lượng thực nhập (${order.donViTinh})</label>
                        <input type="number" id="inputSoLuongThucNhapNL" ${isCompleted ? 'disabled' : ''} value="${order.soLuongYeuCau}" 
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white text-slate-900 font-bold text-sm">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-slate-700 font-medium mb-1.5 text-xs whitespace-nowrap">Ghi chú nhập kho</label>
                        <input type="text" id="inputGhiChuNL" ${isCompleted ? 'disabled' : ''} value="${order.ghiChu || 'Hàng nguyên tem niêm phong, kiểm định đạt chuẩn 100%.'}" 
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
                    ${sortShelvesByAvailability(order.viTriGoiY).map(vt => {
                        const isOccupied = (vt.trangThai === 'Đang chứa');
                        const isSelected = (order.viTriDaChon === vt.maViTri);
                        let itemClass = 'p-3.5 rounded-lg border transition flex items-center justify-between gap-3 ';
                        if (isSelected) {
                            itemClass += 'border-brand-600 bg-amber-50/40 shadow-sm ';
                        } else if (isOccupied && !isCompleted) {
                            itemClass += 'border-slate-200 bg-slate-50/70 opacity-80 cursor-not-allowed ';
                        } else {
                            itemClass += 'border-slate-200 hover:bg-slate-50 cursor-pointer ';
                        }

                        return `
                        <div onclick="${isCompleted ? '' : `chooseRawShelf('${vt.maViTri}')`}"
                            class="${itemClass}">
                            <div class="min-w-0 flex-1">
                                <div class="text-sm">${formatShelfLocation(vt.maViTri)}</div>
                                <div class="text-xs text-slate-500 mt-1 whitespace-nowrap">Sức chứa khả dụng: ${vt.sucChua}</div>
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

            <!-- 2 Nút thao tác ở đáy: Kéo xích lên ngang bằng đáy bảng bên trái -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3 mt-2">
                <button type="button" onclick="openPrintPreview('RAW_INBOUND')"
                    class="px-4 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>In phiếu</span>
                </button>
                <button type="button" id="btnSaveRawInbound" onclick="saveRawInbound()" ${isCompleted ? 'disabled' : ''}
                    class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap transition cursor-pointer ${isCompleted ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-brand-600 hover:bg-brand-700 text-white shadow-sm'}">
                    <i class="fa-solid fa-check"></i>
                    <span>${isCompleted ? 'Đã nhập kho' : 'Xác nhận lưu phiếu nhập kho'}</span>
                </button>
            </div>
        </div>
    </div>
</div>
    `;
}

// Xử lý chọn dòng trong danh sách
function selectListItem(idx) {
    selectedIndex = idx;
    renderCurrentView();
}

// Chọn vị trí kệ cho nguyên liệu
function chooseRawShelf(maViTri) {
    const order = wmsData.rawInboundOrders[selectedIndex];
    if (!order) return;
    if (order.trangThai === 'Hoàn tất') return;
    
    const target = order.viTriGoiY.find(s => s.maViTri === maViTri);
    if (target && target.trangThai === 'Đang chứa') {
        showToast('Vị trí ' + maViTri + ' đang chứa hàng! Vui lòng chọn vị trí còn trống.', 'error');
        return;
    }
    order.viTriDaChon = maViTri;
    renderCurrentView();
}

// Xác nhận lưu phiếu nhập nguyên liệu
function saveRawInbound() {
    const order = wmsData.rawInboundOrders[selectedIndex];
    if (!order) return;
    if (order.trangThai === 'Hoàn tất') return;

    order.trangThai = 'Hoàn tất';
    const chosen = order.viTriDaChon;
    if (order.viTriGoiY) {
        order.viTriGoiY.forEach(s => {
            if (s.maViTri === chosen) {
                s.trangThai = 'Đang chứa';
            }
        });
    }

    showToast('Xác nhận lưu phiếu nhập kho nguyên liệu thành công!', 'success');
    renderCurrentView();
}

// Tự động render ngay sau khi nạp trang
document.addEventListener('DOMContentLoaded', function() {
    renderCurrentView();
});
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    renderCurrentView();
}
</script>
