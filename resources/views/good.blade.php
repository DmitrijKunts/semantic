@extends('app')

@section('title', $good->name . ' - ' . config('app.name'))

@section('canonical')
    <link rel="canonical" href="{{ route('good', $good->slug) }}" />
@endsection

@section('content')
    <section class="text-gray-600 body-font overflow-hidden">
        <div class="container px-5 py-4 mx-auto">

            <div class="lg:w-4/5 mx-auto flex flex-wrap">
                {{ Breadcrumbs::render('good', $good) }}
                <div class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                    <h2 class="text-sm title-font text-gray-500 tracking-widest">{{ $good->vendor }}</h2>
                    <h2 class="text-sm title-font text-gray-500 tracking-widest">{{ $good->model }}</h2>
                    <h1 class="text-gray-900 text-3xl title-font font-medium mb-4">{{ $good->name }}</h1>
                    <div class="flex mb-4">
                        <div class="flex-grow text-indigo-500 border-b-2 border-indigo-500 py-2 text-lg px-1">
                            Описание
                        </div>
                    </div>
                    <p class="leading-relaxed mb-4">
                        {{ $good->desc }}
                    </p>

                    @isset($good->tech)
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
                    @endisset

                    @isset($good->tech)
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
                    @endisset



                    <div class="flex">
                        <span class="title-font font-medium text-2xl text-gray-900">{!! $good->price . ' ' . $good->currency !!}</span>
                        <button
                            class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Купить</button>
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
                <img alt="ecommerce" class="lg:w-1/2 w-full lg:h-auto h-64 object-cover object-center rounded"
                    src="{{ $good->picture() }}">
            </div>
        </div>
    </section>

    @include('cats', ['catChilds' => $good->cats()->first()->brothers(), 'catsAsKeys' => $good->name])

    @include('goods', ['goods' => $good->brothers()])
@endsection
