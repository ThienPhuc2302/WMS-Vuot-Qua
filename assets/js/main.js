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

/**
 * BiscuitWMS Master Data & UI Utilities
 * Hệ thống Quản Lý Kho Bánh Quy Vượt Quá
 */

// Global state
var currentFuncId = window.currentFuncId || 'qc_raw_material';
var selectedIndex = window.selectedIndex || 0;
var auditCategory = window.auditCategory || 'RAW';

// Initial Mock Database
const wmsData = {
            qcBatches: [
                {
                    maLo: 'LO-BM-2610-01',
                    maPhieuYC: 'PYCN-2026-089',
                    maNL: 'NVL-BM01',
                    tenNL: 'Bột mì đa dụng thượng hạng',
                    nhaCungCap: 'Công ty CP Xay Lúa Mì Việt Nam',
                    soLuong: 1500,
                    donViTinh: 'Kg',
                    nsx: '01/10/2026',
                    hsd: '05/11/2026',
                    trangThai: 'Chờ kiểm định',
                    maBienBan: 'BBKD-NL-2610-01',
                    tieuChi: [
                        { maTC: 'TC-BM01', ten: 'Độ ẩm bột tiêu chuẩn', quyChuan: '≤ 14.0 %', ketQuaDo: '13.1 %', danhGia: 'Đạt', ghiChu: 'Độ khô tốt, bột tơi mịn' },
                        { maTC: 'TC-BM02', ten: 'Hàm lượng Protein (Gluten)', quyChuan: '9.5 % – 11.5 %', ketQuaDo: '10.4 %', danhGia: 'Đạt', ghiChu: 'Đạt chuẩn làm vỏ bánh quy' },
                        { maTC: 'TC-BM03', ten: 'Màu sắc, mùi vị & tạp chất', quyChuan: 'Trắng ngà, không ẩm mốc', ketQuaDo: 'Đạt chuẩn', danhGia: 'Đạt', ghiChu: 'Bao bì nguyên niêm phong' }
                    ]
                },
                {
                    maLo: 'LO-BO-2610-02',
                    maPhieuYC: 'PYCN-2026-092',
                    maNL: 'NVL-BO02',
                    tenNL: 'Bơ lạt nguyên kem Anchor',
                    nhaCungCap: 'Fonterra Brands Vietnam',
                    soLuong: 800,
                    donViTinh: 'Kg',
                    nsx: '05/10/2026',
                    hsd: '05/03/2027',
                    trangThai: 'Chờ kiểm định',
                    maBienBan: 'BBKD-NL-2610-02',
                    tieuChi: [
                        { maTC: 'TC-BO01', ten: 'Hàm lượng chất béo sữa', quyChuan: '≥ 82.0 %', ketQuaDo: '82.8 %', danhGia: 'Đạt', ghiChu: 'Đạt độ béo tiêu chuẩn' },
                        { maTC: 'TC-BO02', ten: 'Nhiệt độ bảo quản khi giao', quyChuan: '2°C – 6°C', ketQuaDo: '3.5°C', danhGia: 'Đạt', ghiChu: 'Xe lạnh đủ nhiệt độ' },
                        { maTC: 'TC-BO03', ten: 'Cảm quan màu sắc & cấu trúc', quyChuan: 'Vàng nhạt, đồng nhất', ketQuaDo: 'Đồng nhất', danhGia: 'Đạt', ghiChu: 'Đạt yêu cầu' }
                    ]
                },
                {
                    maLo: 'LO-DG-2610-03',
                    maPhieuYC: 'PYCN-2026-095',
                    maNL: 'NVL-DG03',
                    tenNL: 'Đường tinh luyện nhuyễn',
                    nhaCungCap: 'Công ty CP Thành Thành Công - Biên Hòa',
                    soLuong: 1200,
                    donViTinh: 'Kg',
                    nsx: '10/10/2026',
                    hsd: '10/10/2027',
                    trangThai: 'Đạt',
                    maBienBan: 'BBKD-NL-2610-03',
                    tieuChi: [
                        { maTC: 'TC-DG01', ten: 'Độ tinh khiết Saccharose', quyChuan: '≥ 99.8 %', ketQuaDo: '99.9 %', danhGia: 'Đạt', ghiChu: 'Đạt chuẩn' },
                        { maTC: 'TC-DG02', ten: 'Độ ẩm & Độ tơi', quyChuan: '≤ 0.05 %, không vón cục', ketQuaDo: '0.03 %', danhGia: 'Đạt', ghiChu: 'Đạt chuẩn' }
                    ]
                }
            ],

            rawInboundOrders: [
                {
                    maLenhDP: 'LDP-NL-2610-01',
                    maPhieuYC: 'PYCN-2026-089',
                    maPhieuNhap: 'PNK-NL-2610-009',
                    maLo: 'LO-BM-2610-01',
                    tenNL: 'Bột mì đa dụng thượng hạng',
                    nhaCungCap: 'Công ty CP Xay Lúa Mì Việt Nam',
                    soLuongYeuCau: 1500,
                    donViTinh: 'Kg',
                    nsx: '01/10/2026',
                    hsd: '05/11/2026',
                    khoChiDinh: 'Kho Nguyên Liệu Khô 1 (KHO-NL01)',
                    viTriGoiY: [
                        { maViTri: 'KHO-NL01 / Dãy A - Kệ 02 - Tầng 1', sucChua: '2,000 Kg', trangThai: 'Còn trống' },
                        { maViTri: 'KHO-NL01 / Dãy A - Kệ 03 - Tầng 1', sucChua: '2,000 Kg', trangThai: 'Còn trống' },
                        { maViTri: 'KHO-NL01 / Dãy B - Kệ 01 - Tầng 2', sucChua: '1,800 Kg', trangThai: 'Còn trống' }
                    ],
                    viTriDaChon: 'KHO-NL01 / Dãy A - Kệ 02 - Tầng 1',
                    trangThai: 'Đã điều phối - Chờ nhập kho'
                },
                {
                    maLenhDP: 'LDP-NL-2610-02',
                    maPhieuYC: 'PYCN-2026-092',
                    maPhieuNhap: 'PNK-NL-2610-010',
                    maLo: 'LO-BO-2610-02',
                    tenNL: 'Bơ lạt nguyên kem Anchor',
                    nhaCungCap: 'Fonterra Brands Vietnam',
                    soLuongYeuCau: 800,
                    donViTinh: 'Kg',
                    nsx: '05/10/2026',
                    hsd: '05/03/2027',
                    khoChiDinh: 'Kho Lạnh Nguyên Liệu (KHO-NL03)',
                    viTriGoiY: [
                        { maViTri: 'KHO-NL03 / Dãy C - Kệ 01 - Tầng 1', sucChua: '1,000 Kg', trangThai: 'Còn trống' },
                        { maViTri: 'KHO-NL03 / Dãy C - Kệ 01 - Tầng 2', sucChua: '1,000 Kg', trangThai: 'Còn trống' }
                    ],
                    viTriDaChon: 'KHO-NL03 / Dãy C - Kệ 01 - Tầng 1',
                    trangThai: 'Đã điều phối - Chờ nhập kho'
                }
            ],

            rawExportRequisitions: [
                {
                    maPhieuYC: 'PYCX-NL-2610-15',
                    maKeHoach: 'KHSX-2026-104',
                    xuongYeuCau: 'Phân xưởng Bánh quy số 1',
                    ngayYeuCau: '22/10/2026',
                    tenNL: 'Bột mì đa dụng thượng hạng',
                    maNL: 'NVL-BM01',
                    soLuongYeuCau: 1200,
                    donViTinh: 'Kg',
                    trangThai: 'Chờ xử lý',
                    maLenhSinh: 'LDPX-NL-2610-15',
                    deXuatFEFO: [
                        {
                            thuTu: 'Ưu tiên 1 (HSD gần nhất)',
                            maLo: 'LO-BM-2609-04',
                            nsx: '10/09/2026',
                            hsd: '30/10/2026',
                            soNgayCon: 8,
                            viTri: 'KHO-NL01 / Dãy A - Kệ 01 - Tầng 1',
                            tonKhaDung: 850,
                            slDeXuat: 850
                        },
                        {
                            thuTu: 'Ưu tiên 2 (Lô kế tiếp)',
                            maLo: 'LO-BM-2610-01',
                            nsx: '01/10/2026',
                            hsd: '05/11/2026',
                            soNgayCon: 14,
                            viTri: 'KHO-NL01 / Dãy A - Kệ 02 - Tầng 1',
                            tonKhaDung: 1500,
                            slDeXuat: 350
                        }
                    ]
                },
                {
                    maPhieuYC: 'PYCX-NL-2610-16',
                    maKeHoach: 'KHSX-2026-105',
                    xuongYeuCau: 'Phân xưởng Bánh quy số 2',
                    ngayYeuCau: '22/10/2026',
                    tenNL: 'Bơ lạt nguyên kem Anchor',
                    maNL: 'NVL-BO02',
                    soLuongYeuCau: 500,
                    donViTinh: 'Kg',
                    trangThai: 'Chờ xử lý',
                    maLenhSinh: 'LDPX-NL-2610-16',
                    deXuatFEFO: [
                        {
                            thuTu: 'Ưu tiên 1 (HSD gần nhất)',
                            maLo: 'LO-BO-2609-08',
                            nsx: '15/09/2026',
                            hsd: '15/11/2026',
                            soNgayCon: 24,
                            viTri: 'KHO-NL03 / Dãy C - Kệ 02 - Tầng 1',
                            tonKhaDung: 500,
                            slDeXuat: 500
                        }
                    ]
                }
            ],

            finishedInboundOrders: [
                {
                    maLenhDP: 'LDP-TP-2610-12',
                    maPhieuYC: 'PYCN-TP-2610-05',
                    maPhieuNhap: 'PNK-TP-2610-018',
                    maKH: 'KHSX-2026-104',
                    phanXuong: 'Phân xưởng Bánh quy số 1',
                    maLo: 'LO-TP-2610-05',
                    maTP: 'TP-BQB01',
                    tenTP: 'Bánh quy Bơ Đan Mạch Hộp Thiếc 454g',
                    quyCach: 'Thùng 12 hộp x 454g',
                    donViTinh: 'Thùng',
                    soLuongPhanBo: 500,
                    nsx: '20/10/2026',
                    hsd: '20/04/2027',
                    khoTiepNhan: 'Kho Thành Phẩm 1 (KHO-TP01)',
                    viTriTrong: [
                        { maViTri: 'KHO-TP01 / Dãy B - Kệ 01 - Tầng 2', sucChua: '600 Thùng', trangThai: 'Còn trống' },
                        { maViTri: 'KHO-TP01 / Dãy B - Kệ 02 - Tầng 1', sucChua: '600 Thùng', trangThai: 'Còn trống' },
                        { maViTri: 'KHO-TP01 / Dãy B - Kệ 03 - Tầng 1', sucChua: '600 Thùng', trangThai: 'Còn trống' }
                    ],
                    viTriDaChon: 'KHO-TP01 / Dãy B - Kệ 01 - Tầng 2',
                    ngayGioNhap: '22/10/2026 15:10',
                    ghiChu: 'Bao bì nguyên vẹn 100%, đã xếp lên kệ.',
                    trangThai: 'Chờ nhập kho'
                },
                {
                    maLenhDP: 'LDP-TP-2610-14',
                    maPhieuYC: 'PYCN-TP-2610-06',
                    maPhieuNhap: 'PNK-TP-2610-019',
                    maKH: 'KHSX-2026-105',
                    phanXuong: 'Phân xưởng Bánh quy số 2',
                    maLo: 'LO-TP-2610-08',
                    maTP: 'TP-BQS02',
                    tenTP: 'Bánh quy Socola Chip Hạnh Nhân 300g',
                    quyCach: 'Thùng 20 hộp x 300g',
                    donViTinh: 'Thùng',
                    soLuongPhanBo: 400,
                    nsx: '21/10/2026',
                    hsd: '21/04/2027',
                    khoTiepNhan: 'Kho Thành Phẩm 2 (KHO-TP02)',
                    viTriTrong: [
                        { maViTri: 'KHO-TP02 / Dãy A - Kệ 01 - Tầng 1', sucChua: '500 Thùng', trangThai: 'Còn trống' },
                        { maViTri: 'KHO-TP02 / Dãy A - Kệ 01 - Tầng 2', sucChua: '500 Thùng', trangThai: 'Còn trống' }
                    ],
                    viTriDaChon: 'KHO-TP02 / Dãy A - Kệ 01 - Tầng 1',
                    ngayGioNhap: '22/10/2026 15:25',
                    ghiChu: 'Đủ số lượng theo lệnh điều phối.',
                    trangThai: 'Chờ nhập kho'
                }
            ],

            finishedExportRequisitions: [
                {
                    maPhieuYC: 'PYCX-TP-2610-33',
                    maDonHang: 'DH-COOP-2026-882',
                    maKH: 'KHSX-2026-104',
                    khachHang: 'Hệ thống Siêu thị Co.opmart TP.HCM',
                    ngayGiao: '24/10/2026',
                    maTP: 'TP-BQB01',
                    tenTP: 'Bánh quy Bơ Đan Mạch Hộp Thiếc 454g',
                    donViTinh: 'Thùng',
                    soLuongCanXuat: 600,
                    tongTonKhaDung: 820,
                    trangThai: 'Chờ xử lý',
                    maLenhXuat: 'LDPX-TP-2610-33',
                    phanBoFEFO: [
                        {
                            thuTu: 'Ưu tiên 1 (HSD gần nhất)',
                            maLo: 'LO-TP-2609-19',
                            kho: 'KHO-TP01',
                            viTri: 'KHO-TP01 / Dãy B - Kệ 01 - Tầng 1',
                            nsx: '15/09/2026',
                            hsd: '15/01/2027',
                            tonKhaDung: 320,
                            slPhanBo: 320
                        },
                        {
                            thuTu: 'Ưu tiên 2 (Lô kế tiếp)',
                            maLo: 'LO-TP-2610-05',
                            kho: 'KHO-TP01',
                            viTri: 'KHO-TP01 / Dãy B - Kệ 01 - Tầng 2',
                            nsx: '20/10/2026',
                            hsd: '20/04/2027',
                            tonKhaDung: 500,
                            slPhanBo: 280
                        }
                    ]
                },
                {
                    maPhieuYC: 'PYCX-TP-2610-34',
                    maDonHang: 'DH-WIN-2026-901',
                    maKH: 'KHSX-2026-105',
                    khachHang: 'Hệ thống WinCommerce Miền Nam',
                    ngayGiao: '25/10/2026',
                    maTP: 'TP-BQS02',
                    tenTP: 'Bánh quy Socola Chip Hạnh Nhân 300g',
                    donViTinh: 'Thùng',
                    soLuongCanXuat: 350,
                    tongTonKhaDung: 400,
                    trangThai: 'Chờ xử lý',
                    maLenhXuat: 'LDPX-TP-2610-34',
                    phanBoFEFO: [
                        {
                            thuTu: 'Ưu tiên 1 (HSD gần nhất)',
                            maLo: 'LO-TP-2610-08',
                            kho: 'KHO-TP02',
                            viTri: 'KHO-TP02 / Dãy A - Kệ 01 - Tầng 1',
                            nsx: '21/10/2026',
                            hsd: '21/04/2027',
                            tonKhaDung: 400,
                            slPhanBo: 350
                        }
                    ]
                }
            ],

            inventoryAudits: {
                RAW: {
                    maDot: 'DOT-KK-NL-102026',
                    tenDot: 'Đợt Kiểm kê Kho Nguyên Liệu Tháng 10/2026',
                    khoKiemKe: 'Kho Nguyên Liệu Khô 1 (KHO-NL01)',
                    ngayKiemKe: '22/10/2026',
                    trangThai: 'Đang kiểm đếm',
                    items: [
                        {
                            maLo: 'LO-BM-2610-01',
                            maHang: 'NVL-BM01',
                            tenHang: 'Bột mì đa dụng thượng hạng',
                            viTri: 'KHO-NL01 / Dãy A - Kệ 02 - Tầng 1',
                            dvt: 'Kg',
                            soSoSach: 1150,
                            slTotThucTe: 1150,
                            slHongThucTe: 0
                        },
                        {
                            maLo: 'LO-DG-2610-03',
                            maHang: 'NVL-DG03',
                            tenHang: 'Đường tinh luyện nhuyễn',
                            viTri: 'KHO-NL01 / Dãy A - Kệ 04 - Tầng 1',
                            dvt: 'Kg',
                            soSoSach: 1200,
                            slTotThucTe: 1200,
                            slHongThucTe: 0
                        }
                    ]
                },
                FINISHED: {
                    maDot: 'DOT-KK-TP-102026',
                    tenDot: 'Đợt Kiểm kê Kho Thành Phẩm Tháng 10/2026',
                    khoKiemKe: 'Kho Thành Phẩm 1 (KHO-TP01)',
                    ngayKiemKe: '22/10/2026',
                    trangThai: 'Đang kiểm đếm',
                    items: [
                        {
                            maLo: 'LO-TP-2610-05',
                            maHang: 'TP-BQB01',
                            tenHang: 'Bánh quy Bơ Đan Mạch Hộp Thiếc 454g',
                            viTri: 'KHO-TP01 / Dãy B - Kệ 01 - Tầng 2',
                            dvt: 'Thùng',
                            soSoSach: 220,
                            slTotThucTe: 220,
                            slHongThucTe: 0
                        },
                        {
                            maLo: 'LO-TP-2610-08',
                            maHang: 'TP-BQS02',
                            tenHang: 'Bánh quy Socola Chip Hạnh Nhân 300g',
                            viTri: 'KHO-TP02 / Dãy A - Kệ 01 - Tầng 1',
                            dvt: 'Thùng',
                            soSoSach: 400,
                            slTotThucTe: 400,
                            slHongThucTe: 0
                        }
                    ]
                }
            }
        };

