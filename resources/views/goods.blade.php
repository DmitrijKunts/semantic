<div class="text-gray-600 body-font">
    <div class="container px-5 pb-4 mx-auto">
        <div class="flex flex-wrap">

            @foreach ($goods as $good)
                <div class="p-4 md:w-1/3">
                    <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                        <a rel="nofollow" href="{{ route('good', $good) }}">
                            <img class="lg:h-48 md:h-36 w-full object-cover object-center" src="{!! $good->thumbnail() !!}"
                                alt="{{ $good->name }}">
                        </a>
                        <div class="p-6">
                            <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                                {{ $good->vendor }}</h2>
                            <h1 class="title-font text-lg font-medium text-gray-900 mb-3"><a
                                    href="{{ route('good', $good) }}">{{ $good->nameKey($cat ?? null) }}</a>
                            </h1>
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
        {{ $goods->links() }}
    </div>
</div>
