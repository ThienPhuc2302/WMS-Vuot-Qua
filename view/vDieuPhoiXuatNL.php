<?php
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

// Thiết lập ID chức năng và nạp view
currentFuncId = 'dispatch_raw_fefo';

function renderCurrentView() {
    const container = document.getElementById('viewWorkspaceContainer');
    if (!container) return;
    const req = (wmsData.rawExportRequisitions && wmsData.rawExportRequisitions[selectedIndex]) || (wmsData.rawExportRequisitions ? wmsData.rawExportRequisitions[0] : null);
    const isDispatched = req ? (req.trangThai === 'Đã điều phối') : false;

    container.innerHTML = `
<div class="max-w-7xl mx-auto space-y-6 pb-10">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">Điều phối xuất nguyên liệu</h1>
                            <p class="text-sm text-slate-500 mt-1">Tiếp nhận phiếu yêu cầu xuất từ phân xưởng và phân bổ lô nguyên liệu có hạn sử dụng sớm nhất</p>
                        </div>

                        <!-- Danh sách phiếu yêu cầu xuất -->
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">Danh sách phiếu yêu cầu xuất nguyên liệu</h2>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse erp-table">
                                    <thead>
                                        <tr>
                                            <th class="py-3.5 px-6">Mã phiếu yêu cầu</th>
                                            <th class="py-3.5 px-4">Kế hoạch SX</th>
                                            <th class="py-3.5 px-4">Phân xưởng yêu cầu</th>
                                            <th class="py-3.5 px-4">Tên nguyên liệu</th>
                                            <th class="py-3.5 px-4 text-right">Số lượng yêu cầu</th>
                                            <th class="py-3.5 px-6">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        ${wmsData.rawExportRequisitions.map((item, idx) => `
                                            <tr onclick="selectListItem(${idx})" class="cursor-pointer transition ${idx === selectedIndex ? 'bg-slate-100 font-medium' : ''}">
                                                <td class="py-3.5 px-6 font-semibold text-slate-900">${item.maPhieuYC}</td>
                                                <td class="py-3.5 px-4">${item.maKeHoach}</td>
                                                <td class="py-3.5 px-4">${item.xuongYeuCau}</td>
                                                <td class="py-3.5 px-4">${item.tenNL}</td>
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

                        <!-- Bảng kết quả đề xuất phân bổ -->
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-boxes-stacked text-amber-600 text-lg"></i>
                                        <h3 class="text-base font-bold text-slate-900">Đề xuất phân bổ lô xuất nguyên liệu</h3>
                                    </div>
                                    <p class="text-sm text-slate-500 mt-1">Mặt hàng: <strong class="text-slate-800">${req.tenNL}</strong> — Cần xuất: <strong class="text-slate-800">${req.soLuongYeuCau.toLocaleString()} ${req.donViTinh}</strong></p>
                                </div>
                                <div class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900 bg-slate-100 px-3.5 py-2 rounded-lg border border-slate-200 whitespace-nowrap">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span>Tổng phân bổ: ${req.soLuongYeuCau.toLocaleString()} / ${req.soLuongYeuCau.toLocaleString()} ${req.donViTinh}</span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse erp-table">
                                    <thead>
                                        <tr>
                                            <th class="py-3.5 px-6">Thứ tự ưu tiên</th>
                                            <th class="py-3.5 px-4">Mã lô</th>
                                            <th class="py-3.5 px-4">Hạn sử dụng</th>
                                            <th class="py-3.5 px-4">Vị trí lưu trữ</th>
                                            <th class="py-3.5 px-4 text-right">Tồn khả dụng</th>
                                            <th class="py-3.5 px-6 text-right">Số lượng phân bổ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        ${req.deXuatFEFO.map(row => `
                                            <tr>
                                                <td class="py-3.5 px-6 font-medium text-slate-800">${row.thuTu}</td>
                                                <td class="py-3.5 px-4 font-semibold text-slate-900">${row.maLo}</td>
                                                <td class="py-3.5 px-4">${row.hsd} <span class="text-slate-500 text-xs">(Còn ${row.soNgayCon} ngày)</span></td>
                                                <td class="py-3.5 px-4 text-slate-700">${row.viTri}</td>
                                                <td class="py-3.5 px-4 text-right">${row.tonKhaDung.toLocaleString()} ${req.donViTinh}</td>
                                                <td class="py-3.5 px-6 text-right font-bold text-slate-900">${row.slDeXuat.toLocaleString()} ${req.donViTinh}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
                                <button onclick="saveRawFEFODispatch()" ${isDispatched ? 'disabled' : ''}
                                    class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap transition ${isDispatched ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-brand-600 hover:bg-brand-700 text-white shadow-sm'}">
                                    <i class="fa-solid fa-check"></i>
                                    <span>${isDispatched ? 'Đã xác nhận điều phối' : 'Xác nhận điều phối'}</span>
                                </button>
                            </div>
                        </div>
                    </div>
    `;
}

// Ghi đè hàm selectListItem để re-render view hiện tại
function selectListItem(idx) {
    selectedIndex = idx;
    renderCurrentView();
}

// Ghi đè hàm switchAuditCategory để re-render tab kiểm kê
function switchAuditCategory(cat) {
    auditCategory = cat;
    renderCurrentView();
}

// Tự động render ngay sau khi nạp trang
document.addEventListener('DOMContentLoaded', function() {
    renderCurrentView();
});
// Fallback nếu DOMContentLoaded đã kích hoạt
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    renderCurrentView();
}
</script>
