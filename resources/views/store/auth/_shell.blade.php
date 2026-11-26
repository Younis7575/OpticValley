<section class="container-x grid min-h-[70vh] place-items-center py-16">
    <div class="w-full max-w-md">
        <div class="text-center">
            <h1 class="text-4xl">{{ $heading }}</h1>
            @isset($sub)<p class="mt-2 text-sm text-muted">{!! $sub !!}</p>@endisset
        </div>
        <div class="card mt-8 p-7 md:p-9">{{ $slot }}</div>
    </div>
</section>
