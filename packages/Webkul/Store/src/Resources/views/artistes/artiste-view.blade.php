<!-- Page Layout -->
<x-shop::layouts>
    @include('store::components.artiste-product-card-script')
    
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
                @foreach($products as $product)
                    <v-product-card
                        :product='{{ json_encode($product) }}'
                        :bundle-downloadable-image='{{ json_encode($product["bundle_downloadable_image"] ?? null) }}'
                    ></v-product-card>
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
            <h1 class ="border-b text-2xl mt-[34px] mb-[18px]">ÉCOUTER</h1>
            <div data-spotify-token="{{ $artistes->spotifyToken }}" id="embed-iframe"></div>
                @if($count_products <= 2)
                    <div class="mt-4 grid grid-cols-2 gap-6 max-1060:grid-cols-1 max-md:gap-x-4" id="youtube-grid">
                @else
                    <div class="mt-4 grid grid-cols-1 gap-6 max-1060:grid-cols-1 max-md:gap-x-4" id="youtube-grid">
                @endif
                    @php
                        $videoclips = [];
                        foreach($artistes->products as $product) {
                            foreach ($product->videoclips as $videoclip) {
                                $videoclips[$videoclip->youtubetoken] = $videoclip->youtubetoken;
                            }
                        }
                    @endphp
                    @foreach($videoclips as $token)
                        <div class='youtube-container' data-youtube-token='{{ $token }}'></div>
                    @endforeach        
            </div>
        </div>
    </div>

    @push('scripts')
        {!! Captcha::renderJS() !!}
        <script>document.body.style.overflow ='scroll';</script>
        <script>
            function loadSpotifyPlayer() {
                const container = document.getElementById('embed-iframe');
                if (!container) return;
                
                const spotifyToken = container.getAttribute('data-spotify-token');
                if (!spotifyToken) return;
                
                const iframe = document.createElement('iframe');
                iframe.style.borderRadius = '12px';
                iframe.src = `https://open.spotify.com/embed/artist/${spotifyToken}?utm_source=generator`;
                iframe.width = '100%';
                iframe.height = '352';
                iframe.frameBorder = '0';
                iframe.allow = 'autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture';
                iframe.loading = 'lazy';
                
                container.appendChild(iframe);
            }

            function loadYoutubeVideos() {
                const containers = document.querySelectorAll('[data-youtube-token]');
                containers.forEach(container => {
                    const token = container.getAttribute('data-youtube-token');
                    const iframe = document.createElement('iframe');
                    iframe.src = `https://www.youtube-nocookie.com/embed/${token}?si=DCmthf-j1ajhYQmw`;
                    iframe.title = 'YouTube video player';
                    iframe.frameborder = '0';
                    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                    iframe.referrerPolicy = 'strict-origin-when-cross-origin';
                    iframe.allowFullscreen = true;
                    iframe.className = 'rounded-lg border border-black w-full aspect-video';
                    iframe.loading = 'lazy';
                    container.appendChild(iframe);
                });
            }

            // Attendre que la page soit COMPLÈTEMENT chargée, puis ajouter un délai pour laisser Vue finir
            window.addEventListener('load', function() {
                // Ajouter 500ms de délai pour s'assurer que Vue a fini
                setTimeout(() => {
                    loadSpotifyPlayer();
                    loadYoutubeVideos();
                }, 500);
            });
        </script>
    @endpush
</x-shop::layouts>

