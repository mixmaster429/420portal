<template>
  <mobile-home v-if="$device.isMobile"></mobile-home>
  <desktop-home v-else></desktop-home>
</template>

<script>
import { mapGetters } from 'vuex'

import DesktopHome from '~/components/Home'
import MobileHome from '~/components/mobile/Home'
export default {
  components: {
    DesktopHome,
    MobileHome,
  },
  head () {
    return { title: '' }
  },

  mounted() {
    console.log(this.$device.isMobile);
  },

  computed: mapGetters({
    user: 'auth/user',
  }),
}
</script>
