<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Rice Log System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <style>
        :root {
            --primary-red: #ef4444;
            --primary-blue: #3b82f6;
            --primary-green: #10b981;
            --bg-white: #ffffff;
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-green) 100%);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px -10px rgba(59, 130, 246, 0.3);
        }

        /* Animated Rice Background */
        .rice-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: #f8fafc;
            overflow: hidden;
            pointer-events: none;
        }

        .rice-particle {
            position: absolute;
            color: var(--primary-green);
            opacity: 0.15;
            font-size: 24px;
            animation: float 15s linear infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(110vh) rotate(0deg);
                opacity: 0;
            }
            10% { opacity: 0.15; }
            90% { opacity: 0.15; }
            100% {
                transform: translateY(-10vh) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
    @yield('extra-css')
</head>

<body class="bg-gray-50">
    <!-- Animated Rice Background -->
    <div class="rice-background" id="rice-bg"></div>

    @include('components.navbar')

    <div class="flex pt-28">
        @if(auth()->check())
            @if(auth()->user()->isBoss())
                @include('components.sidebar-boss')
            @else
                @include('components.sidebar-employee')
            @endif
        @endif

        <main class="flex-1 p-4 md:p-8 md:ml-64">
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bg = document.getElementById('rice-bg');
            const icons = ['fa-seedling', 'fa-wheat-awn']; // Font Awesome rice-like icons
            
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('i');
                const icon = icons[Math.floor(Math.random() * icons.length)];
                particle.className = `fas ${icon} rice-particle`;
                
                // Random position and animation
                particle.style.left = Math.random() * 100 + 'vw';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.fontSize = (15 + Math.random() * 20) + 'px';
                
                bg.appendChild(particle);
            }
        });
    </script>
    @yield('extra-js')
</body>

</html>