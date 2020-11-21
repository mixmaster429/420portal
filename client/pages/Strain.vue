<template>
    <div class="row">
        <div class="col-12 strains px-0">
            <strain-nav></strain-nav>
            <div class="container" style="min-height: calc(100vh - 555px);">
                <div class="page-header">
                    <img src="~assets/imgs/h_icon2.png" alt="Marijuana Strains" />
                    <h1 @click="showModal()"> Marijuana Strains</h1>
                </div>

                <div class="row mt-2 justify-content-center">
                    <div class="col-4 col-md-3 px-1">
                        <a href="/marijuana-strains/indica">
                            <img class="strains__img" src="~assets/imgs/strains/indica.png" alt="Indica">
                        </a>
                    </div>
                    <div class="col-4 col-md-3 px-1">
                        <a href="/marijuana-strains/sativa">
                            <img class="strains__img" src="~assets/imgs/strains/sativa.png" alt="Sativa">
                        </a>
                    </div>
                    <div class="col-4 col-md-3 px-1">
                        <a href="/marijuana-strains/hybrid">
                            <img class="strains__img" src="~assets/imgs/strains/hybrid.png" alt="Hybrid">
                        </a>
                    </div>
                </div>
                <client-only>
                    <div class="row justify-content-center mt-4" v-if="user && user.name == '420portal'">
                        <add-strain></add-strain>
                    </div>
                </client-only>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="headingModal" tabindex="-1" role="dialog" aria-labelledby="headingModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content text-center">
                        <div class="modal-header">
                            <h2 class="modal-title mx-auto" id="headingModalLabel">Search Thousands of Marijuana Strains</h2>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <edit-description class="strain__modal__content" type="strain-modal" :strain="modal_data" :auth="user"></edit-description>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <page-footer></page-footer>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import EditDescription from '~/components/strain/EditDescription'
import StrainNav from '~/components/strain/StrainNav'
import AddStrain from '~/components/strain/AddStrain'
import PageFooter from '~/components/PageFooter'

export default {
    components: {
        AddStrain,
        StrainNav,
        EditDescription,
        PageFooter
    },
    head () {
        return { 
            title: 'Marijuana Strains',
            meta: [
                { hid: 'title', name: 'title', content: 'Marijuana Strains - Weed Strains' },
                { hid: 'keywords', name: 'keywords', content: 'marijuana, strains, cannabis, weed' },
                { hid: 'description', name: 'description', content: 'Search Thousands of Marijuana Strains. Find Cannabis Strains Near You.' }
            ],
        }
    },
    serverPrefetch () {
        return this.getModalData()
    },
    computed: mapGetters({
        user: 'auth/user',
        modal_data: 'strain/modal_data',
    }),
    mounted: function () {
        if (!this.modal_data) {
            this.getModalData();
        }
    },
    methods: {
        getModalData(){
            return this.$store.dispatch('strain/getModalData');
        },
        showModal() {
            $("#headingModal").modal();
        }
    }
}
</script>
<style scoped lang="scss">
    .page-header {
        margin-top: 25px;
    }
</style>
