<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Warkop Mugi Berkah</title>
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Google Font: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        warkop: '#b91c1c', // Merah Khas Warkop
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white h-screen w-full flex items-center justify-center">

    {{-- Container Utama (Tanpa Border, Tanpa Shadow/Card) --}}
    <div class="w-full max-w-sm px-6 sm:px-8">
        
        {{-- 1. HEADER / LOGO --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-48 h-38 rounded-3xl text-warkop animate-pulse-slow">
                <img src="assets/image.png" alt="">
              </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Warkop Mugi Berkah</h1>
            <p class="text-sm text-gray-500 mt-2">Masuk untuk mengelola kasir</p>
        </div>

        {{-- ALERT ERROR --}}
        @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 flex gap-3 items-start">
            <i class="fa-solid fa-circle-exclamation text-warkop mt-0.5 text-sm"></i>
            <div class="text-xs text-red-800 font-medium">
                <span class="block font-bold mb-1">Login Gagal</span>
                <ul class="list-disc list-inside opacity-80">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- 2. FORM --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            
            {{-- Input Username --}}
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-gray-900 ml-1 uppercase tracking-wide">Username</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-warkop transition-colors">
                        <i class="fa-solid fa-user text-sm"></i>
                    </div>
                    {{-- Style Input: Background abu-abu muda, tanpa border kasar --}}
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                           class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-warkop/20 text-gray-800 font-semibold placeholder-gray-400 transition-all outline-none"
                           placeholder="Masukkan username">
                </div>
            </div>

            {{-- Input Password --}}
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-gray-900 ml-1 uppercase tracking-wide">Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-warkop transition-colors">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input type="password" name="password" id="passwordInput" required
                           class="w-full pl-10 pr-12 py-3.5 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-warkop/20 text-gray-800 font-semibold placeholder-gray-400 transition-all outline-none"
                           placeholder="Masukkan password">
                    
                    {{-- Toggle Eye Icon --}}
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer">
                        <i class="fa-regular fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            {{-- Remember & Forgot --}}
            <div class="flex items-center justify-between py-1">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-warkop border-gray-300 focus:ring-warkop">
                    <span class="ml-2 text-xs font-medium text-gray-500">Ingat saya</span>
                </label>
                {{-- <a href="#" class="text-xs font-bold text-warkop hover:underline">Lupa password?</a> --}}
            </div>

            {{-- Button --}}
            <button type="submit" class="w-full py-4 bg-warkop hover:bg-red-800 text-white rounded-xl font-bold shadow-lg shadow-red-200 hover:shadow-red-300 transform active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                <span>Masuk Sekarang</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>

        </form>
        
        {{-- Footer --}}
        <div class="mt-12 text-center">
            <p class="text-[10px] text-gray-300 font-medium">
                &copy; {{ date('Y') }} Warkop Mugi Berkah &bull; POS System v1.0
            </p>
        </div>

    </div>

    {{-- Script Toggle Password --}}
    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>