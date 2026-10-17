@extends('layouts.admin')

@section('title', 'Dashboard')

@section('actions')
    <a href="{{ route('admin.products.create') }}" class="inline-flex h-11 items-center gap-2 rounded-full bg-brand-600 px-5 text-sm font-semibold text-white hover:bg-brand-700">
        <x-icon name="plus" size="18" /> Add product
    </a>
@endsection

@section('content')
    <ul class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        @foreach ($stats as [$label, $value, $icon, $tone])
            <li class="flex flex-col items-start gap-3 rounded-xl border border-zinc-200 bg-white p-4 sm:flex-row sm:items-center sm:gap-4 sm:p-5">
                <span @class([
                    'flex h-10 w-10 shrink-0 items-center justify-center rounded-full sm:h-12 sm:w-12',
                    'bg-brand-50 text-brand-600' => $tone === 'brand',
                    'bg-green-50 text-green-700' => $tone === 'green',
                    'bg-red-50 text-red-700' => $tone === 'red',
                    'bg-amber-50 text-amber-700' => $tone === 'amber',
                ])><x-icon :name="$icon" size="22" /></span>
                <div>
                    <p class="text-sm text-zinc-500">{{ $label }}</p>
                    <p class="text-3xl font-bold">{{ $value ?? '–' }}</p>
                </div>
            </li>
        @endforeach
    </ul>

    <section class="mt-8 rounded-xl border border-zinc-200 bg-white p-6">
        <h2 class="font-semibold">Manage the store</h2>
        <p class="mt-1 text-sm text-zinc-500">Products you add, edit or hide here show on the store straight away.</p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('admin.products.index') }}" class="inline-flex h-11 items-center gap-2 rounded-full border border-zinc-300 px-5 text-sm font-semibold hover:border-zinc-400">
                <x-icon name="box" size="18" /> All products
            </a>
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="inline-flex h-11 items-center gap-2 rounded-full border border-zinc-300 px-5 text-sm font-semibold hover:border-zinc-400">
                <x-icon name="external" size="18" /> View store
            </a>
        </div>
    </section>
@endsection
