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
                                <h1>Marijuana News</h1>
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
                                    <li class="category-link" category-key="0" category-slug="all">All ({{news_data.posts.length}})</li>                                    
                                    <li class="category-link" v-for="(category, c_index) of news_data.categories" :key="c_index" :category-key="category.id" :category-slug="category.slug" @click="selectCategory(category)">{{`${category.name} (${category.posts_count})`}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="marijuananewsecright" style="margin-top:94px">
                            <h2>Categories </h2>
                            <p><a class="category-link" category-key="0" category-slug="all" style="color:#efa720;cursor:pointer;">All ({{news_data.posts.length}})</a></p>
                            <p v-for="(category, c_index) of news_data.categories" :key="c_index"><a class="category-link" :category-key="category.id" :category-slug="category.slug" style="color:#efa720;cursor:pointer;" @click="selectCategory(category)">{{`${category.name} (${category.posts_count})`}}</a></p>
                            <div class="marijuananews-admin">
                                <a href="#" v-if="auth_user && (auth_user.name == '420portal' || auth_user.name == 'writer')">
                                    <img src="/imgs/add1.png" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <page-footer></page-footer>       
    </div>
</template>

<script>
    import PageFooter from "~/components/PageFooter"
    import { mapGetters } from "vuex";
    export default {
        components: {
            PageFooter
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
            if (!this.news_data) {
                this.getNewsData();
            }
        },
        methods: {
            fetchNewsData() {
                let category_id = this.category_id;
                if(!category_id && process.client) {
                    category_id = localStorage.getItem('news_category_id');
                }
                return this.$store.dispatch('prefetch/fetchNewsData', this.category_id);
            },
            selectCategory(item) {
                this.category_id = item.id;
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