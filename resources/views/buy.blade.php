<form class="flex ml-auto" action="{{ route('buy', [$good]) }}" method="POST" target="_blank">
    @method('PUT')
    @csrf
    <button type="submit"
        class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">
        {{ __('good.buy') }}
    </button>
</form>
