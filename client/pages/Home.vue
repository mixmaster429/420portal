<template>
  <mobile-home v-if="is_mobile"></mobile-home>
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

  computed: mapGetters({
    user: 'auth/user',
    is_mobile: 'auth/is_mobile',
  }),
}
</script>
