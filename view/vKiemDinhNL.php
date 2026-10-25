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
currentFuncId = 'qc_raw_material';

function renderCurrentView() {
    const container = document.getElementById('viewWorkspaceContainer');
    if (!container) return;
    const batch = (wmsData.qcBatches && wmsData.qcBatches[selectedIndex]) || (wmsData.qcBatches ? wmsData.qcBatches[0] : null);
    const isDone = batch ? (batch.trangThai === 'Đạt' || batch.trangThai === 'Đã hoàn tất') : false;

    container.innerHTML = `
<div class="max-w-7xl mx-auto space-y-6 pb-10">
                        <!-- Tiêu đề trang -->
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Lập biên bản kiểm định nguyên liệu</h1>
                                <p class="text-sm text-slate-500 mt-1">Kiểm tra chất lượng lô nguyên liệu đầu vào theo bộ tiêu chí trước khi điều phối nhập kho</p>
                            </div>
                        </div>

                        <!-- Phần 1: Bảng danh sách lô nguyên liệu (Full-width) -->
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-list-check text-amber-600 text-lg"></i>
                                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">Danh sách lô nguyên liệu</h2>
                                </div>
                                <div class="relative w-full sm:w-80">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                                    <input type="text" placeholder="Tìm theo mã lô hoặc tên nguyên liệu..." 
                                        class="w-full pl-10 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:border-brand-600">
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse erp-table">
                                    <thead>
                                        <tr>
                                            <th class="py-3.5 px-6">Mã lô</th>
                                            <th class="py-3.5 px-4">Tên nguyên liệu</th>
                                            <th class="py-3.5 px-4">Nhà cung cấp</th>
                                            <th class="py-3.5 px-4 text-right">Số lượng</th>
                                            <th class="py-3.5 px-4">Ngày SX</th>
                                            <th class="py-3.5 px-4">Hạn sử dụng</th>
                                            <th class="py-3.5 px-6">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        ${wmsData.qcBatches.map((item, idx) => `
                                            <tr onclick="selectListItem(${idx})" class="cursor-pointer transition ${idx === selectedIndex ? 'bg-slate-100 font-medium' : ''}">
                                                <td class="py-3.5 px-6 font-semibold text-slate-900">
                                                    <div class="flex items-center gap-2.5">
                                                        <span class="w-1.5 h-4 rounded-sm ${idx === selectedIndex ? 'bg-brand-600' : 'bg-transparent'}"></span>
                                                        <span>${item.maLo}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4">${item.tenNL}</td>
                                                <td class="py-3.5 px-4 text-slate-600">${item.nhaCungCap}</td>
                                                <td class="py-3.5 px-4 text-right font-semibold">${item.soLuong.toLocaleString()} ${item.donViTinh}</td>
                                                <td class="py-3.5 px-4 text-slate-600">${item.nsx}</td>
                                                <td class="py-3.5 px-4 text-slate-600">${item.hsd}</td>
                                                <td class="py-3.5 px-6">
                                                    ${renderStatusDot(item.trangThai)}
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Phần 2: 2 Khối thông tin song song (Thông tin lô & Kết luận tổng thể) -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3 gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-file-invoice text-amber-600 text-lg"></i>
                                        <h3 class="text-base font-bold text-slate-900 whitespace-nowrap">Thông tin lô nguyên liệu</h3>
                                    </div>
                                    <span class="text-sm text-slate-500 whitespace-nowrap">Mã phiếu nhập: <strong class="text-slate-800">${batch.maPhieuYC}</strong></span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 text-sm">
                                    <div>
                                        <span class="text-slate-500 block whitespace-nowrap">Mã lô</span>
                                        <strong class="text-slate-900 text-base mt-1 block whitespace-nowrap">${batch.maLo}</strong>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <span class="text-slate-500 block whitespace-nowrap">Tên nguyên liệu</span>
                                        <strong class="text-slate-900 text-base mt-1 block">${batch.tenNL}</strong>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block whitespace-nowrap">Số lượng kiểm định</span>
                                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${batch.soLuong.toLocaleString()} ${batch.donViTinh}</strong>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block whitespace-nowrap">Ngày sản xuất</span>
                                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${batch.nsx}</strong>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block whitespace-nowrap">Hạn sử dụng</span>
                                        <strong class="text-slate-900 mt-1 block whitespace-nowrap">${batch.hsd}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3 gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-clipboard-check text-amber-600 text-lg"></i>
                                        <h3 class="text-base font-bold text-slate-900 whitespace-nowrap">Kết luận kiểm định</h3>
                                    </div>
                                    <span class="text-sm font-medium text-slate-500 whitespace-nowrap">${batch.maBienBan}</span>
                                </div>
                                <div class="space-y-4 text-sm">
                                    <div>
                                        <label class="block font-medium text-slate-700 mb-1.5">Kết quả kiểm định tổng thể</label>
                                        <select ${isDone ? 'disabled' : ''} class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white font-semibold text-slate-900 text-sm">
                                            <option value="Đạt">Đạt chuẩn nhập kho</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-slate-700 mb-1.5">Ghi chú biên bản</label>
                                        <input type="text" ${isDone ? 'disabled' : ''} value="Đạt tất cả tiêu chí chất lượng đầu vào." 
                                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-white text-slate-900 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Phần 3: Bảng bộ tiêu chí kiểm định (Full-width) -->
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-vial-circle-check text-amber-600 text-lg"></i>
                                    <h3 class="text-base font-bold text-slate-900">Danh sách tiêu chí kiểm định</h3>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse erp-table">
                                    <thead>
                                        <tr>
                                            <th class="py-3.5 px-6">Mã tiêu chí</th>
                                            <th class="py-3.5 px-4">Tên tiêu chí kiểm định</th>
                                            <th class="py-3.5 px-4">Quy chuẩn</th>
                                            <th class="py-3.5 px-4">Kết quả đo</th>
                                            <th class="py-3.5 px-4">Đánh giá</th>
                                            <th class="py-3.5 px-6">Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        ${batch.tieuChi.map(tc => `
                                            <tr>
                                                <td class="py-3.5 px-6 font-medium text-slate-700">${tc.maTC}</td>
                                                <td class="py-3.5 px-4 font-medium text-slate-900">${tc.ten}</td>
                                                <td class="py-3.5 px-4 text-slate-600">${tc.quyChuan}</td>
                                                <td class="py-3.5 px-4">
                                                    <input type="text" value="${tc.ketQuaDo}" ${isDone ? 'disabled' : ''}
                                                        class="w-32 px-3 py-2 border border-slate-300 rounded-lg bg-white text-slate-900 text-sm">
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <select ${isDone ? 'disabled' : ''} class="px-3 py-2 border border-slate-300 rounded-lg bg-white text-slate-900 font-semibold text-sm">
                                                        <option value="Đạt" selected>Đạt</option>
                                                    </select>
                                                </td>
                                                <td class="py-3.5 px-6">
                                                    <input type="text" value="${tc.ghiChu}" ${isDone ? 'disabled' : ''}
                                                        class="w-full min-w-[220px] px-3 py-2 border border-slate-300 rounded-lg bg-white text-slate-800 text-sm">
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
                                <button onclick="saveQCRawMaterial()" ${isDone ? 'disabled' : ''}
                                    class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 whitespace-nowrap transition ${isDone ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-brand-600 hover:bg-brand-700 text-white shadow-sm'}">
                                    <i class="fa-solid fa-check"></i>
                                    <span>${isDone ? 'Đã xác nhận biên bản' : 'Xác nhận và lưu biên bản'}</span>
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
