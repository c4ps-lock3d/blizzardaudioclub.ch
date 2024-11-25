<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.home.contact.title')
    </x-slot>

    <div class="container mt-[34px] px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4 max-md:text-sm max-sm:text-xs">
        <div class="mt-[34px] mb-[18px] flex justify-start max-lg:hidden">
            <div id="colorTextCommand">
                <a class="text-2xl !text-[#FADA00]" href="{{ route('shop.home.artisteslist') }}">ARTISTES</a>
                <span class="align-top icon-arrow-right text-3xl"></span>
                <span class="uppercase text-2xl">{{ $artistes->name }}</span>
            </div>
        </div>
        <div class="grid grid-cols-10 gap-6">
            <div class="col-span-3"><img class="rounded-lg border border-black" src="/storage/{{ $artistes->image }}"></div>
            <div class="col-span-7">
                <h1 class="text-3xl font-medium max-sm:text-xl mb-[18px] flex justify-between">
                    <div>
                        {{ $artistes->name }}
                    </div>
                    <div>
                        @if($artistes->facebook)
                            <a href="{{ $artistes->facebook }}" target="_blank" class="fab fa-facebook fa-1x fa-inverse mr-4"></a>
                        @endif
                        @if($artistes->instagram)
                            <a href="{{ $artistes->instagram }}" target="_blank" class="fab fa-instagram fa-1x fa-inverse mr-4"></a>
                        @endif
                        @if($artistes->tiktok)
                            <a href="{{ $artistes->tiktok }}" target="_blank" class="fab fa-tiktok fa-1x fa-inverse mr-4"></a>
                        @endif
                        @if($artistes->soundcloud)
                            <a href="{{ $artistes->soundcloud }}" target="_blank" class="fab fa-soundcloud fa-1x fa-inverse mr-4"></a>
                        @endif
                        @if($artistes->youtube)
                            <a href="{{ $artistes->youtube }}" target="_blank" class="fab fa-youtube fa-1x fa-inverse"></a>
                        @endif
                    </div>
                </h1>
                <div>
                    <p class="text-justify">{{ $artistes->content }}</p>
                </div>
            </div>
        </div>

        <h1 class ="text-2xl mt-[34px] mb-[18px]">PRODUITS RELATIFS</h1>

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
        @php
                $arr = array();
                foreach($artistes->products as $product){
                    foreach ($product->videoclips as $videoclip) {
                        $arr[] = $videoclip->youtubetoken;
                    }
                }
                $unique_data = array_unique($arr);
                foreach($unique_data as $key => $val) {
                    if ($key === array_key_first($unique_data)) {
                            echo "<h1 class ='text-2xl mt-[34px] mb-[18px]'>VIDÉOCLIPS</h1>";

                    }
                }
            @endphp   

        <div class="grid grid-cols-3 gap-6 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
            @php
                $arr = array();
                foreach($artistes->products as $product){
                    foreach ($product->videoclips as $videoclip) {
                        $arr[] = $videoclip->youtubetoken;
                    }
                }
                $unique_data = array_unique($arr);
                foreach($unique_data as $key => $val) {
                    echo "<div><lite-youtube class='rounded-lg border border-black' autoload videoid='".$val."'></lite-youtube></div>";
                }
            @endphp        
        </div>
    </div>

    @push('scripts')
        {!! Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>

<script>document.body.style.overflow ='scroll';</script>
<script type="module" src="https://cdn.jsdelivr.net/npm/@justinribeiro/lite-youtube@1/lite-youtube.min.js"></script>