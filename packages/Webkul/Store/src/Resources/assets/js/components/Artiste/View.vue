@pushOnce('scripts')
<script>
export default {
    props: ["product", "image"],
    data() {
        return {
            isAddingToCart: false,
        }
    },
    methods: {
        addToCart() {
            this.isAddingToCart = true;

            this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                    'quantity': 1,
                    'product_id': this.product.id,
                })
                .then(response => {
                    if (response.data.message) {
                        this.$emitter.emit('update-mini-cart', response.data.data );

                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                    } else {
                        this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                    }

                    this.isAddingToCart = false;
                })
                .catch(error => {
                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                    if (error.response.data.redirect_uri) {
                        window.location.href = error.response.data.redirect_uri;
                    }
                    
                    this.isAddingToCart = false;
                });
        },
    },
};
</script>
@endpushOnce

<template>
    
        <div
            class="1180:transtion-all group w-full rounded-md 1180:relative 1180:grid 1180:content-start 1180:overflow-hidden 1180:duration-300 1180:hover:shadow-[0_5px_10px_rgba(0,0,0,0.1)]"
        >
            <div
                class="relative max-h-[300px] max-w-[291px] overflow-hidden max-md:max-h-60 max-md:max-w-full max-md:rounded-lg max-sm:max-h-[200px] max-sm:max-w-full"
            >
                <a v-bind:href="'/' + product.url_key"><img v-bind:src="'/storage/' + image.path" /></a>
            </div>
            <div
                id="backgroundCard"
                class="-mt-9 justify-center grid max-w-[291px] translate-y-9 content-start gap-2.5 p-4 transition-transform duration-300 ease-out group-hover:-translate-y-0 group-hover:rounded-t-lg max-md:relative max-md:mt-0 max-md:translate-y-0 max-md:gap-0 max-md:px-0 max-md:py-1.5 max-sm:min-w-[170px] max-sm:max-w-[192px]"
            >
                <p class="">{{ product.name }}</p>
                <p class="font-bold">
                    {{ parseFloat(product.price).toFixed(2) }} CHF
                </p>
            </div>
            <!-- Product Actions Section -->
            <div class="action-items flex items-center justify-between opacity-0 transition-all duration-300 ease-in-out group-hover:opacity-100 max-md:hidden">


                        <button
                            class="secondary-button w-full max-w-full p-2.5 text-sm font-medium max-sm:rounded-xl max-sm:p-2"

                            @click="addToCart()"
                        >
                            Ajouter au panier
                        </button>


                </div>
        </div>
    
</template>
