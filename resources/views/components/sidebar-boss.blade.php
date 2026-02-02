<aside class="w-64 bg-gray-900 text-white min-h-screen p-4 hidden md:block">
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
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.leave-approval.*') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>Persetujuan Cuti</span>
            </a>
            <a href="{{ route('boss.deposit-approval.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('boss.deposit-approval.*') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-check-circle"></i>
                <span>Verifikasi Setor</span>
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