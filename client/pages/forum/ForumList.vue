<template>
    <div class="forum">
        <forum-list></forum-list>
        <page-footer></page-footer>
    </div>
</template>

<script>
    import ForumList from "~/components/forum/ForumList";
    import PageFooter from "~/components/PageFooter";
    export default {        
        components: {
            ForumList,
            PageFooter
        },
        head () {
            return { 
                title: 'Marijuana Forums',
                meta: [
                    { hid: 'title', name: 'title', content: 'Marijuana Forums - Cannabis Forums' },
                    { hid: 'keywords', name: 'keywords', content: 'marijuana, forums, cannabis, medical, recreational, weed' },
                    { hid: 'description', name: 'description', content: 'Marijuana Forums - 420Portal Community Discussing Cannabis / Weed Forum Topics.' }
                ],
            }
        },
    }
</script>
<style lang="css">
    @import url('../../assets/css/forum.css');
</style>