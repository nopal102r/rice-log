<aside class="w-64 bg-gray-900 text-white min-h-screen p-4 hidden md:block">
    <nav class="space-y-2">
        <a href="{{ route('employee.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('employee.dashboard') ? 'bg-blue-600' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

        <div class="mt-6 mb-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase px-4 mb-2">Absensi</h3>
            <a href="{{ route('employee.absence.form', 'masuk') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('employee.absence.form') && request()->route('type') === 'masuk' ? 'bg-blue-600' : '' }}">
                <i class="fas fa-sign-in-alt"></i>
                <span>Absen Masuk</span>
            </a>
            <a href="{{ route('employee.absence.form', 'keluar') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('employee.absence.form') && request()->route('type') === 'keluar' ? 'bg-blue-600' : '' }}">
                <i class="fas fa-sign-out-alt"></i>
                <span>Absen Keluar</span>
            </a>
        </div>

        <div class="mb-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase px-4 mb-2">Cuti & Setor</h3>
            <a href="{{ route('employee.leave.create') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('employee.leave.create') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-calendar"></i>
                <span>Pengajuan Cuti</span>
            </a>
            <a href="{{ route('employee.leave.my-submissions') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('employee.leave.my-submissions') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-list"></i>
                <span>Status Cuti</span>
            </a>
            <a href="{{ route('employee.deposit.create') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('employee.deposit.create') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-shopping-bag"></i>
                <span>Setor Beras</span>
            </a>
            <a href="{{ route('employee.deposit.my-deposits') }}"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-700 {{ request()->routeIs('employee.deposit.my-deposits') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-history"></i>
                <span>Riwayat Setor</span>
            </a>
        </div>
    </nav>
</aside>