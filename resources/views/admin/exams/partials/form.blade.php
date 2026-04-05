<div class="glass-card mx-auto max-w-3xl rounded-[32px] p-8">
    <h1 class="font-display text-4xl font-semibold text-slate-950">{{ $title }}</h1>
    <form class="mt-8 space-y-5" method="POST" action="{{ $action }}" autocomplete="off">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Title</label>
            <input class="w-full rounded-2xl border-slate-200 bg-white/80" name="title" value="{{ old('title', $exam?->title ?? '') }}" placeholder="Enter exam title" autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false">
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Description</label>
            <textarea class="w-full rounded-2xl border-slate-200 bg-white/80" rows="5" name="description" placeholder="Enter exam description" autocapitalize="sentences" autocorrect="off" spellcheck="false">{{ old('description', $exam?->description ?? '') }}</textarea>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                <select class="w-full rounded-2xl border-slate-200 bg-white/80" name="status">
                    @foreach (['draft', 'published', 'archived'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $exam?->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3 pt-8">
                <input id="is_ai_supported" type="checkbox" name="is_ai_supported" value="1" @checked(old('is_ai_supported', $exam?->is_ai_supported ?? true))>
                <label for="is_ai_supported" class="text-sm text-slate-700">Use Gemini generation. Turn this off to build from local cached questions instead.</label>
            </div>
        </div>
        <button class="btn-primary" type="submit">Save Exam</button>
    </form>
</div>
