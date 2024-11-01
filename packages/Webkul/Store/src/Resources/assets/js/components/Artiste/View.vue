@pushOnce('scripts')
<script>
export default {
    props: ["product", "image"],
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
        </div>
</template>
