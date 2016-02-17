<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:brd="http://bom.us/sitemaps"
        brd:urlCount="{{$objects->count()}}"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

    @foreach($objects as $object)
        <url>
            <brd:title>{{trim($object['title'])}}</brd:title>
            <loc>{{trim($object['url'])}}</loc>
            <changefreq>{{$generator->frequency}}</changefreq>
            <priority>{{$generator->priority}}</priority>
        </url>
    @endforeach
</urlset>
