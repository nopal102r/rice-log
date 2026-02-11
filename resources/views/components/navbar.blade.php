<nav class="bg-white border-b border-gray-100 text-gray-800 px-8 flex items-center shadow-sm fixed top-0 left-0 right-0 z-40 w-full h-24">
    <div class="flex justify-between items-center w-full">
        <div class="flex items-center gap-4">
            <img src="{{ asset('image/logo 2.png') }}" alt="Logo" class="h-16 md:h-20 transition-all duration-300">
        </div>

        <div class="flex items-center gap-6">
            @if(auth()->check())
                <div class="relative group">
                    <button class="flex items-center gap-2 hover:bg-gray-100 px-3 py-2 rounded-lg relative transition-colors text-gray-600">
                        <i class="fas fa-bell text-lg"></i>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                            <span id="notification-badge" class="absolute top-1 right-2 bg-red-500 text-[10px] text-white rounded-full px-1.5 py-0.5 border-2 border-white font-black animate-pulse">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-72 bg-white text-gray-800 rounded-xl shadow-2xl opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto z-50 overflow-hidden transition-all duration-300 transform origin-top-right scale-95 group-hover:scale-100 border border-gray-100">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                            <h4 class="font-black text-xs uppercase tracking-widest text-gray-500">Notifikasi</h4>
                            @if(($unreadNotificationsCount ?? 0) > 0)
                                <span class="bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $unreadNotificationsCount }} Baru</span>
                            @endif
                        </div>
                        <div id="notification-list" class="max-h-96 overflow-y-auto">
                            <p class="p-8 text-center text-gray-400 text-sm italic">Memuat notifikasi...</p>
                        </div>
                        <div class="bg-white p-2 border-t border-gray-100 text-center">
                            <a href="#" class="text-[10px] font-bold text-blue-600 hover:underline uppercase tracking-widest">Lihat Semua</a>
                        </div>
                    </div>
                </div>

                <div class="relative group">
                    <button class="flex items-center gap-2 hover:bg-gray-100 px-3 py-2 rounded-lg transition-colors text-gray-700">
                        <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                        <span class="font-semibold">{{ auth()->user()->name }}</span>
                    </button>
                    <div
                        class="absolute right-0 top-full pt-2 w-48 bg-white text-gray-800 rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto z-50 transition-opacity duration-200">
                        <div class="bg-white rounded shadow-lg">
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profil
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationList = document.getElementById('notification-list');
        const badge = document.getElementById('notification-badge');
        
        async function fetchNotifications() {
            try {
                const response = await fetch('/notifications');
                const data = await response.json();
                
                if (data.notifications && data.notifications.length > 0) {
                    notificationList.innerHTML = data.notifications.map(n => `
                        <div class="p-4 border-b border-gray-50 hover:bg-blue-50 cursor-pointer transition-colors" onclick="markAsRead(${n.id}, this)">
                            <div class="flex justify-between items-start mb-1">
                                <h5 class="font-bold text-sm text-gray-800">${n.title}</h5>
                                <span class="text-[10px] text-gray-400">${n.created_at}</span>
                            </div>
                            <p class="text-xs text-gray-600 leading-relaxed">${n.message}</p>
                        </div>
                    `).join('');
                } else {
                    notificationList.innerHTML = '<p class="p-8 text-center text-gray-400 text-sm italic">Tidak ada notifikasi baru</p>';
                }
            } catch (err) {
                console.error('Failed to fetch notifications:', err);
            }
        }

        window.markAsRead = async function(id, element) {
            try {
                const response = await fetch(\`/notifications/\${id}/mark-as-read\`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    element.classList.add('opacity-50', 'bg-gray-50');
                    element.onclick = null;
                    // Optionally refresh the list or just hide the element
                    fetchNotifications();
                    
                    // Simple logic to decrement badge or hide if zero
                    if (badge) {
                        let count = parseInt(badge.innerText);
                        if (count > 1) {
                            badge.innerText = count - 1;
                        } else {
                            badge.remove();
                        }
                    }
                }
            } catch (err) {
                console.error('Failed to mark notification as read:', err);
            }
        };

        // Initial fetch
        fetchNotifications();
        
        // Poll every 60 seconds
        setInterval(fetchNotifications, 60000);
    });
</script>