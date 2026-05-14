<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VistáBarangay</title>
    <meta name="description" content="Sign in to the VistáBarangay Document Request System administration panel.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f0f7ff', 100: '#e0effe', 200: '#bae0fd', 300: '#7cd0fd',
                            400: '#36bffa', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1',
                            800: '#075985', 900: '#0c4a6e', 950: '#082f49',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .login-bg {
            background: radial-gradient(circle at 30% 20%, rgba(14, 165, 233, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(2, 132, 199, 0.1) 0%, transparent 40%),
                        linear-gradient(135deg, #082f49 0%, #0c4a6e 50%, #075985 100%);
            min-height: 100vh;
        }
        .login-card {
            animation: slideUp 0.5s ease-out;
            backdrop-filter: blur(20px);
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-field {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-field:focus {
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
            border-color: #0ea5e9;
            outline: none;
            background: #ffffff;
        }
    </style>
</head>
<body class="login-bg flex items-center justify-center p-4 antialiased selection:bg-brand-500 selection:text-white">

    {{-- Subtle grid overlay --}}
    <div class="fixed inset-0 opacity-[0.03] bg-[linear-gradient(to_right,#fff_1px,transparent_1px),linear-gradient(to_bottom,#fff_1px,transparent_1px)] bg-[size:4rem_4rem] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        {{-- Logo Area --}}
        <div class="text-center mb-8">
            <img src="{{ asset('New Logo.png') }}" alt="VistáBarangay Logo" class="w-20 h-20 rounded-full object-cover mx-auto mb-4 shadow-xl ring-4 ring-white/15">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Vistá<span class="text-brand-300">Barangay</span></h1>
            <p class="text-brand-200/70 text-sm mt-1 font-medium">Document Request System</p>
        </div>

        {{-- Login Card --}}
        <div class="login-card bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-1">Welcome Back</h2>
            <p class="text-slate-400 text-sm mb-6 font-medium">Sign in to your admin account</p>

            @if($errors->any())
                <x-alert type="error" title="Sign In Failed" class="mb-5 rounded-xl shadow-none backdrop-blur-0">
                    {{ $errors->first() }}
                </x-alert>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input-field w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium bg-slate-50/80 text-slate-800 placeholder:text-slate-400"
                           placeholder="admin@vistabarangay.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required
                           class="input-field w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium bg-slate-50/80 text-slate-800 placeholder:text-slate-400"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <label for="remember" class="ml-2 text-sm text-slate-500 font-medium">Remember me</label>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-brand-600 to-brand-500 text-white py-3.5 rounded-xl font-bold text-sm hover:from-brand-500 hover:to-brand-400 transition-all duration-300 shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5 uppercase tracking-wider">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-brand-200/40 text-xs mt-6 font-medium">&copy; {{ date('Y') }} VistáBarangay. All rights reserved.</p>
    </div>

</body>
</html>
