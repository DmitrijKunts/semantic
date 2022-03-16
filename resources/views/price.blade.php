@switch($good->currency)
    @case('USD')
        ${!! $good->price !!}
    @break

    @case('UAH')
        {!! $good->price !!} ₴
    @break

    @default
        {!! $good->price . ' ' . $good->currency !!}
@endswitch
