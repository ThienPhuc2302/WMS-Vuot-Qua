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
currentFuncId = 'dispatch_finished_product';

function renderCurrentView() {
    const container = document.getElementById('viewWorkspaceContainer');
    if (!container) return;
    const expReq = (wmsData.finishedExportRequisitions && wmsData.finishedExportRequisitions[selectedIndex]) || (wmsData.finishedExportRequisitions ? wmsData.finishedExportRequisitions[0] : null);
    const isDispatched = expReq ? (expReq.trangThai === 'Đã điều phối') : false;

    container.innerHTML = `
<div class="max-w-7xl mx-auto space-y-6 pb-10">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">Điều phối xuất thành phẩm</h1>
                            <p class="text-sm text-slate-500 mt-1">Phân bổ lô thành phẩm xuất kho theo hạn sử dụng gần nhất đáp ứng đơn đặt hàng</p>
                        </div>

                        <!-- Danh sách phiếu yêu cầu xuất thành phẩm -->
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">Danh sách phiếu yêu cầu xuất thành phẩm</h2>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse erp-table">
                                    <thead>
                                        <tr>
                                            <th class="py-3.5 px-6">Mã phiếu yêu cầu</th>
                                            <th class="py-3.5 px-4">Mã đơn hàng</th>
                                            <th class="py-3.5 px-4">Khách hàng</th>
                                            <th class="py-3.5 px-4">Tên thành phẩm</th>
                                            <th class="py-3.5 px-4 text-right">Số lượng cần xuất</th>
                                            <th class="py-3.5 px-6">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        ${wmsData.finishedExportRequisitions.map((item, idx) => `
                                            <tr onclick="selectListItem(${idx})" class="cursor-pointer transition ${idx === selectedIndex ? 'bg-slate-100 font-medium' : ''}">
                                                <td class="py-3.5 px-6 font-semibold text-slate-900">${item.maPhieuYC}</td>
                                                <td class="py-3.5 px-4">${item.maDonHang}</td>
                                                <td class="py-3.5 px-4">${item.khachHang}</td>
                                                <td class="py-3.5 px-4">${item.tenTP}</td>
                                                <td class="py-3.5 px-4 text-right font-semibold">${item.soLuongCanXuat} ${item.donViTinh}</td>
                                                <td class="py-3.5 px-6">
                                                    ${renderStatusDot(item.trangThai)}
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Bảng phân bổ lô thành phẩm xuất -->
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-truck-fast text-amber-600 text-lg"></i>
                                        <h3 class="text-base font-bold text-slate-900">Chi tiết phân bổ lô thành phẩm xuất kho</h3>
                                    </div>
                                    <p class="text-sm text-slate-500 mt-1">Khách hàng: <strong class="text-slate-800">${expReq.khachHang}</strong> — Ngày giao: <strong class="text-slate-800">${expReq.ngayGiao}</strong></p>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="px-3.5 py-2 rounded-lg bg-slate-100 border border-slate-200 text-slate-900 font-semibold whitespace-nowrap">
                                        Tổng tồn khả dụng: ${expReq.tongTonKhaDung} ${expReq.donViTinh}
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-slate-100 border border-slate-200 text-slate-900 font-semibold whitespace-nowrap">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span>Cần xuất: ${expReq.soLuongCanXuat} ${expReq.donViTinh}</span>
                                    </span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse erp-table">
                                    <thead>
                                        <tr>
                                            <th class="py-3.5 px-6">Thứ tự ưu tiên</th>
                                            <th class="py-3.5 px-4">Mã lô TP</th>
                                            <th class="py-3.5 px-4">Hạn sử dụng</th>
                                            <th class="py-3.5 px-4">Vị trí lưu trữ</th>
                                            <th class="py-3.5 px-4 text-right">Tồn khả dụng</th>
                                            <th class="py-3.5 px-6 text-right">Số lượng phân bổ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        ${expReq.phanBoFEFO.map(row => `
                                            <tr>
                                                <td class="py-3.5 px-6 font-medium text-slate-800">${row.thuTu}</td>
                                                <td class="py-3.5 px-4 font-semibold text-slate-900">${row.maLo}</td>
                                                <td class="py-3.5 px-4">${row.hsd}</td>
                                                <td class="py-3.5 px-4 text-slate-700">${row.viTri}</td>
                                                <td class="py-3.5 px-4 text-right">${row.tonKhaDung} ${expReq.donViTinh}</td>
                                                <td class="py-3.5 px-6 text-right font-bold text-slate-900">${row.slPhanBo} ${expReq.donViTinh}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
                                <button onclick="saveFinishedExportDispatch()" ${isDispatched ? 'disabled' : ''}
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
