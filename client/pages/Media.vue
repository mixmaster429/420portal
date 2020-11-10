<template>
    
</template>

<script>
    import ShowMedia from "~/components/media/ShowMedia";
    import PageFooter from "~/components/PageFooter"
    import { mapGetters } from "vuex";
    export default {
        components: {
            ShowMedia,
            PageFooter,
        },        
        head () {
            return {
                title: 'Marijuana News - Marijuana Blog',
                meta: [
                    { hid: 'title', name: 'title', content: 'Marijuana News - Marijuana Blog' },
                    { hid: 'keywords', name: 'keywords', content: 'marijuana news, marijuana blog, marijuana articles' },
                    { hid: 'description', name: 'description', content: "Keep up with Marijuana News from all over the World. We write Marijuana articles that's happening today." }
                ],
            }
        },
        serverPrefetch() {
            return this.fetchMedia();
        },
        computed: mapGetters({
            media: 'prefetch/media',
        }),
        mounted() {
            if(!this.media) {
                this.fetchMedia();
            }
        },
        methods: {
            fetchMedia() {
                return this.$store.dispatch('prefetch/fetchMedia', this.$router.params.id);
            }
        }
    }
</script>