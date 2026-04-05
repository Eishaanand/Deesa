@extends('layouts.guest')

@section('content')
    <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-md">
            <div class="glass-card rounded-[32px] p-8">
                <div class="mb-8">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Deesa UCAT AI</p>
                    <h1 class="mt-4 font-display text-4xl font-semibold text-slate-950">Forgot password</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-600">Enter your email and we will send you a password reset link.</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center">Send reset link</button>
                </form>

                <p class="mt-6 text-sm text-slate-600">
                    Remembered your password?
                    <a href="{{ route('login') }}" class="font-medium text-sky-700">Back to login</a>
                </p>
            </div>
        </div>
    </div>
@endsection
