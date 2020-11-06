<template>
  <desktop-home v-if="!$device.isMobile"></desktop-home>
  <mobile-home v-else ></mobile-home>
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

  computed: mapGetters({
    user: 'auth/user',
  }),
}
</script>
