<x-mail::message>
    # Hello {{ $name }},

    You boosted your profile for {{ $days }}days at the price of ${{ $price }}.

    <br>
    Love,

    {{ config('app.name') }}
</x-mail::message>