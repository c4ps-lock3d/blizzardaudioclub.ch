<v-product-gallery ref="gallery">
    <x-shop::shimmer.products.gallery />
</v-product-gallery>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-gallery-template"
    >
        <div>
            <!-- For large screens greater than 1180px. -->
            <div class="top-20 flex h-max gap-8 max-1180:hidden">
                <!-- Product Image and Videos Slider -->
                <div class="flex-24 h-509 flex min-w-[100px] max-w-[100px] flex-wrap place-content-start justify-center gap-2.5 overflow-y-auto overflow-x-hidden">
                    <!-- Arrow Up -->
                    <span
                        class="icon-arrow-up cursor-pointer text-2xl"
                        role="button"
                        aria-label="@lang('shop::app.components.products.carousel.previous')"
                        tabindex="0"
                        @click="swipeDown"
                        v-if="lengthOfMedia"
                    >
                    </span>

                    <!-- Swiper Container -->
                    <div
                        ref="swiperContainer"
                        class="flex flex-col max-h-[540px] gap-2.5 [&>*]:flex-[0] overflow-auto scroll-smooth scrollbar-hide"
                    >
                        <template v-for="(media, index) in [...media.images, ...media.videos]">
                            <video
                                v-if="media.type == 'videos'"
                                :class="`transparent max-h-[100px] min-w-[100px] cursor-pointer rounded-xl border ${isActiveMedia(index) ? 'pointer-events-none border border-navyBlue' : 'border-white'}`"
                                @click="change(media, index)"
                                alt="{{ $product->name }}"
                            >
                                <source
                                    :src="media.video_url"
                                    type="video/mp4"
                                />
                            </video>

                            <img
                                v-else
                                :class="`transparent max-h-[100px] min-w-[100px] cursor-pointer rounded-xl border ${isActiveMedia(index) ? 'pointer-events-none border border-navyBlue' : 'border-white'}`"
                                :src="media.small_image_url"
                                alt="{{ $product->name }}"
                                width="100"
                                height="100"
                                @click="change(media, index)"
                            />
                        </template>
                    </div>

                    <!-- Arrow Down -->
                    <span
                        class="icon-arrow-down cursor-pointer text-2xl"
                        v-if= "lengthOfMedia"
                        role="button"
                        aria-label="@lang('shop::app.components.products.carousel.previous')"
                        tabindex="0"
                        @click="swipeTop"
                    >
                    </span>
                </div>

                <!-- Product Base Image and Video with Shimmer-->
                <div
                    class="max-h-[560px] max-w-[560px]"
                    v-show="isMediaLoading"
                >
                    <div class="shimmer min-h-[560px] min-w-[560px] rounded-xl bg-zinc-200"></div>
                </div>

                <div
                    class="max-h-[560px] max-w-[560px]"
                    v-show="! isMediaLoading"
                >
                    <img
                        class="min-w-[450px] cursor-pointer rounded-xl"
                        :src="baseFile.path"
                        v-if="baseFile.type == 'image'"
                        alt="{{ $product->name }}"
                        width="560"
                        height="560"
                        @click="isImageZooming = !isImageZooming"
                        @load="onMediaLoad()"
                    />

                    <div
                        class="min-w-[450px] cursor-pointer rounded-xl"
                        v-if="baseFile.type == 'video'"
                    >
                        <video
                            controls
                            width="475"
                            alt="{{ $product->name }}"
                            @click="isImageZooming = !isImageZooming"
                            @loadeddata="onMediaLoad()"
                            :key="baseFile.path"
                        >
                            <source
                                :src="baseFile.path"
                                type="video/mp4"
                            />
                        </video>
                    </div>

                    <!-- Display bundle product info if this is a bundle child image 
                    <div
                        v-if="baseFile.productFormat || baseFile.productName"
                        class="mt-2 text-sm text-zinc-600"
                    >
                        <span v-if="baseFile.productFormat" class="font-medium">
                            @{{ baseFile.productFormat }}
                        </span>
                        <span v-else-if="baseFile.productName" class="font-medium">
                            @{{ baseFile.productName }}
                        </span>
                    </div> -->
                </div>
            </div>

            <!-- Product Images and Videos for Medium & Small Screen -->
            <div
                class="scrollbar-hide flex w-screen gap-8 overflow-auto max-sm:gap-5 1180:hidden"
                v-show="! isMediaLoading"
            >
                <x-shop::media.images.lazy
                    ::src="image.large_image_url"
                    class="w-[490px]"
                    ::class="(media.images.length + media.videos.length) > 1 ? 'max-sm:hidden' : ''"
                    v-for="(image, index) in media.images"
                    alt="{{ $product->name }}"
                    @click="isImageZooming = !isImageZooming"
                />

                <!-- For mobile view, use the carousel when the image count is greater than 2" -->
                <x-shop::products.mobile.carousel
                    class="sm:hidden"
                    ::class="(media.images.length + media.videos.length) > 1 ? '' : 'hidden'"
                    ::options="media"
                    @click="isImageZooming = !isImageZooming"
                />
            </div>
            
            <!-- Gallery Images Zoomer -->
            <x-shop::image-zoomer 
                ::attachments="attachments" 
                ::is-image-zooming="isImageZooming" 
                ::initial-index="`media_${activeIndex}`"
            />
        </div>
    </script>

    <script type="module">
        app.component('v-product-gallery', {
            template: '#v-product-gallery-template',

            data() {
                return {
                    isImageZooming: false,

                    isMediaLoading: true,

                    media: {
                        images: @json($product->type === 'bundle' ? $bundleChildImages : product_image()->getGalleryImages($product)),

                        videos: @json(product_video()->getVideos($product)),
                    },

                    // Keep original images for bundle thumbnails
                    bundleImages: @json($product->type === 'bundle' ? $bundleChildImages : []),

                    baseFile: {
                        type: '',

                        path: '',

                        productFormat: null,

                        productName: null
                    },

                    activeIndex: 0,

                    containerOffset: 110,
                };
            },

            watch: {
                'media.images': {
                    deep: true,

                    handler(newImages, oldImages) {
                        let selectedImage = newImages?.[this.activeIndex];

                        if (JSON.stringify(newImages) !== JSON.stringify(oldImages) && selectedImage?.large_image_url) {
                            this.baseFile.path = selectedImage.large_image_url;
                        }
                    },
                },
            },
        
            mounted() {
                if (this.media.images.length) {

                    this.baseFile.type = 'image';

                    this.baseFile.path = this.media.images[0].original_image_url || this.media.images[0].large_image_url;
                    this.baseFile.productFormat = this.media.images[0].product_format || null;
                    this.baseFile.productName = this.media.images[0].product_name || null;
                } else if (this.media.videos.length) {

                    this.baseFile.type = 'video';

                    this.baseFile.path = this.media.videos[0].video_url;
                }

                // Listen for bundle product selection
                this.$emitter.on('bundle-product-selected', (event) => {
                    if (event.images && event.images.length > 0) {
                        // Only update the main image, keep thumbnails unchanged
                        this.activeIndex = 0;
                        this.baseFile.type = 'image';
                        this.baseFile.path = event.images[0].original_image_url || event.images[0].large_image_url;
                        this.baseFile.productFormat = event.images[0].product_format || null;
                        this.baseFile.productName = event.images[0].product_name || null;
                    }
                });
            },

            computed: {
                lengthOfMedia() {
                    if (this.media.images.length) {
                        return [...this.media.images, ...this.media.videos].length > 5;
                    }
                },

                attachments() {
                    return [...this.media.images, ...this.media.videos].map(media => ({
                        url: media.type === 'videos' ? media.video_url : media.original_image_url,
                        
                        type: media.type === 'videos' ? 'video' : 'image',
                    }));
                },
            },

            methods: {
                isActiveMedia(index) {
                    return index === this.activeIndex;
                },
                
                onMediaLoad() {
                    this.isMediaLoading = false;
                },

                change(media, index) {
                    this.isMediaLoading = true;

                    if (media.type == 'videos') {
                        this.baseFile.type = 'video';

                        this.baseFile.path = media.video_url;

                        this.baseFile.productFormat = null;

                        this.baseFile.productName = null;

                        this.onMediaLoad();
                    } else {
                        this.baseFile.type = 'image';

                        // Use original image for best quality, fallback to large if not available
                        this.baseFile.path = media.original_image_url || media.large_image_url;

                        // Set bundle product metadata if available
                        this.baseFile.productFormat = media.product_format || null;

                        this.baseFile.productName = media.product_name || null;
                    }

                    if (index > this.activeIndex) {
                        this.swipeDown();
                    } else if (index < this.activeIndex) {
                        this.swipeTop();
                    }

                    this.activeIndex = index;
                },

                swipeTop() {
                    const container = this.$refs.swiperContainer;

                    container.scrollTop -= this.containerOffset;
                },

                swipeDown() {
                    const container = this.$refs.swiperContainer;

                    container.scrollTop += this.containerOffset;
                },
            },
        });
    </script>
@endpushOnce