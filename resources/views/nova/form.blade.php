@extends('layouts.nova')

@section('content')
<div class="flex items-center space-x-4 mb-8">
    <a href="/admin/resources/{{ $resource::uriKey() }}" class="text-slate-400 hover:text-white transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <h2 class="text-2xl font-bold text-white">{{ $title }}</h2>
</div>

<div class="max-w-4xl glass rounded-2xl p-8 shadow-xl">
    <form action="{{ $model ? "/admin/resources/{$resource::uriKey()}/{$model->id}" : "/admin/resources/{$resource::uriKey()}" }}" method="POST" class="space-y-6">
        @if($model)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($resource->fields() as $field)
                @if($field->showOnForm)
                    <div class="space-y-2">
                        <label for="{{ $field->attribute }}" class="block text-sm font-semibold text-slate-300 capitalize">
                            {{ $field->name }}
                        </label>
                        
                        @if($field->type === 'textarea')
                            <textarea id="{{ $field->attribute }}" name="{{ $field->attribute }}" rows="4" 
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-white placeholder-slate-500"
                            >{{ $model ? $model->{$field->attribute} : '' }}</textarea>
                        @elseif($field->type === 'select')
                            <select id="{{ $field->attribute }}" name="{{ $field->attribute }}" 
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-white"
                            >
                                @foreach($field->options ?? [] as $value => $label)
                                    <option value="{{ $value }}" {{ $model && $model->{$field->attribute} == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="{{ $field->type }}" id="{{ $field->attribute }}" name="{{ $field->attribute }}" 
                                value="{{ $model ? $model->{$field->attribute} : '' }}"
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-white placeholder-slate-500"
                            >
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <div class="flex items-center justify-end pt-8 border-t border-slate-800">
            <button type="submit" class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 font-bold transition active:scale-95 shadow-lg shadow-blue-500/20">
                {{ $model ? 'Update' : 'Create' }} Resource
            </button>
        </div>
    </form>
</div>
@endsection
