<script>
export default {
    data() {
        return {
            artistes: [],
        };
    },
    mounted() {
        this.fetchPosts();
    },
    methods: {
        fetchPosts() {
            axios
                .get("/api/artisteslist")
                .then((response) => (this.artistes = response.data.data))
                .catch((error) => console.log(error));
        },
    },
};
</script>

<template>
    <div v-for="artiste in artistes">
        <a v-bind:href="'/artistes/' + artiste.slug + '-' + artiste.id">
            <div
                class="1180:transtion-all group w-full rounded-lg border border-black 1180:relative 1180:grid 1180:content-start 1180:overflow-hidden 1180:duration-300 1180:hover:shadow-[0_5px_10px_rgba(0,0,0,0.1)]"
            >
                <div
                    class="relative max-h-[300px] max-w-[291px] overflow-hidden max-md:max-h-60 max-md:max-w-full max-md:rounded-lg max-sm:rounded-b-none"
                >
                    <img v-bind:src="'storage/' + artiste.image" />
                </div>
                <div
                    id="backgroundCard"
                    class="-mt-9 text-center font-bold justify-center grid max-w-[291px] translate-y-9 content-start gap-2.5 p-4 transition-transform duration-300 ease-out group-hover:-translate-y-0 max-md:relative max-md:mt-0 max-md:translate-y-0 max-md:gap-0 max-md:px-0 max-md:py-1.5 rounded-b-lg"
                >
                    <p>{{ artiste.name }} - {{ artiste.name }}</p>
                    <p>{{ artiste.country }}</p>
                </div>
            </div>
        </a>
    </div>
</template>
