<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rice Log System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3b82f6;
            --primary-green: #10b981;
            --primary-red: #ef4444;
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-green) 100%);
        }

        /* Animated Rice Background */
        .rice-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: #f8fafc;
            overflow: hidden;
            pointer-events: none;
        }

        .rice-particle {
            position: absolute;
            color: var(--primary-green);
            opacity: 0.15;
            font-size: 24px;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(110vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.15; }
            90% { opacity: 0.15; }
            100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
        }

        /* Rice Decorations */
        .rice-decor-left, .rice-decor-right {
            position: fixed;
            bottom: -20px;
            font-size: 15rem;
            color: var(--primary-green);
            opacity: 0.05;
            z-index: -1;
            pointer-events: none;
        }
        .rice-decor-left { left: -50px; transform: rotate(15deg); }
        .rice-decor-right { right: -50px; transform: rotate(-15deg); }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="bg-gray-100 overflow-hidden">
    <!-- Rice Decorations/Background -->
    <div class="rice-background" id="rice-bg"></div>
    <i class="fas fa-seedling rice-decor-left"></i>
    <i class="fas fa-wheat-awn rice-decor-right"></i>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full glass-card rounded-3xl shadow-2xl p-10 border border-white/50 relative overflow-hidden">
            <!-- Subtle internal decoration -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-green-50 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-50"></div>

            <div class="relative z-10">
            <div class="text-center mb-8 md:mb-10">
                <img src="{{ asset('image/logo 1.png') }}" alt="Logo Main" class="h-24 md:h-32 mx-auto mb-3 drop-shadow-md">
                <img src="{{ asset('image/logo 3.png') }}" alt="Logo Tagline" class="h-10 md:h-14 mx-auto opacity-90">
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-600"></i> Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                        placeholder="example@mail.com">
                    @error('email')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-lock mr-2 text-blue-600"></i> Password
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                        placeholder="Masukkan password">
                    @error('password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" value="on" class="rounded border-gray-300">
                    <label for="remember" class="ml-2 text-gray-700 text-sm">Ingat saya</label>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full gradient-bg text-white font-bold py-2 rounded hover:opacity-90 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bg = document.getElementById('rice-bg');
            const icons = ['fa-seedling', 'fa-wheat-awn', 'fa-spa'];
            
            for (let i = 0; i < 25; i++) {
                const particle = document.createElement('i');
                const icon = icons[Math.floor(Math.random() * icons.length)];
                particle.className = `fas ${icon} rice-particle`;
                
                particle.style.left = Math.random() * 100 + 'vw';
                particle.style.animationDelay = Math.random() * 20 + 's';
                particle.style.fontSize = (20 + Math.random() * 30) + 'px';
                particle.style.opacity = (0.05 + Math.random() * 0.1).toString();
                
                bg.appendChild(particle);
            }
        });
    </script>
</body>

</html>