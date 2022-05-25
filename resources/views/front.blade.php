@extends('app')

@section('canonical')
    <link rel="canonical" href="{{ route('home') }}" />
@endsection

@section('title', config('app.name'))

@section('content')
    @foreach ($menu as $cat)
        @break($loop->index == 5)
        <div class="text-gray-600 body-font">
            <div class="container px-5 py-4 mx-auto">

                <div class="text-center mb-10">
                    <h2 class="sm:text-3xl text-2xl font-medium text-center title-font text-gray-900 mb-4">
                        {{ $cat->name }}
                    </h2>
                </div>


                @isset($cat->childs)
                    @include('cats', [
                        'catChilds' => $cat->childs()->limit(6)->get(),
                    ])
                @endisset


            </div>
        </div>
    @endforeach
@endsection
