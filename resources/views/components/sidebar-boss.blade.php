<aside id="sidebar-boss"
    class="sidebar-responsive fixed left-0 top-24 bottom-0 w-64 bg-white border-r border-gray-100 p-4 transition-transform duration-300 transform -translate-x-full lg:translate-x-0 overflow-y-auto z-30 shadow-sm">
    <nav class="space-y-1">
        <a href="{{ route('boss.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('boss.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
            <i class="fas fa-chart-bar"></i>
            <span class="font-semibold">Dashboard</span>
        </a>

        <div class="mt-8 mb-4">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-4 mb-2">Manajemen</h3>
            <a href="{{ route('boss.employees.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('boss.employees.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-users"></i>
                <span class="font-semibold">Daftar Karyawan</span>
            </a>
            <a href="{{ route('boss.leave-approval.index') }}"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('boss.leave-approval.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-calendar-check"></i>
                    <span class="font-semibold">Persetujuan Cuti</span>
                </div>
                @if(($pendingLeaveCount ?? 0) > 0)
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.8)]"></span>
                @endif
            </a>
            <a href="{{ route('boss.deposit-approval.index') }}"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('boss.deposit-approval.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span class="font-semibold">Verifikasi Setor</span>
                </div>
                @if(($pendingDepositCount ?? 0) > 0)
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.8)]"></span>
                @endif
            </a>
            <a href="{{ route('boss.reports.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('boss.reports.index') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span class="font-semibold">Laporan Gaji</span>
            </a>
            <a href="{{ route('boss.reports.attendance') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('boss.reports.attendance') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-calendar-alt"></i>
                <span class="font-semibold">Laporan Kehadiran</span>
            </a>
            <a href="{{ route('boss.stock.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('boss.stock.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-warehouse"></i>
                <span class="font-semibold">Stok Inventori</span>
            </a>
        </div>

        <div class="mb-4">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-4 mb-2">Pengaturan</h3>
            <a href="{{ route('boss.payroll-settings.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('boss.payroll-settings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-cog"></i>
                <span class="font-semibold">Pengaturan Gaji</span>
            </a>
        </div>
    </nav>
</aside>