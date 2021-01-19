<template>
    <div class="row">
        <div class="col-12">
            <div class="container px-0">
                <div class="row marijuananewsec" style="margin-top: 15px;">
                    <div class="col-sm-8 marijuananewsecleft">
                        <input type="hidden" name="selected_category" class="selected_category" value="0">
                        <div class="marijuanatitle">
                            <div class="fixedHeadingDiv">
                                <div class="mobileMenuBarTop" @click="toggleSubmenuNews()">
                                    <fa :icon="mobile_menu_expanded ? 'times' : 'bars'" class="openSubMenuCategories"></fa>
                                </div>                                
                                <img src="/imgs/news.png" width="35" style="margin-right:3px;" alt="Marijuana News" />
                                <h1 @click="showModal()">Marijuana News</h1>
                            </div>
                            <div class="movableBoxedDiv">
                                <a :href="'/marijuana-news/' + item.slug" v-for="(item, index) of news_data.posts" :key="index" class="linkDivAnchor">
                                    <div class="imgsec">
                                        <img :src="serverUrl(item.image)">
                                        <div class="titlesec">
                                            <h3 style="color: #efa720;">{{item.title}}</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="mobEditSubMenu">
                            <div class="mobileSubMenuNews">
                                <ul>
                                    <li class="category-link" category-key="0" category-slug="all" @click="selectCategory('all')">All ({{news_data.posts.length}})</li>                                    
                                    <li class="category-link" v-for="(category, c_index) of news_data.categories" :key="c_index" :category-key="category.id" :category-slug="category.slug" @click="selectCategory(category)">{{`${category.name} (${category.posts_count})`}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="marijuananewsecright" style="margin-top:94px">
                            <h2>Categories </h2>
                            <p><a class="category-link" category-key="0" category-slug="all" style="color:#efa720;cursor:pointer;" @click="selectCategory('all')">All ({{news_data.total_count}})</a></p>
                            <p v-for="(category, c_index) of news_data.categories" :key="c_index"><a class="category-link" :category-key="category.id" :category-slug="category.slug" style="color:#efa720;cursor:pointer;" @click="selectCategory(category.id)">{{`${category.name} (${category.posts_count})`}}</a></p>
                            <div class="marijuananews-admin">
                                <router-link :to="{name: 'admin.post'}" v-if="auth_user && (auth_user.name == '420portal' || auth_user.name == 'writer')">
                                    <img src="/imgs/add1.png" />
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <page-footer></page-footer>        

        <div class="modal fade" id="descriptionModal">
            <div class="modal-dialog modal-lg border-0">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <button type="button" class="close text-420" data-dismiss="modal"><fa icon="times" fixed-width></fa></button>
                    </div>
                    <div class="modal-body" style="background:white !important;">
                        <h2 class="text-center">Marijuana News Updates</h2>
                        <description field_name="news_description"></description>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PageFooter from "~/components/PageFooter"
    import Description from "~/components/Description"
    import { mapGetters } from "vuex";
    export default {
        components: {
            PageFooter,
            Description,
        },        
        head () {
            return {
                title: 'Marijuana News - Marijuana Blog',
                meta: [
                    { hid: 'title', name: 'title', content: 'Marijuana News - Marijuana Blog' },
                    { hid: 'keywords', name: 'keywords', content: 'marijuana news, marijuana blog, marijuana articles' },
                    { hid: 'description', name: 'description', content: "Keep up with Marijuana News from all over the World. We write Marijuana articles that's happening today." }
                ],
            }
        },
        serverPrefetch() {
            return this.fetchNewsData();
        },
        computed: mapGetters({
            auth_user: 'auth/user',
            news_data: 'prefetch/news_data',
        }),
        data: function () {
            return {
                mobile_menu_expanded: false,
                category_id: '',
            };
        },
        mounted() {
            if (!this.news_data ) {
                this.fetchNewsData();
            }
            if(this.$route.params.category_id) {
                this.selectCategory(this.$route.params.category_id);
            }
            console.log(this.news_data);
        },
        methods: {
            fetchNewsData() {             
                return this.$store.dispatch('prefetch/fetchNewsData', this.category_id);
            },
            selectCategory(id) {
                if(id == 'all') {
                    this.category_id = '';
                } else {
                    this.category_id = id;
                }                
                this.fetchNewsData();
            },
            getPosts() {
                let uri = '/marijuana-news';
                this.axios.get(uri).then(response => {
                    this.posts = response.data.posts;
                    this.categories = response.data.categories;
                });
            },
            toggleSubmenuNews() {
                this.mobile_menu_expanded = !this.mobile_menu_expanded;
                $(".mobileSubMenuNews").slideToggle();
            },
            showModal() {
                $("#descriptionModal").modal();
            },
            serverUrl(item) {
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + 'imgs/default.png';
                }
            }
        },
    }
</script>
<style lang="scss" scoped>
    .fixedHeadingDiv {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;
        h1 {
            margin: 0 !important;
        }
    }
</style>