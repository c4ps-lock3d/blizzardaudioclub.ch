

<template>
    <div id="backgroundCard" class="overflow-hidden rounded-lg border border-black">
        <a v-bind:href="'/' + product.url_key">
            <img v-bind:src="'/storage/' + image.path"/>
            <div class="p-4">
                <div class="mb-2">{{ product.name }}</div>
                <div class="font-bold">
                    {{ parseFloat(product.price).toFixed(2) }}CHF
                </div>
            </div>
        </a>
    </div>
</template>

@pushOnce('scripts')
<script>
export default {
    name: 'ArtisteView',
    props: ['image', 'product'],

    async mounted() {
        try {
            await this.$nextTick();
            const initSpotify = () => {
                const script = document.createElement('script');
                script.src = 'https://open.spotify.com/embed/iframe-api/v1';
                script.defer = true;
                
                window.onSpotifyIframeApiReady = (IFrameAPI) => {
                    const element = document.getElementById('embed-iframe');
                    if (!element) return;
                    
                    const spotifyToken = element.dataset.spotifyToken;
                    const options = {
                        width: '100%',
                        height: '160',
                        uri: `spotify:artist:${spotifyToken}`,
                    };
                    
                    IFrameAPI.createController(element, options, () => {
                        console.log('Spotify iframe loaded');
                    });
                };

                document.body.appendChild(script);
            };

            initSpotify();
        } catch (error) {
            console.error('Error loading Spotify iframe:', error);
        }
    }
}
</script>
@endpushOnce