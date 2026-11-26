@extends('admin.layout')
@section('title', 'Lens options')
@section('actions')<a href="{{ route('admin.lens-options.create') }}" class="a-btn-primary">+ Add lens option</a>@endsection
@section('content')
<p class="mb-4 max-w-2xl text-sm text-muted">These lens choices appear on every product with "Prescription lenses available" turned on. The price is added on top of the frame price.</p>
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[600px]">
        <thead><tr><th>#</th><th>Lens</th><th>Price</th><th>Prescription</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @forelse ($lenses as $l)
                <tr><td class="text-muted">{{ $l->sort_order }}</td><td><p class="font-semibold">{{ $l->name }}</p><p class="text-xs text-muted">{{ $l->description }}</p></td><td>{{ $l->price > 0 ? '+ '.money($l->price) : 'Included' }}</td><td>{{ $l->requires_prescription ? 'Required' : 'Optional' }}</td><td><x-admin.status :status="$l->is_active ? 'active' : 'inactive'"/></td><td class="space-x-3 text-right"><a href="{{ route('admin.lens-options.edit', $l) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.lens-options.destroy', $l)"/></td></tr>
            @empty
                <x-admin.empty colspan="6"/>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
