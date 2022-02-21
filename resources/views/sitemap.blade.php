<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($cats as $cat)
        <url>
            <loc>{{ route('cat', $cat) }}</loc>
        </url>
    @endforeach
    @foreach ($goods as $good)
        <url>
            <loc>{{ route('good', $good) }}</loc>
            <lastmod>{{ $good->created_at->format('Y-m-d') }}</lastmod>
        </url>
    @endforeach
</urlset>
