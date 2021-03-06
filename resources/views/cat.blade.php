@extends('app')

@php
$page = request()->input('page');
if (!$page) {
    $page = request()->input('cat_page');
}
$page = $page ? " #$page" : '';
@endphp
@section('title', $cat->name . "$page - " . config('app.name'))

@section('canonical')
    <link rel="canonical" href="{{ $cat->canonical() }}" />
@endsection

@section('schemaorg')
    {{ Breadcrumbs::view('breadcrumbs::json-ld', 'cat', $cat) }}
@endsection

@section('content')
    <div class="text-gray-600 body-font">
        <div class="container px-5 py-12 mx-auto">
            {{ Breadcrumbs::render('cat', $cat) }}

            <div class="text-center mb-10">
                <h1 class="sm:text-3xl text-2xl font-medium text-center title-font text-gray-900 mb-4">
                    {{ $cat->name }}
                    @if (config('app.debug'))
                        [keys: {{ $cat->keys->count() }}]
                    @endif
                </h1>
            </div>

            @if (!isBot())
                {!! getBanner($cat->name) !!}
            @endif

            @isset($catChilds)
                @include('cats', ['catChilds' => $catChilds])
            @endisset

            @include('goods', [
                'goods' => $cat->goods()->paginate(),
                'loadKeys' => $cat->calcKeysNotUsedWords(),
            ])

            <article class="text-base leading-relaxed xl:w-2/4 lg:w-3/4 mx-auto">
                {!! Illuminate\Support\Str::of($cat->text)->explode("\n")->map(fn($i) => "<p>$i</p>")->join("\n") !!}
                {!! $cat->snippet2Text()->map(fn($i) => "<p>$i</p>")->join("\n") !!}
            </article>

        </div>
    </div>


@endsection