// Action Handlers & Business Logic
function saveQCRawMaterial() {
            const batch = wmsData.qcBatches[selectedIndex];
            batch.trangThai = 'Đạt';
            showToast('Lập biên bản kiểm định thành công.');
            renderWorkspace();
        }

        function chooseRawShelf(maViTri) {
            wmsData.rawInboundOrders[selectedIndex].viTriDaChon = maViTri;
            renderWorkspace();
        }

        function saveRawInbound() {
            const order = wmsData.rawInboundOrders[selectedIndex];
            order.trangThai = 'Hoàn tất';
            showToast('Nhập kho nguyên liệu thành công.');
            renderWorkspace();
        }

        function saveRawFEFODispatch() {
            const req = wmsData.rawExportRequisitions[selectedIndex];
            req.trangThai = 'Đã điều phối';
            showToast('Điều phối xuất kho nguyên liệu thành công.');
            renderWorkspace();
        }

        function chooseFinishedShelf(maViTri) {
            if (wmsData.finishedInboundOrders && wmsData.finishedInboundOrders[selectedIndex]) {
                wmsData.finishedInboundOrders[selectedIndex].viTriDaChon = maViTri;
            }
            if (typeof renderCurrentView === 'function') {
                renderCurrentView();
            } else if (typeof renderWorkspace === 'function') {
                renderWorkspace();
            }
        }

        function saveFinishedInbound() {
            if (wmsData.finishedInboundOrders && wmsData.finishedInboundOrders[selectedIndex]) {
                const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
                tpOrder.trangThai = 'Hoàn tất nhập kho';
            }
            showToast('Lập phiếu nhập kho thành phẩm thành công!', 'success');
            if (typeof renderCurrentView === 'function') {
                renderCurrentView();
            } else if (typeof renderWorkspace === 'function') {
                renderWorkspace();
            }
        }

        function saveFinishedExportDispatch() {
            const expReq = wmsData.finishedExportRequisitions[selectedIndex];
            expReq.trangThai = 'Đã điều phối';
            showToast('Điều phối xuất thành phẩm thành công.');
            renderWorkspace();
        }

        function switchAuditCategory(cat) {
            auditCategory = cat;
            renderWorkspace();
        }

        function updateAuditCount(idx, field, val) {
            const num = Math.max(0, Number(val) || 0);
            if (field === 'tot') {
                wmsData.inventoryAudits[auditCategory].items[idx].slTotThucTe = num;
            } else {
                wmsData.inventoryAudits[auditCategory].items[idx].slHongThucTe = num;
            }
            renderWorkspace();
        }

        function saveInventoryAudit() {
            wmsData.inventoryAudits[auditCategory].trangThai = 'Đã hoàn tất';
            showToast('Lập biên bản kiểm kê thành công.');
            renderWorkspace();
        }

        function openPrintPreview(type) {
            const modal = document.getElementById('printModal');
            const body = document.getElementById('printModalBody');

            if (type === 'RAW_INBOUND') {
                const order = wmsData.rawInboundOrders[selectedIndex];
                body.innerHTML = buildA4ReceiptHTML({
                    title: 'PHIẾU NHẬP KHO NGUYÊN LIỆU',
                    code: order.maPhieuNhap,
                    dispatchCode: order.maLenhDP,
                    warehouse: order.khoChiDinh,
                    location: order.viTriDaChon,
                    batchCode: order.maLo,
                    itemName: order.tenNL,
                    unit: order.donViTinh,
                    qty: order.soLuongYeuCau,
                    nsx: order.nsx,
                    hsd: order.hsd
                });
            } else {
                const tpOrder = wmsData.finishedInboundOrders[selectedIndex];
                body.innerHTML = buildA4ReceiptHTML({
                    title: 'PHIẾU NHẬP KHO THÀNH PHẨM',
                    code: tpOrder.maPhieuNhap,
                    dispatchCode: tpOrder.maLenhDP,
                    warehouse: tpOrder.khoTiepNhan,
                    location: tpOrder.viTriDaChon,
                    batchCode: tpOrder.maLo,
                    itemName: tpOrder.tenTP,
                    unit: tpOrder.donViTinh,
                    qty: tpOrder.soLuongPhanBo,
                    nsx: tpOrder.nsx,
                    hsd: tpOrder.hsd
                });
            }
            modal.classList.remove('hidden');
        }

        function buildA4ReceiptHTML(data) {
            return `
                <div class="border border-slate-300 p-6 space-y-6">
                    <div class="flex justify-between items-start border-b border-slate-200 pb-4">
                        <div>
                            <div class="font-bold text-base text-slate-900">CÔNG TY CỔ PHẦN BÁNH QUY BISCUIT CORP</div>
                            <div class="text-xs text-slate-500 mt-0.5">Khu Công Nghiệp Tân Bình, TP. Hồ Chí Minh</div>
                        </div>
                        <div class="text-right text-xs">
                            <div class="font-semibold text-slate-700">Mẫu số: 01-VT</div>
                            <div class="text-slate-600 mt-0.5">Số phiếu: <strong>${data.code}</strong></div>
                        </div>
                    </div>

                    <div class="text-center">
                        <h2 class="text-lg font-bold uppercase text-slate-900">${data.title}</h2>
                        <div class="text-xs text-slate-500 mt-1">Ngày lập: 22/10/2026</div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div><strong>Lệnh điều phối:</strong> ${data.dispatchCode}</div>
                        <div><strong>Kho tiếp nhận:</strong> ${data.warehouse}</div>
                        <div><strong>Vị trí lưu trữ:</strong> ${data.location}</div>
                        <div><strong>Hạn sử dụng:</strong> ${data.nsx} – ${data.hsd}</div>
                    </div>

                    <table class="w-full text-left border border-slate-300 text-sm border-collapse">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="border border-slate-300 p-2.5">Mã lô</th>
                                <th class="border border-slate-300 p-2.5">Tên hàng hóa</th>
                                <th class="border border-slate-300 p-2.5">ĐVT</th>
                                <th class="border border-slate-300 p-2.5 text-right">SL Phân bổ</th>
                                <th class="border border-slate-300 p-2.5 text-right">SL Thực nhập</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-slate-300 p-2.5 font-semibold">${data.batchCode}</td>
                                <td class="border border-slate-300 p-2.5">${data.itemName}</td>
                                <td class="border border-slate-300 p-2.5">${data.unit}</td>
                                <td class="border border-slate-300 p-2.5 text-right">${data.qty.toLocaleString()}</td>
                                <td class="border border-slate-300 p-2.5 text-right font-bold">${data.qty.toLocaleString()}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="grid grid-cols-3 text-center text-sm pt-6 pb-8">
                        <div>
                            <div class="font-semibold">Người giao hàng</div>
                            <div class="text-xs text-slate-400 mt-1">(Ký, họ tên)</div>
                        </div>
                        <div>
                            <div class="font-semibold">Nhân viên kho</div>
                            <div class="text-xs text-slate-400 mt-1">(Ký, họ tên)</div>
                        </div>
                        <div>
                            <div class="font-semibold">Quản lý kho</div>
                            <div class="text-xs text-slate-400 mt-1">(Ký, họ tên)</div>
                        </div>
                    </div>
                </div>
            `;
        }

        function closePrintModal() {
            document.getElementById('printModal').classList.add('hidden');
        }

        function showToast(message) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'px-4 py-3 rounded-lg shadow-lg bg-slate-900 text-white text-sm font-medium flex items-center gap-2.5 pointer-events-auto';
            toast.innerHTML = `
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                <span>${message}</span>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

// Show toast notification
function showNotification(msg, type = 'success') {
    showToast(msg);
}
