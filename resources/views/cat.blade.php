@extends('app')

@section('title', $cat->name . ' - ' . config('app.name'))

@section('canonical')
    <link rel="canonical" href="{{ url($cat->slug) }}" />
@endsection

@section('content')
    <section class="text-gray-600 body-font">
        <div class="container px-5 py-12 mx-auto">
            {{ Breadcrumbs::render('cat', $cat) }}

            <div class="text-center mb-10">
                <h1 class="sm:text-3xl text-2xl font-medium text-center title-font text-gray-900 mb-4">
                    {{ $cat->name }}
                </h1>
            </div>


            @isset($cat->childs)
                <div class="flex flex-wrap lg:w-4/5 sm:mx-auto sm:mb-2 -mx-2">
                    @foreach ($cat->childs as $child)
                        <div class="p-2 sm:w-1/2 w-full">
                            <div class="bg-gray-100 rounded flex p-4 h-full items-center">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="3" class="text-indigo-500 w-6 h-6 flex-shrink-0 mr-4" viewBox="0 0 24 24">
                                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
                                    <path d="M22 4L12 14.01l-3-3"></path>
                                </svg>
                                <a href="{{ url($child->slug) }}" class="title-font font-medium">{{ $child->name }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endisset

            @isset($cat->keys)
                <ul>
                    @foreach ($cat->keys as $key)
                        <li>{{ $key->name }}</li>
                    @endforeach
                </ul>

            @endisset

            <p class="text-base leading-relaxed xl:w-2/4 lg:w-3/4 mx-auto">
                {{ $cat->text }}
            </p>

        </div>
    </section>


@endsection
