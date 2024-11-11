@pushOnce('scripts')
<script>
export default {
    props: ["product", "image"],
};
</script>
@endpushOnce

<template>
    <div class="basis-1/3 overflow-hidden rounded-lg">
        <a v-bind:href="'/' + product.url_key">
            <img v-bind:src="'/storage/' + image.path"/>
            <div
                id="backgroundCard"
                class="p-4"
            >
                <p class="line-clamp-2">{{ product.name }}</p>
                <p class="font-bold mt-2">
                    {{ parseFloat(product.price).toFixed(2) }}CHF
                </p>
            </div>
        </a>
    </div>
</template>
