<div class="flex flex-col max-md:hidden">
    <p class="font-semibold text-sm text-gray-800">
        {{ $address->company_name ?? '' }}
    </p>

    <p class="font-semibold text-sm leading-6 text-gray-800">
        {{ $address->name }}
    </p>
    
    <p class="!leading-6 text-sm text-gray-600">
        {{ $address->address }}<br>

        {{ $address->city }}<br>

        {{ core()->country_name($address->country) }} @if ($address->postcode) ({{ $address->postcode }}) @endif<br>

        {{ $address->phone }}
    </p>
</div>

<!-- For Mobile View -->
<div class="text-gray-800 md:hidden">
    <p class="font-semibold">
        {{ $address->company_name ?? '' }}
    </p>

    <p class="text-xs">
        {{ $address->name }}

        {{ $address->address }}

        {{ $address->city }}

        {{ core()->country_name($address->country) }} @if ($address->postcode) ({{ $address->postcode }}) @endif <br>

        <span class="no-underline">
            {{ $address->phone }}
        </span>
    </p>
</div>