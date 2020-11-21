<template>
    <div class="row">
        <div class="col-12">
            <desktop-home v-if="$device.isDesktop"></desktop-home>
            <mobile-home v-else></mobile-home>
        </div>
        <div class="col-md-10 card card-body mx-auto" id="#description_section" style="margin-top: 100px;background-color: #FFF !important;text-align:initial;">
            <div class="page-header">
                <img src="~/assets/imgs/logo.png" style="width:unset;height:50px;" alt="Marijuana Pictures and Cannabis Videos">
                <h1>Marijuana Pictures and Cannabis Videos</h1>
            </div>
            <description field_name="home_description"></description>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import DesktopHome from '~/components/Home'
import MobileHome from '~/components/mobile/Home';
import Description from '~/components/Description';
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
        Description,
    },

    computed: mapGetters({
        user: 'auth/user',
    }),
}
</script>

<style scoped lang="scss">
    #description_section {
        @media (max-width: 600px) {
            display: none;
        }
    }
</style>