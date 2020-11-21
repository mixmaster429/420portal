<template>
    <desktop-home v-if="!$device.isMobile"></desktop-home>
    <mobile-home v-else ></mobile-home>
</template>

<script>
import { mapGetters } from 'vuex'

import DesktopHome from '~/components/Home'
import MobileHome from '~/components/mobile/Home'
export default {
    head () {
        return { 
            title: 'Marijuana Pictures - Cannabis Videos',
            meta: [
                { hid: 'title', name: 'title', content: 'Marijuana Pictures - Cannabis Videos' },
                { hid: 'keywords', name: 'keywords', content: 'marijuana, cannabis, weed, pictures, images, videos' },
                { hid: 'description', name: 'description', content: 'Share Your Marijuana Pictures and Cannabis Videos for Fun. Weed Images and Videos' }
            ],
        }
    },

    components: {
        DesktopHome,
        MobileHome,
    },

    computed: mapGetters({
        user: 'auth/user',
    }),
}
</script>
