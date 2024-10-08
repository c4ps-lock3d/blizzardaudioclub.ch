<script>
    export default {
        props:['product']
    }
</script>

<template>
    <a v-bind:href="'/'+product.url_key">
        <div>{{ product.name }}</div>
    </a>
</template>