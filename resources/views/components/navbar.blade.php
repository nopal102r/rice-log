<nav class="gradient-bg text-white px-6 py-4 shadow-lg fixed top-0 left-0 right-0 z-40 w-full">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fas fa-seedling text-2xl"></i>
            <h1 class="text-2xl font-bold">Rice Log</h1>
        </div>

        <div class="flex items-center gap-6">
            @if(auth()->check())
                <div class="relative group">
                    <button class="flex items-center gap-2 hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">
                        <i class="fas fa-bell"></i>
                        <span id="notification-count" class="bg-red-500 text-xs rounded-full px-2 py-1 hidden">0</span>
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-64 bg-white text-gray-800 rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto z-50 max-h-96 overflow-y-auto">
                        <div id="notification-list" class="p-4">
                            <p class="text-center text-gray-500 text-sm">Tidak ada notifikasi</p>
                        </div>
                    </div>
                </div>

                <div class="relative group">
                    <button class="flex items-center gap-2 hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">
                        <i class="fas fa-user-circle text-2xl"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </button>
                    <div
                        class="absolute right-0 top-full pt-2 w-48 bg-white text-gray-800 rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto z-50 transition-opacity duration-200">
                        <div class="bg-white rounded shadow-lg">
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profil
                            </a>
                            <form method="POST" action="/logout" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 border-t">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</nav>