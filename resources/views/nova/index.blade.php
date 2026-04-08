@extends('layouts.nova')

@section('content')
<div class="flex items-center justify-between mb-8">
    <h2 class="text-2xl font-bold text-white">{{ $resource::label() }}</h2>
    <a href="/admin/resources/{{ $resource::uriKey() }}/create" class="px-6 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 font-medium transition active:scale-95">
        Create {{ $resource::label() }}
    </a>
</div>

<div class="overflow-hidden glass rounded-2xl shadow-xl transition hover:shadow-blue-500/10">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-800/50 border-b border-slate-800">
                @foreach($resource->fields() as $field)
                    @if($field->showOnIndex)
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 capitalize">{{ $field->name }}</th>
                    @endif
                @endforeach
                <th class="px-6 py-4 text-slate-400 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
            @foreach($models as $model)
                <tr class="hover:bg-slate-800/30 transition-colors">
                    @foreach($resource->fields() as $field)
                        @if($field->showOnIndex)
                            <td class="px-6 py-4 text-sm font-medium">
                                {{ $model->{$field->attribute} }}
                            </td>
                        @endif
                    @endforeach
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="/admin/resources/{{ $resource::uriKey() }}/{{ $model->id }}/edit" class="text-blue-400 hover:text-blue-300 transition text-sm">Edit</a>
                        <form action="/admin/resources/{{ $resource::uriKey() }}/{{ $model->id }}" method="POST" class="inline" onsubmit="return confirm('Silently delete this item?');">
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-400 transition text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            
            @if(count($models) === 0)
                <tr>
                    <td colspan="100%" class="px-6 py-12 text-center text-slate-500 italic">
                        No {{ strtolower($resource::label()) }} found. Start by creating one.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
