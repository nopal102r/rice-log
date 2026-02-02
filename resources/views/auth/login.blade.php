<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rice Log System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div
                    class="gradient-bg text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-seedling text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Rice Log</h1>
                <p class="text-gray-600 text-sm">Sistem Absensi Pabrik Beras</p>
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

            <!-- Demo Credentials -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-gray-700 text-sm font-bold mb-4">Demo Akun:</p>
                <div class="space-y-2 bg-gray-50 p-4 rounded text-sm">
                    <div>
                        <p class="text-gray-600"><strong>Bos:</strong></p>
                        <p class="text-gray-700">Email: bos@ricemail.com</p>
                        <p class="text-gray-700">Password: password</p>
                    </div>
                    <div class="pt-2 border-t">
                        <p class="text-gray-600"><strong>Karyawan:</strong></p>
                        <p class="text-gray-700">Lihat di database seeder</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>