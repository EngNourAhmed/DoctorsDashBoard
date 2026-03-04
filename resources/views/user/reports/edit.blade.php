@extends('layouts.user')

@section('title', 'Edit case')
@section('header', 'Edit case')

@section('content')
    <div class="max-w-xl mx-auto rounded-2xl bg-slate-900/80 border border-slate-700/70 p-6 text-xs">
        <h2 class="text-base md:text-lg font-semibold mb-4">Edit case</h2>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-2 text-xs text-red-200">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('user.reports.update', $report) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="title" class="block mb-1">Patient Name</label>
                <input id="title" name="title" type="text" value="{{ old('title', $report->title) }}" required
                    class="w-full rounded-lg border border-slate-600 bg-slate-950/60 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
            </div>
            <div>
                <label for="description" class="block mb-1">Description (optional)</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full rounded-lg border border-slate-600 bg-slate-950/60 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">{{ old('description', $report->description) }}</textarea>
            </div>
            <div class="space-y-1">
                <p class="text-slate-300">Current file:
                    <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank"
                        class="text-amber-300 hover:text-amber-200">{{ $report->original_name }}</a>
                </p>
                <label for="file" class="block mb-1">Replace file (optional)</label>
                <input id="file" name="file" type="file"
                    class="w-full text-xs text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-amber-400 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-black hover:file:bg-amber-300">
            </div>
            <button type="submit"
                class="btn btn-yellow">Update
                case</button>
        </form>
    </div>
@endsection
