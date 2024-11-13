<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.home.contact.title')
    </x-slot>

    <div class="container mt-[34px] px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4 max-md:text-sm max-sm:text-xs">
        <div class="mt-[34px] mb-[34px] flex justify-start max-lg:hidden">
            <div id="colorTextCommand">
                <a class="text-xl !text-[#FADA00]" href="{{ route('shop.home.artisteslist') }}">ARTISTES</a>
                <span class="align-top icon-arrow-right text-2xl"></span>
                <span class="uppercase text-xl">{{ $artistes->name }}</span>
            </div>
        </div>
        <div class="grid grid-cols-8 gap-4">
            <div class="col-span-2"><img class="rounded-md" src="/storage/{{ $artistes->image }}"></div>
            <div class="col-span-6">
                <h1 class="text-3xl font-medium max-sm:text-xl mb-[18px]">{{ $artistes->name }}</h1>
                <div><p class="text-justify">{{ $artistes->content }}</p></div>
            </div>
            <div class="col-span-6">
                <h1 class ="text-xl mt-[34px] mb-[18px]">PRODUITS RELATIFS</h1>
            </div>
        </div>
        <div class="grid grid-cols-5 gap-6 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
            @foreach($artistes->products as $product)
                @foreach ($product->images as $image)
                    <artiste-view
                        :image='{{ json_encode($image) }}'
                        :product='{{ json_encode($product) }}'
                    ></artiste-view>
                @endforeach
            @endforeach
        </div>
    </div>

    @push('scripts')
        {!! Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>

<script>document.body.style.overflow ='scroll';</script>