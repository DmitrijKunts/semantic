@php
$yt = $yts ? $yts->shift() : null;
@endphp
@if ($yt)
    <div class="p-4 md:w-4/5 mx-auto">
        <div class="p-2 h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
            <h2 class="text-center title-font text-lg font-medium text-gray-900 mb-3">
                {{ $yt->title }}</h2>
            <object class="lozad aspect-video w-full" data="/css/loading.gif" data-data="{{ $yt->url }} "></object>
            <p class="leading-relaxed mb-3">
                {{ $yt->snippet }}
            </p>
        </div>
    </div>
@endif
