<template>
  <div class="container privacy-content">
    <Terms />
  </div>
</template>

<script>
import Terms from './auth/terms'
export default {
  components: {
    Terms,
  },
  head() {
    return {
      title: 'Terms of Use and Privacy Policy',
      meta: [
        {
          hid: 'title',
          name: 'title',
          content: 'Terms of Use and Privacy Policy',
        },
        {
          hid: 'description',
          name: 'description',
          content: "420Portal's Terms of Use and Privacy Policy",
        },
        {
          hid: 'keywords',
          name: 'keywords',
          content: 'Terms of Use and Privacy Policy',
        },
      ],
    }
  },
  data: () => ({}),
  methods: {},
}
</script>

<style lang="scss">
.privacy-content {
  color: whitesmoke;
  padding: 50px 0 100px;

  @media (max-width: 678px) {
    padding: 30px 0 50px;
  }
}
</style>
