@unless($breadcrumbs->isEmpty())
    <nav class="flex py-4" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">

            @foreach ($breadcrumbs as $breadcrumb)
                @if ($loop->first)
                    <li>
                        <div>
                            <a rel="nofollow" href="{{ $breadcrumb->url }}" class="text-gray-400 hover:text-gray-500">
                                <!-- Heroicon name: solid/home -->
                                <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                                <span class="sr-only">Home</span>
                            </a>
                        </div>
                    </li>
                @else
                    <li>
                        <div class="flex items-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            @if (isset($breadcrumb->url))
                                <a href="{{ $breadcrumb->url }}"
                                    class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                                    aria-current="page">{{ $breadcrumb->title }}</a>
                            @else
                                <div class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700" aria-current="page">
                                    {{ $breadcrumb->title }}</div>
                            @endif

                        </div>
                    </li>
                @endif
            @endforeach

        </ol>
    </nav>
@endunless