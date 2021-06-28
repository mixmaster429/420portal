<template>
  <div>
    <div class="container privacy-content">
      <Terms />
    </div>

    <div class="row">
      <page-footer></page-footer>
    </div>
  </div>
</template>

<script>
import Terms from './auth/terms'
import PageFooter from '~/components/PageFooter'
export default {
  components: {
    Terms,
    PageFooter,
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
  padding: 50px 0 30px;

  @media (max-width: 678px) {
    padding: 30px 0 20px;
  }
}
</style>
