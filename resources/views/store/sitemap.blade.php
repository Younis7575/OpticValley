{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($urls as $u)
    <url><loc>{{ $u }}</loc><changefreq>weekly</changefreq></url>
@endforeach
@foreach ($products as $p)
    <url><loc>{{ route('product.show', $p) }}</loc><lastmod>{{ $p->updated_at->toAtomString() }}</lastmod><changefreq>weekly</changefreq>@if ($p->primaryImage)<image:image><image:loc>{{ $p->primaryImage->url }}</image:loc><image:title>{{ $p->name }}</image:title></image:image>@endif</url>
@endforeach
</urlset>
