@extends('admin.layout')
@section('title', 'Customers')
@section('content')
<div class="mb-4"><x-admin.search placeholder="Name, email or phone" :filters="['status' => ['Any status', ['active' => 'Active', 'inactive' => 'Blocked']]]"/></div>
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[720px]">
        <thead><tr><th>Customer</th><th>Phone</th><th>Orders</th><th>Total spent</th><th>Joined</th><th>Status</th></tr></thead>
        <tbody>
            @forelse ($customers as $c)
                <tr class="cursor-pointer" onclick="location='{{ route('admin.customers.show', $c) }}'"><td><p class="font-semibold">{{ $c->name }}</p><p class="text-xs text-muted">{{ $c->email }}</p></td><td>{{ $c->phone ?? '—' }}</td><td>{{ $c->orders_count }}</td><td class="font-semibold">{{ money($c->spent ?? 0) }}</td><td class="text-muted">{{ $c->created_at->format('d M Y') }}</td><td><x-admin.status :status="$c->is_active ? 'active' : 'inactive'"/></td></tr>
            @empty
                <x-admin.empty colspan="6" text="No customers yet. Customers appear here when they create an account."/>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $customers->links() }}</div>
@endsection
