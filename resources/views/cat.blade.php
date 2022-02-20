@extends('app')

@section('title', $cat->name . ' - ' . config('app.name'))

@section('canonical')
    <link rel="canonical" href="{{ $cat->canonical() }}" />
@endsection

@section('content')
    <div class="text-gray-600 body-font">
        <div class="container px-5 py-12 mx-auto">
            {{ Breadcrumbs::render('cat', $cat) }}

            <div class="text-center mb-10">
                <h1 class="sm:text-3xl text-2xl font-medium text-center title-font text-gray-900 mb-4">
                    {{ $cat->name }}
                </h1>
            </div>

            {{-- {{ $cat->keysNotUsedWords }} --}}

            @isset($cat->childs)
                @include('cats', ['catChilds' => $cat->childs])
            @endisset

            @isset($cat->goods)
                @include('goods', ['goods' => $cat->goods, 'loadKeys' => $cat->calcKeysNotUsedWords()])
            @endisset

            {{-- {{ $cat->keysNotUsedWords }} --}}

            <p class="text-base leading-relaxed xl:w-2/4 lg:w-3/4 mx-auto">
                {{ $cat->text }}
            </p>

        </div>
    </div>


@endsection
