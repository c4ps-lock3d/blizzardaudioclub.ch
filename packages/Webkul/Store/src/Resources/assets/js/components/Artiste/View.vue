

<template>
    <div id="backgroundCard" class="overflow-hidden rounded-lg border border-black">
        <a v-bind:href="'/' + product.url_key">
            <img v-bind:src="'/storage/' + image.path"/>
            <div class="p-4">
                <div class="mb-2">{{ product.name }}</div>
                <div v-if="bundlePriceDisplay" style="color: #dcdcdc" class="flex flex-col gap-0.5 text-base font-bold">
                    <div v-for="(price, format) in bundlePriceDisplay" :key="format">
                        {{ format }} : {{ price }}
                    </div>
                </div>
                <div class="font-bold" v-else>
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

    computed: {
        bundlePriceDisplay() {
            if (this.product.type === 'bundle' && this.product.bundle_format_prices) {
                return this.product.bundle_format_prices;
            }
            return null;
        }
    }
}
</script>
@endpushOnce