<template>
    <div class="forum">
        <forum-detail :forum="forum"></forum-detail>
        <page-footer></page-footer>
    </div>
</template>

<script>
    import ForumDetail from "~/components/forum/ForumDetail";
    import PageFooter from "~/components/PageFooter";
    import { mapGetters } from "vuex";
    export default {        
        components: {
            ForumDetail,
            PageFooter
        },
        head () {
            return { 
                title: 'Marijuana Forums',
                meta: [
                    { hid: 'title', name: 'title', content: this.forum.title },
                    { hid: 'keywords', name: 'keywords', content: 'forums' },
                    { hid: 'description', name: 'description', content: `${this.forum.user ? this.forum.user.name + ' - ' : ''}${this.forum.description}` }
                ],
            }
        },
        computed: mapGetters({
            forum: 'prefetch/forum',
        }),
        serverPrefetch(){
            return this.fetchForum();
        },
        mounted() {
            if(!this.forum) {
                this.fetchForum();
            }
        },
        methods: {
            fetchForum() {
                return this.$store.dispatch('prefetch/fetchForum', this.$route.params.id);
            }
        }
    }
</script>
<style lang="css">
    @import url('../../assets/css/forum.css');
</style>