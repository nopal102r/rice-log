<aside
    class="w-64 bg-white border-r border-gray-100 min-h-screen p-4 hidden md:block fixed left-0 top-24 bottom-0 overflow-y-auto z-30 shadow-sm">
    <nav class="space-y-1">
        <a href="{{ route('employee.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('employee.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
            <i class="fas fa-chart-line"></i>
            <span class="font-semibold">Dashboard</span>
        </a>

        <div class="mt-8 mb-4">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-4 mb-2">Absensi</h3>
            <a href="{{ route('employee.absence.form', 'masuk') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('employee.absence.form') && request()->route('type') === 'masuk' ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-sign-in-alt"></i>
                <span class="font-semibold">Absen Masuk</span>
            </a>
            <a href="{{ route('employee.absence.form', 'keluar') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('employee.absence.form') && request()->route('type') === 'keluar' ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-sign-out-alt"></i>
                <span class="font-semibold">Absen Keluar</span>
            </a>
        </div>

        <div class="mb-4">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-4 mb-2">Cuti & Setor</h3>
            <a href="{{ route('employee.leave.create') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('employee.leave.create') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-calendar"></i>
                <span class="font-semibold">Pengajuan Cuti</span>
            </a>
            <a href="{{ route('employee.leave.my-submissions') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('employee.leave.my-submissions') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-list"></i>
                <span class="font-semibold">Status Cuti</span>
            </a>
            <a href="{{ route('employee.deposit.create') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('employee.deposit.create') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-shopping-bag"></i>
                <span class="font-semibold">Setoran</span>
            </a>
            <a href="{{ route('employee.deposit.my-deposits') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('employee.deposit.my-deposits') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-history"></i>
                <span class="font-semibold">Riwayat Setor</span>
            </a>
        </div>
    </nav>
</aside>
