<template>
    <div class="row justify-content-center mb-5 mb-md-0">
        <div class="col-12">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <show-media :selected="media"></show-media>
                    </div>
                </div>
            </div>            
        </div>
        <page-footer v-if="$device.isDesktop"></page-footer>
    </div>
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
                title: this.media.meta.title,
                meta: [
                    { hid: 'title', name: 'title', content: this.media.meta.title },
                    { hid: 'keywords', name: 'keywords', content: this.media.meta.keywords },
                    { hid: 'description', name: 'description', content: this.media.meta.description }
                ],
            }
        },
        serverPrefetch() {
            return this.fetchMedia();
        },
        data() {
            return {
                selected: null,
            };
        },
        watch: {
            media: 'setLocalState'
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
                return this.$store.dispatch('prefetch/fetchMedia', this.$route.params.id);
            },
            setLocalState(value) {
                this.selected = Object.assign({}, value);
            }
        }
    }
</script>