<template>
  <div class="forum">
    <forum-detail :forum="forum" />
    <page-footer />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import ForumDetail from '~/components/forum/ForumDetail'
import PageFooter from '~/components/PageFooter'
export default {
  components: {
    ForumDetail,
    PageFooter,
  },
  head() {
    return {
      title: this.forum.title,
      meta: [
        { hid: 'title', name: 'title', content: this.forum.title },
        { hid: 'keywords', name: 'keywords', content: 'forums' },
        {
          hid: 'description',
          name: 'description',
          content: `${this.forum.user ? this.forum.user.name + ' - ' : ''}${this.forum.description}`,
        },
      ],
    }
  },
  computed: mapGetters({
    forum: 'prefetch/forum',
  }),
  serverPrefetch() {
    return this.fetchForum()
  },
  mounted() {
    if (!this.forum) {
      this.fetchForum()
    }
  },
  methods: {
    fetchForum() {
      return this.$store.dispatch('prefetch/fetchForum', this.$route.params.id)
    },
  },
}
</script>
<style lang="css">
@import url('../../assets/css/forum.css');
</style>
