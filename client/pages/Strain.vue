<template>
    <div class="row">
        <div class="col-12 strains px-0">
            <strain-nav></strain-nav>
            <div class="container">
                <h1 class="text-center my-4 clickable-title" data-toggle="modal" data-target="#headingModal">
                    <img src="~assets/imgs/h_icon2.png" width="35" style="margin-top: -8px;" alt="" /> Marijuana Strains</h1>

                <div class="row mt-2 justify-content-center">
                    <div class="col-4 col-md-3 px-1">
                        <a href="#">
                            <img class="strains__img" src="~assets/imgs/strains/indica.png" alt="Indica">
                        </a>
                    </div>
                    <div class="col-4 col-md-3 px-1">
                        <a href="#">
                            <img class="strains__img" src="~assets/imgs/strains/sativa.png" alt="Sativa">
                        </a>
                    </div>
                    <div class="col-4 col-md-3 px-1">
                        <a href="#">
                            <img class="strains__img" src="~assets/imgs/strains/hybrid.png" alt="Hybrid">
                        </a>
                    </div>
                </div>

                <div class="row justify-content-center mt-4" v-if="user && user.name == '420portal'">
                    <!-- <add-strain></add-strain> -->
                </div>
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
                            <!-- <edit-description class="strain__modal__content" type="strain-modal" :strain="{{strain_dta}}" :auth="user"></edit-description> -->
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
import PageFooter from '~/components/PageFooter'

export default {
    components: {
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

    computed: mapGetters({
        user: 'auth/user',
        is_mobile: 'auth/is_mobile',
    }),
}
</script>
