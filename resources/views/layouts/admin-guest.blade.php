<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · {{ config('app.name') }} Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    @vite('resources/css/app.css')
</head>
{{-- Admin pages for signed-out visitors (the login screen). --}}
<body class="flex min-h-screen flex-col items-center justify-center bg-zinc-900 px-4 py-10 font-sans text-zinc-900 antialiased">
    <main class="w-full max-w-sm">
        <div class="mb-8 flex justify-center text-zinc-300"><x-admin.brand /></div>

        <div class="rounded-2xl bg-white p-6 shadow-xl sm:p-8">
            @if (session('status'))
                <p class="mb-6 rounded-lg bg-brand-50 px-4 py-3 text-sm text-brand-700" role="status">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>

        <p class="mt-6 text-center text-sm text-zinc-400">
            <a href="{{ route('home') }}" class="hover:text-white">&larr; Back to the store</a>
        </p>
    </main>
</body>
</html>
