@extends('layouts.guest')

@section('content')
    <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-md">
            <div class="glass-card rounded-[32px] p-8">
                <div class="mb-8">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Deesa UCAT AI</p>
                    <h1 class="mt-4 font-display text-4xl font-semibold text-slate-950">Create account</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-600">Start with a standard email and password sign-up flow.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                        <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center">Create account</button>
                </form>

                <p class="mt-6 text-sm text-slate-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-sky-700">Login</a>
                </p>
            </div>
        </div>
    </div>
@endsection
