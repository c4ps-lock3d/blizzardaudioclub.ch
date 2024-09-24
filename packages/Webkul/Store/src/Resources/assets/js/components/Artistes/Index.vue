<template>
    <div v-for="artiste in artistes">
        <a v-bind:href="'/artistes/'+artiste.slug+'-'+artiste.id">
            <div class="1180:transtion-all group w-full rounded-md 1180:relative 1180:grid 1180:content-start 1180:overflow-hidden 1180:duration-300 1180:hover:shadow-[0_5px_10px_rgba(0,0,0,0.1)]">
                <div class="relative max-h-[300px] max-w-[291px] overflow-hidden max-md:max-h-60 max-md:max-w-full max-md:rounded-lg max-sm:max-h-[200px] max-sm:max-w-full">
                    <img v-bind:src="'storage/'+artiste.image">
                </div>
                <div id="backgroundCard" class="-mt-9 justify-center grid max-w-[291px] translate-y-9 content-start gap-2.5 p-4 transition-transform duration-300 ease-out group-hover:-translate-y-0 group-hover:rounded-t-lg max-md:relative max-md:mt-0 max-md:translate-y-0 max-md:gap-0 max-md:px-0 max-md:py-1.5 max-sm:min-w-[170px] max-sm:max-w-[192px]">
                    <p class="mb-8">{{ artiste.name }}</p>
                </div>
            </div>
        </a>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                artistes: []
            }
        },
        mounted() { 
            this.fetchPosts()
        },
        methods: {
            fetchPosts() {
            document.body.style.overflow ='scroll';
            
                axios.get('/api/artisteslist')
                    .then(response => this.artistes = response.data)
                    .catch(error => console.log(error))
            }
        } 
    }
</script>