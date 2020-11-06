<template>
    <div class="col-12" id="media_scroll_wrapper" style="overflow-y: auto;height:100vh">
        <div class="container">
            <h1 class="text-center my-4 category__header">{{ category.name }}</h1>
            <edit-description class="category__content" type="category" :strain="category" :auth="user"></edit-description>
            <h2 class="text-center my-4 category__header">{{ category.name }} Marijuana Strains</h2>
        </div>
        <!-- Category Strains -->
        <div class="container" style="padding-bottom: 80px;">
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 category__list">
                <div class="col p-1 container__box" v-for="(item, index) of strains" :key="index">
                    <a :href="'/marijuana-strains/' + item.slug" class="container_strain_img">
                        <img class="strains__img img-fluid" :src="serverUrl(item.main_image)" :alt="item.name + ' mairjuana'" />
                    </a>
                    <a class="strain__name py-2 py-md-2" :href="'/marijuana-strains/' + item.slug">
                        <span>{{item.name}}</span>
                    </a>
                </div>
            </div>
            <infinite-loading 
                :distance="250" 
                spinner="spiral" 
                @infinite="getallposts"            
                force-use-infinite-wrapper="#media_scroll_wrapper"
            ><div slot="no-more"></div></infinite-loading> 
        </div>       
    </div>
</template>
<script>
    import { mapGetters } from "vuex";
    import _ from 'lodash';
    import EditDescription from "./EditDescription";
    export default {
        name: 'StrainCategory',
        components: {
            EditDescription
        },
        props: ['category'],
        data: function() {
            return {
                strains: [],
                page: 1,
            }
        },
        computed: {
            ...mapGetters({
                user: 'auth/user',
            }),
        },
        mounted: function () {
            
        },
        methods: {
            getallposts($state) {
                let uri = `/category-strains`;
                let params = {
                    target_id: this.category.id,
                    model: "category",
                    per_page : 50,
                    page: this.page,
                };
                this.loading = true;
                this.axios.post(uri, params).then(response => {
                    if (response.data.strains.data.length) {
                        if(this.strains.length == 0) {
                            this.strains = response.data.strains.data;
                        } else {
                            this.strains = _.concat(this.strains, response.data.strains.data);
                        }
                        this.page++ ;
                        this.loading = false;
                        $state.loaded();
                    } else {
                        $state.complete();
                    }
                });
            },
            serverUrl(item) {
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + 'imgs/default.png';
                }
            }
        }
    }
</script>