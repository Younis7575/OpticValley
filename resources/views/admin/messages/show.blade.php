@extends('admin.layout')
@section('title', 'Message from '.$message->name)
@section('content')
<div class="a-card max-w-3xl p-6">
    <div class="flex flex-wrap justify-between gap-4 text-sm"><div><p class="font-bold">{{ $message->name }}</p><p class="text-muted"><a href="mailto:{{ $message->email }}" class="hover:underline">{{ $message->email }}</a>@if ($message->phone) · <a href="tel:{{ $message->phone }}">{{ $message->phone }}</a>@endif</p></div><p class="text-muted">{{ $message->created_at->format('d M Y, g:i a') }}</p></div>
    @if ($message->subject)<p class="mt-5 font-semibold">{{ $message->subject }}</p>@endif
    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-ink-2">{{ $message->message }}</p>
    <div class="mt-6 flex gap-3"><a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->subject ?: 'Your message to '.setting('store_name'))) }}" class="a-btn-primary">Reply by email</a><x-admin.delete :action="route('admin.messages.destroy', $message)" class="a-btn-danger"/></div>
</div>
@endsection
