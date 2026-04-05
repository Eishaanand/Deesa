@extends('layouts.guest')

@section('content')
    <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-md">
            <div class="glass-card rounded-[32px] p-8">
                <div class="mb-8">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Deesa UCAT AI</p>
                    <h1 class="mt-4 font-display text-4xl font-semibold text-slate-950">Reset password</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-600">Choose a new password for your account.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">New password</label>
                        <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center">Reset password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
