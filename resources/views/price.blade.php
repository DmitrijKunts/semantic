@switch($good->currency)
    @case('USD')
        ${!! $val !!}
    @break

    @case('UAH')
        {!! $val !!} ₴
    @break

    @default
        {!! $val . ' ' . $good->currency !!}
@endswitch
