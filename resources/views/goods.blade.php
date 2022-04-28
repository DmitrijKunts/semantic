<div class="text-gray-600 body-font">
    <div class="container px-5 pb-4 mx-auto">
        <div class="flex flex-wrap">
            @php
                $yts =
                    $cat ?? null
                        ? $cat->youtubesUniq()
                        : $good
                            ->cats()
                            ->first()
                            ->youtubesUniq();
            @endphp
            @include('youtube')
            @foreach ($goods as $good)
                @if ($loop->index % 3 == 0 && $loop->index != 0)
                    @include('youtube')
                @endif

                <div class="p-4 md:w-1/3">
                    <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                        <a rel="nofollow" href="{{ route('good', $good) }}">
                            <img srcset="/css/loading.gif 320w" sizes="100vw"
                                class="lozad lg:h-48 md:h-36 w-full object-cover object-center"
                                data-srcset="{!! $good->thumbnail() !!} 320w" src="{!! $good->thumbnail() !!}"
                                alt="{{ $good->name }}">
                        </a>
                        <div class="p-6">
                            @if (config('app.debug'))
                                <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                                    [RANK: {{ $good->pivot->rank }}, PicCount: {{ $good->pictures()->count() }}]
                                </h2>
                            @endif
                            <h3 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                                {{ $good->vendor }}</h3>
                            <h2 class="title-font text-lg font-medium text-gray-900 mb-3"><a
                                    href="{{ route('good', $good) }}">{{ $good->nameKey($cat ?? null) }}</a>
                            </h2>
                            <p class="leading-relaxed mb-3">
                                {{ $good->descKey($cat ?? null) }}
                            </p>
                            <div class="flex items-center flex-wrap ">
                                <a rel="nofollow" href="{{ route('good', $good) }}"
                                    class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">
                                    {{ __('goods.learn') }}
                                    <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                                <span
                                    class="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1">
                                    @include('price')
                                </span>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
        @if (method_exists($goods, 'links'))
            {{ $goods->links() }}
        @endif

    </div>
</div>
