<aside
    class="w-64 bg-gray-900 text-white min-h-screen p-4 hidden md:block fixed left-0 top-20 bottom-0 overflow-y-auto z-30">
    <nav class="space-y-2">
        <a href="{{ route('boss.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.dashboard') ? 'bg-blue-600' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Dashboard</span>
        </a>

        <div class="mt-6 mb-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase px-4 mb-2">Manajemen</h3>
            <a href="{{ route('boss.employees.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.employees.*') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-users"></i>
                <span>Daftar Karyawan</span>
            </a>
            <a href="{{ route('boss.leave-approval.index') }}"
                class="flex items-center justify-between px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.leave-approval.*') ? 'bg-blue-600' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-calendar-check"></i>
                    <span>Persetujuan Cuti</span>
                </div>
                @if(($pendingLeaveCount ?? 0) > 0)
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.8)]"></span>
                @endif
            </a>
            <a href="{{ route('boss.deposit-approval.index') }}"
                class="flex items-center justify-between px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.deposit-approval.*') ? 'bg-blue-600' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span>Verifikasi Setor</span>
                </div>
                @if(($pendingDepositCount ?? 0) > 0)
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.8)]"></span>
                @endif
            </a>
            <a href="{{ route('boss.reports.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.reports.index') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Laporan Gaji</span>
            </a>
            <a href="{{ route('boss.reports.attendance') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.reports.attendance') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Laporan Kehadiran</span>
            </a>
            <a href="{{ route('boss.stock.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.stock.*') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-warehouse"></i>
                <span>Stok Inventori</span>
            </a>
        </div>

        <div class="mb-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase px-4 mb-2">Pengaturan</h3>
            <a href="{{ route('boss.payroll-settings.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.payroll-settings.*') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan Gaji</span>
            </a>
        </div>
    </nav>
</aside>