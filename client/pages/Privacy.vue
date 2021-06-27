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
    return { title: 'Privacy' }
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
