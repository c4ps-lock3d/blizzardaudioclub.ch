<template>
    <div v-for="artiste in artistes">
        {{ artiste.name }}
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