<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.home.contact.title')
    </x-slot>

    <div class="container mt-[34px] px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4 max-md:text-sm max-sm:text-xs">
        <div class="grid grid-cols-6 gap-4">
            <div class="col-span-2"><img src="/storage/{{ $artistes->image }}"></div>
            <div class="col-span-4">
                <h1 class="text-xl mb-[18px]">{{ $artistes->name }}</h1>
                <div><p class="text-justify">{{ $artistes->content }}</p></div>
                <div>
                    <h1 class ="text-xl mt-[34px] mb-[18px]">Produits relatifs</h1>
                    @foreach($artistes->products as $product)
                        <h1><artiste-view :product='{{ json_encode($product) }}'></artiste-view></h1>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {!! Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>

<script>document.body.style.overflow ='scroll';</script>