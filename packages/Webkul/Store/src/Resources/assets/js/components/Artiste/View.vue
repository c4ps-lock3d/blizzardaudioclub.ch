@pushOnce('scripts')
<script>
export default {
    props: ["product", "image"],
};
</script>
@endpushOnce

<template>
    <div id="backgroundCard" class="overflow-hidden rounded-lg border border-black">
        <a v-bind:href="'/' + product.url_key">
            <img v-bind:src="'/storage/' + image.path"/>
            <div class="p-4">
                <p class="mb-2">{{ product.name }}</p>
                <p class="font-bold">
                    {{ parseFloat(product.price).toFixed(2) }}CHF
                </p>
            </div>
        </a>
    </div>
</template>
