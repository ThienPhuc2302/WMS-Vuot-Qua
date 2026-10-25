            </main>

            <!-- ==================== FOOTER ==================== -->
            <footer class="h-12 bg-white border-t border-stone-200 px-8 flex items-center justify-between text-xs text-stone-500 shrink-0">
                <span>© 2026 BiscuitWMS - Hệ thống Quản lý Kho Nhà máy Bánh Quy</span>
                <span>Phiên bản v1.0.4 • Môi trường Phát triển ứng dụng</span>
            </footer>
        </div>
    </div>

    <!-- ==================== MODAL XEM TRƯỚC PHIẾU IN A4 ==================== -->
    <div id="printModal" class="fixed inset-0 bg-slate-900/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <div class="px-6 py-4 bg-stone-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5 text-base font-semibold">
                    <i class="fa-solid fa-print text-amber-400"></i>
                    <span>Bản xem trước chứng từ in ấn (Khổ A4)</span>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="window.print()" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer">
                        <i class="fa-solid fa-print"></i>
                        <span>In phiếu</span>
                    </button>
                    <button onclick="closePrintModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-stone-800 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>
            <div id="printModalBody" class="p-6 overflow-y-auto flex-1 bg-stone-100 flex justify-center">
                <!-- Nội dung phiếu A4 sẽ render vào đây -->
            </div>
        </div>
    </div>

    <!-- Toast container (Góc trên bên phải, dưới Header, không che nút thao tác bên dưới) -->
    <div id="toastContainer" class="fixed top-20 right-6 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('leftSidebar');
            const icon = document.getElementById('sidebarToggleIcon');
            if (sidebar) {
                sidebar.classList.toggle('-ml-80');
                if (icon) {
                    icon.classList.toggle('rotate-90');
                }
            }
        }

        function showToast(msg, type = 'info') {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            const toast = document.createElement('div');
            const borderClass = type === 'error' ? 'border-rose-500/60 bg-stone-900/95 text-white' : 
                               type === 'success' ? 'border-emerald-500/60 bg-stone-900/95 text-white' : 
                               'border-amber-600/40 bg-stone-900/95 text-white';
            const iconClass = type === 'error' ? 'fa-solid fa-circle-xmark text-rose-500' : 
                             type === 'success' ? 'fa-solid fa-circle-check text-emerald-400' : 
                             'fa-solid fa-circle-info text-amber-400';
            toast.className = `pointer-events-auto px-5 py-3.5 rounded-2xl shadow-2xl border flex items-center gap-3.5 text-xs sm:text-sm font-semibold transition-all duration-300 backdrop-blur-md transform translate-x-6 opacity-0 ${borderClass}`;
            toast.innerHTML = `
                <i class="${iconClass} text-xl shrink-0"></i>
                <span class="flex-1 leading-snug">${msg}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-stone-400 hover:text-white p-1 rounded-lg transition ml-1 shrink-0">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            `;
            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-6', 'opacity-0');
            });
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-6');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }
    </script>
</body>
</html>
