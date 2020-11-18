<template>
    <div class="row strains">
        <strain-nav></strain-nav>
        <strain-detail v-if="strain_data.type == 'strain'" :strain_data="strain_data"></strain-detail>
        <strain-category v-if="strain_data.type == 'category'" :category="strain_data.category"></strain-category>
        <page-footer></page-footer>
    </div>
</template>
<script>
    import { mapGetters } from "vuex";
    import StrainNav from "~/components/strain/StrainNav";
    import StrainDetail from "~/components/strain/StrainDetail";
    import StrainCategory from "~/components/strain/StrainCategory";
    import PageFooter from "~/components/PageFooter";
    export default {
        components: {
            StrainNav,
            StrainCategory,
            StrainDetail,
            PageFooter
        },
        head () {
            return { 
                title: this.strain_data.type == 'strain' ? this.strain_data.strain.name : this.strain_data.category.name + ' Marijuana Strains',
                meta: [
                    { hid: 'title', name: 'title', content: this.strain_data.type == 'strain' ? `${this.strain_data.strain.name} Marijuana Strains - ${this.strain_data.strain.name} Cannabis` : `${this.strain_data.category.name} Marijuana Strains - ${this.strain_data.category.name} Cannabis` },
                    { hid: 'keywords', name: 'keywords', content: this.strain_data.type == 'strain' ? `${this.strain_data.strain.name}, marijuana, weed, cannabis, strains, pictures, videos, images` : `${this.strain_data.category.name}, strains, marijuana, weed, cannabis, videos, pictures, images` },
                    { hid: 'description', name: 'description', content: this.strain_data.type == 'strain' ? `Find ${this.strain_data.strain.name} Marijuana Strains Near You. View ${this.strain_data.strain.name} Cannabis Pictures and Videos. ${this.strain_data.strain.name} Weed Strain` : `Search ${this.strain_data.category.name} Marijuana Strains. Find ${this.strain_data.category.name} Cannabis Strains Near You. View ${this.strain_data.category.name} Marijuana Pictures and Videos` }
                ],
            }
        },
        data() {
            return {
                slug: this.$route.params.strain,
                openEditPopup: false,
                openFollowerPopup: false,
            }
        },
        watch: {
            $route: "fetchStrainData",
        },
        serverPrefetch () {
            return this.fetchStrainData()
        },
        computed: {
            ...mapGetters({
                user: 'auth/user',
                strain_data: 'strain/data',
            }),
        },
        mounted() {
            if (!this.strain_data) {
                this.fetchStrainData();
            }
        },
        methods: {
            fetchStrainData () {
                return this.$store.dispatch('strain/fetchStrainData', this.$route.params.strain)
            },
        }        
    }
</script>