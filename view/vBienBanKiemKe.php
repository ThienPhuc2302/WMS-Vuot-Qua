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
currentFuncId = 'inventory_audit';

function renderCurrentView() {
    const container = document.getElementById('viewWorkspaceContainer');
    if (!container) return;
    const auditData = (wmsData.inventoryAudits && wmsData.inventoryAudits[auditCategory]) || { trangThai: 'Đang kiểm đếm', items: [] };
    const isLocked = auditData.trangThai === 'Đã hoàn tất';

    container.innerHTML = `
<div class="max-w-7xl mx-auto space-y-6 pb-10">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Lập biên bản kiểm kê</h1>
                                <p class="text-sm text-slate-500 mt-1">Ghi nhận số lượng kiểm đếm thực tế tại kho và đối chiếu chênh lệch sổ sách</p>
                            </div>

                            <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm text-sm">
                                <button onclick="switchAuditCategory('RAW')"
                                    class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap transition ${auditCategory === 'RAW' ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100'}">
                                    Kiểm kê nguyên liệu
                                </button>
                                <button onclick="switchAuditCategory('FINISHED')"
                                    class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap transition ${auditCategory === 'FINISHED' ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100'}">
                                    Kiểm kê thành phẩm
                                </button>
                            </div>
                        </div>

                        <!-- Thông tin đợt kiểm kê -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-slate-500 whitespace-nowrap">Mã đợt kiểm kê</span>
                                    <i class="fa-solid fa-clipboard-list text-amber-600"></i>
                                </div>
                                <strong class="text-base text-slate-900 block whitespace-nowrap">${auditData.maDot}</strong>
                            </div>
                            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-slate-500 whitespace-nowrap">Kho thực hiện</span>
                                    <i class="fa-solid fa-warehouse text-amber-600"></i>
                                </div>
                                <strong class="text-base text-slate-900 block whitespace-nowrap">${auditData.khoKiemKe}</strong>
                            </div>
                            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-slate-500 whitespace-nowrap">Trạng thái biên bản</span>
                                    <i class="fa-solid fa-circle-info text-amber-600"></i>
                                </div>
                                <div class="mt-1.5">
                                    ${renderStatusDot(auditData.trangThai)}
                                </div>
                            </div>
                        </div>

                        <!-- Bảng kiểm kê chi tiết -->
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">${auditData.tenDot}</h2>
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
                                    <tbody class="divide-y divide-slate-100">
                                        ${auditData.items.map((item, idx) => {
                                            const tongThucTe = Number(item.slTotThucTe) + Number(item.slHongThucTe);
                                            const chenhLech = tongThucTe - Number(item.soSoSach);
                                            const diffText = chenhLech === 0 ? '0 (Khớp)' : (chenhLech > 0 ? `+${chenhLech}` : `${chenhLech}`);
                                            return `
                                                <tr>
                                                    <td class="py-3.5 px-6 font-semibold text-slate-900">${item.maLo}</td>
                                                    <td class="py-3.5 px-4 font-medium">${item.tenHang}</td>
                                                    <td class="py-3.5 px-4 text-slate-600">${item.viTri}</td>
                                                    <td class="py-3.5 px-4 text-right font-semibold">${item.soSoSach.toLocaleString()} ${item.dvt}</td>
                                                    <td class="py-3.5 px-4 text-right">
                                                        <input type="number" ${isLocked ? 'disabled' : ''} value="${item.slTotThucTe}"
                                                            oninput="updateAuditCount(${idx}, 'tot', this.value)"
                                                            class="w-28 text-right px-3 py-2 border border-slate-300 rounded-lg bg-white font-semibold text-slate-900 text-sm">
                                                    </td>
                                                    <td class="py-3.5 px-4 text-right">
                                                        <input type="number" ${isLocked ? 'disabled' : ''} value="${item.slHongThucTe}"
                                                            oninput="updateAuditCount(${idx}, 'hong', this.value)"
                                                            class="w-24 text-right px-3 py-2 border border-slate-300 rounded-lg bg-white text-slate-800 text-sm">
                                                    </td>
                                                    <td class="py-3.5 px-4 text-right font-bold text-slate-900">${tongThucTe.toLocaleString()} ${item.dvt}</td>
                                                    <td class="py-3.5 px-6 text-right">
                                                        ${renderStatusDot(diffText)}
                                                    </td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
                                <button onclick="saveInventoryAudit()" ${isLocked ? 'disabled' : ''}
                                    class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap transition ${isLocked ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-brand-600 hover:bg-brand-700 text-white shadow-sm'}">
                                    <i class="fa-solid fa-check"></i>
                                    <span>${isLocked ? 'Đã hoàn tất kiểm kê' : 'Xác nhận hoàn tất'}</span>
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
