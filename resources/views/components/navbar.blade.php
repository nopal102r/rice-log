<nav class="bg-white border-b border-gray-100 text-gray-800 px-8 flex items-center shadow-sm fixed top-0 left-0 right-0 z-40 w-full h-24">
    <div class="flex justify-between items-center w-full">
        <div class="flex items-center gap-4">
            <img src="{{ asset('image/logo 2.png') }}" alt="Logo" class="h-16 md:h-20 transition-all duration-300">
        </div>

        <div class="flex items-center gap-6">
            @if(auth()->check())
                <div class="relative group">
                    <button class="flex items-center gap-2 hover:bg-gray-100 px-3 py-2 rounded-lg transition-colors text-gray-700">
                        <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                        <span class="font-semibold">{{ auth()->user()->name }}</span>
                    </button>
                    <div
                        class="absolute right-0 top-full pt-2 w-48 bg-white text-gray-800 rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto z-50 transition-opacity duration-200">
                        <div class="bg-white rounded shadow-lg">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-100">
                                <i class="fas fa-user mr-2 text-blue-600"></i> Profil Saya
                            </a>
                            <form method="POST" action="/logout" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 border-t font-bold text-red-600">
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

