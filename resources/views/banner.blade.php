{{-- <a href="{{ route('banner') }}"><img class="mx-auto" src="{{ $src }}"></a> --}}
<form class="flex ml-auto" action="{{ route('banner', [$href]) }}" method="POST" target="_blank">
    @method('PUT')
    @csrf
    <input type="hidden" name="query" value="{{ $query }}">
    <button type="submit" class="mx-auto">
        <img class="mx-auto" src="{{ $src }}">
    </button>
</form>
