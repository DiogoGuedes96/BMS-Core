<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.front_app_url') ?? config('app.url')">
@if (config('app.bms_client') === 'ATRAVEL')
<img src="{{ url('/images/atravel.png') }}" alt="Atravel Logo">
@elseif(config('app.bms_client') === 'ASM')
<img src="/images/asmLogo.png" alt="ASM Logo">
@else
{{ config('app.name') }}
@endif
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
