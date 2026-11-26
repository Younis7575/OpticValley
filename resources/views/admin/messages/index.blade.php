@extends('admin.layout')
@section('title', 'Contact messages')
@section('content')
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[620px]">
        <thead><tr><th>From</th><th>Subject</th><th>Received</th><th></th></tr></thead>
        <tbody>
            @forelse ($messages as $m)<tr class="cursor-pointer {{ $m->is_read ? '' : 'font-semibold' }}" onclick="location='{{ route('admin.messages.show', $m) }}'"><td>@unless ($m->is_read)<span class="mr-1 inline-block size-2 rounded-full bg-gold"></span>@endunless{{ $m->name }}<div class="text-xs font-normal text-muted">{{ $m->email }}</div></td><td>{{ $m->subject ?: \Illuminate\Support\Str::limit($m->message, 60) }}</td><td class="font-normal text-muted">{{ $m->created_at->diffForHumans() }}</td><td class="text-right text-xs text-muted">Open →</td></tr>@empty<x-admin.empty colspan="4" text="No messages yet."/>@endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $messages->links() }}</div>
@endsection
