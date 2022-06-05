@extends('app')

@section('title', $good->name . ' - ' . config('app.name'))

@section('canonical')
    <link rel="canonical" href="{{ route('good', $good) }}" />
@endsection

@section('schemaorg')
    {{ Breadcrumbs::view('breadcrumbs::json-ld', 'good', $good) }}
    <script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "Product",
            "name": {{ Js::from($good->name) }},
            "image": [
                {!! $good->pictures()->map(fn($i, $k) => '"' . $good->picture($k) . '"')->join(',') !!}
            ],
            "description": {{ Js::from($good->desc) }},
            "sku": "{{ $good->sku }}",
            "mpn": "{{ $good->sku + 666 }}",
            "brand": {
                "@type": "Brand",
                "name": {{ Js::from($good->vendor) }}
            },
            {{-- "review": {
                "@type": "Review",
                "reviewRating": {
                    "@type": "Rating",
                    "ratingValue": "4",
                    "bestRating": "5"
                },
                "author": {
                    "@type": "Person",
                    "name": "Fred Benson"
                }
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.4",
                "reviewCount": "89"
            }, --}} "offers": {
                "@type": "Offer",
                "url": "{{ route('good', $good) }}",
                "priceCurrency": "{{ $good->currency }}",
                "price": "{{ $good->price }}",
                "priceValidUntil": "{{ $good->updated_at->addDays(15)->format('Y-m-d') }}",
                "itemCondition": "https://schema.org/NewCondition",
                "availability": "https://schema.org/InStock"
            }
        }
    </script>
@endsection

@if ($good->summary)
    @section('meta')
        <meta name="description" content="{{ $good->summary }}">
    @endsection
@endif


@section('content')
    <article class="text-gray-600 body-font overflow-hidden">
        <div class="container px-5 py-4 mx-auto">

            <div class="lg:w-4/5 mx-auto flex flex-wrap">
                {{ Breadcrumbs::render('good', $good) }}
                <div class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                    <h1 class="text-gray-900 text-3xl title-font font-medium mb-4">{!! $good->name !!}</h1>
                    <h2 class="text-sm title-font text-gray-500 tracking-widest">{{ $good->vendor }}</h2>
                    <h2 class="text-sm title-font text-gray-500 tracking-widest">{{ $good->model }}</h2>

                    <div class="lg:hidden flex">
                        <span class="title-font font-medium text-2xl text-gray-900">
                            @include('price', ['val' => $good->price])
                            @if ($good->oldprice > 0)
                                <span class="ml-1 line-through text-lg text-gray-400">
                                    @include('price', ['val' => $good->oldprice])
                                </span>
                            @endif
                        </span>
                        @include('buy')
                    </div>

                    <div class="flex mb-4">
                        <div class="flex-grow text-indigo-500 border-b-2 border-indigo-500 py-2 text-lg px-1">
                            {{ __('good.description') }}
                        </div>
                    </div>
                    <div class="leading-relaxed mb-4">
                        {!! Illuminate\Support\Str::of($good->summary)->explode("\n")->map(fn($i) => "<p>$i</p>")->join("\n") !!}
                    </div>

                    @if ($good->tech != '')
                        <div class="flex mb-4">
                            <div class="flex-grow text-indigo-500 border-b-2 border-indigo-500 py-2 text-lg px-1">
                                Характеристики
                            </div>
                        </div>
                        @foreach ($good->techs() as $tech)
                            <div class="flex border-t border-gray-200 py-2">
                                <span class="text-gray-500">{!! $good->techDiv($tech, 0) !!}</span>
                                <span class="ml-auto text-gray-900">{!! $good->techDiv($tech, 1) !!}</span>
                            </div>
                        @endforeach
                    @endif

                    @if ($good->tech != '')
                        <div class="flex mb-4">
                            <div class="flex-grow text-indigo-500 border-b-2 border-indigo-500 py-2 text-lg px-1">
                                Комплектация
                            </div>
                        </div>
                        @foreach ($good->equips() as $equip)
                            <div class="flex border-t border-gray-200 py-2">
                                <span class="text-gray-500">{!! $equip !!}</span>
                            </div>
                        @endforeach
                    @endif



                    <div class="flex">
                        <span class="title-font font-medium text-2xl text-gray-900">
                            @include('price', ['val' => $good->price])
                            @if ($good->oldprice > 0)
                                <span class="ml-2 line-through text-lg text-gray-400">
                                    @include('price', ['val' => $good->oldprice])
                                </span>
                            @endif
                        </span>



                        @include('buy')
                        <button
                            class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4">
                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                class="w-5 h-5" viewBox="0 0 24 24">
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                <img alt="{{ $good->alt() }}"
                    class="lozad lg:w-1/2 w-full lg:h-auto h-64 object-cover object-center rounded"
                    srcset="/css/loading.gif 320w" sizes="100vw" src="{{ $good->picture() }}"
                    data-srcset="{!! $good->picture() !!} 320w">

                @if (isBot() || request()->exists('full'))
                    <section class="text-gray-600 body-font">
                        <div class="container px-5 mx-auto flex flex-wrap">
                            <div class="flex flex-wrap md:-m-2 -m-1 ">
                                @foreach ($good->pictures() as $p)
                                    @if (!$loop->first)
                                        <div class="md:p-2 p-1 w-1/2 lg:w-1/5">
                                            <img alt="{{ $good->alt($loop->index) }} - thumbnail"
                                                srcset="/css/loading.gif 320w" sizes="100vw"
                                                class="lozad w-full object-cover h-full object-center block"
                                                data-srcset="{!! $good->thumbnail($loop->index) !!} 320w"
                                                src="{{ $good->thumbnail($loop->index) }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <article>
                        {!! Illuminate\Support\Str::of($good->desc)->explode("\n")->map(fn($i) => "<p>$i</p>")->join("\n") !!}
                    </article>

                    <section class="text-gray-600 body-font">
                        <div class="container px-5 mx-auto flex flex-wrap">
                            <div class="flex flex-wrap md:-m-2 -m-1">
                                @foreach ($good->pictures() as $p)
                                    @if (!$loop->first)
                                        <div class="md:p-2 p-1">
                                            <img alt="{{ $good->alt($loop->index) }}" srcset="/css/loading.gif 320w"
                                                sizes="100vw" class="lozad w-full object-cover h-full object-center block"
                                                data-srcset="{!! $good->picture($loop->index) !!} 320w"
                                                src="{{ $good->picture($loop->index) }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif


            </div>
        </div>
    </article>

    @include('goods', ['goods' => $good->brothers()])

    @include('cats', [
        'catChilds' => constSort(
            $good->cats()->first()->brothers()->get(),
            $good->id
        )->slice(0, 20),
        'catsAsKeys' => $good->name,
    ])


@endsection
