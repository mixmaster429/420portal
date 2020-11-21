<template>
    <div class="row">
        <div class="col-12">
            <desktop-home v-if="$device.isDesktop"></desktop-home>
            <mobile-home v-else></mobile-home>
        </div>
        <div class="col-md-12" id="#description_section" style="margin-top: 100px; margin-bottom:80px;">
            <div class="page-header">
                <img src="~/assets/imgs/logo.png" style="width:unset;height:50px;" alt="Marijuana Pictures and Cannabis Videos">
                <h1 @click="showModal()">Marijuana Pictures and Cannabis Videos</h1>
            </div>          
        </div>
        <div class="modal fade" id="descriptionModal">
            <div class="modal-dialog modal-lg border-0">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <button type="button" class="close text-420" data-dismiss="modal"><fa icon="times" fixed-width></fa></button>
                    </div>
                    <div class="modal-body" style="background:white !important;">
                        <h2 class="text-center">Marijuana Pictures and Cannabis Videos</h2>
                        <description field_name="home_description"></description>
                    </div>
                </div>
            </div>
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
    methods: {
        showModal() {
            $("#descriptionModal").modal();
        }
    }
}
</script>

<style scoped lang="scss">
    #description_section {
        @media (max-width: 600px) {
            display: none;
        }
    }
</style>