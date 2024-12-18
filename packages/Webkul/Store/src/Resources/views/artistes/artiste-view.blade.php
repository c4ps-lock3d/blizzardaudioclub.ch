<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ $artistes->name }}
    </x-slot>

    <div class="container mt-[34px] px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4 max-md:text-sm max-sm:text-xs">
        <div class="mt-[34px] mb-[18px] flex justify-start max-lg:hidden">
            <div id="colorTextCommand">
                <a class="text-xl !text-[#FADA00]" href="{{ route('shop.home.artisteslist') }}">ARTISTES</a>
                <span class="align-top icon-arrow-right text-2xl"></span>
                <span class="uppercase text-xl">{{ $artistes->name }}</span>
            </div>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-3 max-1060:col-span-12 max-md:gap-x-4">
                <img class="rounded-lg border border-black" src="/storage/{{ $artistes->image }}">
            </div>
            <div class="col-span-9 max-1060:col-span-12 max-md:gap-x-4">
                <div class="border-b text-3xl font-medium max-sm:text-xl mb-[18px] flex justify-between">
                    <h1>
                        {{ $artistes->name }} @if($artistes->country) - {{ $artistes->country }}@endif
                    </h1>
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
                            <a href="{{ $artistes->youtube }}" target="_blank" class="fab fa-youtube fa-1x fa-inverse mr-4"></a>
                        @endif
                        @if($artistes->bandcamp)
                            <a href="{{ $artistes->bandcamp }}" target="_blank" class="fab fa-bandcamp fa-1x fa-inverse mr-4"></a>
                        @endif
                        @if($artistes->spotifyToken)
                            <a href="https://open.spotify.com/intl-fr/artist/{{ $artistes->spotifyToken }}" target="_blank" class="fab fa-spotify fa-1x fa-inverse"></a>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-justify">{{ $artistes->content }}
                        @if($artistes->website)
                            <a href="{{ $artistes->website }}" target="_blank" class="!text-[#FADA00] whitespace-nowrap">site officiel de l'artiste <i class="fas fa-external-link-alt"></i></a>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-5 gap-12 max-1060:grid-cols-1 max-md:gap-x-4">
            @if($count_products < 2)
                <div class="col-span-1">
            @elseif($count_products === 2)
                <div class="col-span-2">
            @elseif($count_products >= 3)
                <div class="col-span-3">
            @endif
                <h1 class ="border-b text-2xl mt-[34px] mb-[18px]">SORTIES LIÉES</h1>
                @if($count_products < 2)
                    <div class="grid grid-cols-1 gap-6 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
                @elseif($count_products === 2)
                    <div class="grid grid-cols-2 gap-6 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
                @elseif($count_products >= 3)
                    <div class="grid grid-cols-3 gap-6 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
                @endif
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

            @if($count_products < 2)
                <div class="col-span-4">
            @elseif($count_products === 2)
                <div class="col-span-3">
            @elseif($count_products >= 3)
                <div class="col-span-2">
            @endif
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
                            echo "<h1 class ='border-b text-2xl mt-[34px] mb-[18px]'>VIDÉOCLIPS</h1>";
                        }
                    }
                @endphp
                @if($count_products <= 2)
                    <div id="test1" class="grid grid-cols-2 gap-6 max-1060:grid-cols-1 max-md:gap-x-4">
                @elseif($count_products >= 3)
                    <div class="grid grid-cols-1 gap-6 max-1060:grid-cols-1 max-md:gap-x-4">
                @endif
                    @php
                        $arr = array();
                        foreach($artistes->products as $product){
                            foreach ($product->videoclips as $videoclip) {
                                $arr[] = $videoclip->youtubetoken;
                            }
                        }
                        $unique_data = array_unique($arr);
                        foreach($unique_data as $key => $val) {
                            echo "<lite-youtube id='test2' class='rounded-lg border border-black' autoload videoid='".$val."'></lite-youtube>";
                        }
                    @endphp        
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1">
            <h1 class ="border-b text-2xl mt-[34px] mb-[18px]">ÉCOUTER</h1>

        </div>
    </div>

    @push('scripts')
        {!! Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>

<script>document.body.style.overflow ='scroll';</script>
<script type="module" src="https://cdn.jsdelivr.net/npm/@justinribeiro/lite-youtube@1/lite-youtube.min.js"></script>