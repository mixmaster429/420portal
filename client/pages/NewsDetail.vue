<template>
    <div class="row">
        <div class="col-12">
            <div class="container px-0" id="news_show">
                <div class="row marijuananewsec">
                    <div class="col-md-8 marijuananewsecleft">
                        <input type="hidden" name="selected_category" class="selected_category" value="0">
                        <div class="page-title">
                            <h3 style="text-align: left; margin-left:10px;font-size:25px;">
                                <a href="/marijuana-news" style="color:#EFA720;"><fa icon="arrow-left" fixed-width></fa></a>
                                Marijuana News
                            </h3>
                        </div>
                        <div class="to-replace-div">
                            <div class="page-content">
                                <h1 class="py-md-4" style="font-size: 30px;">{{post.title}}</h1>
                                <div class="blog-image">
                                    <img :src="serverUrl(post.image)" :alt="post.title">
                                </div>
                                <div class="blog-date"><p class="post_time" style="font-size:16px;color:gray;">{{changeDateFormat(post.created_at)}}</p></div>
                                <div class="blog-content" v-html="post.description"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 px-md-0">
                        <mini-comment :page="post" model="news" :comments_count="post.comments.length" v-if="$device.isMobile"></mini-comment>
                        <div class="modal fade" id="commentModal" style="width: calc(100% - 10px);" v-if="$device.isMobile">
                            <div class="modal-dialog" role="document" style="margin:0;">
                                <div class="modal-content comment_page">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><fa icon="arrow-left" fixed-width></fa></span></button>
                                        <h5 class="modal-title" id="couponModalLabel">Comments</h5>
                                    </div>
                                    <div class="modal-body" style="padding: 0;">
                                        <page-comment :page="post" model="news"></page-comment>
                                    </div>
                                </div>
                            </div>
                        </div>         
                        <div class="right-panel" v-if="!$device.isMobile && news_data">
                            <div class="marijuananewsecright" style="margin-top:94px">
                                <h2>Categories </h2>
                                <p><a class="category-link" category-key="0" category-slug="all" style="color:#efa720;cursor:pointer;">All ({{news_data.posts.length}})</a></p>
                                <p v-for="(category, c_index) of news_data.categories" :key="c_index"><a class="category-link" :category-key="category.id" :category-slug="category.slug" style="color:#efa720;cursor:pointer;" @click="selectCategory(category)">{{`${category.name} (${category.posts_count})`}}</a></p>
                                <div class="marijuananews-admin">
                                    <router-link :to="{name: 'admin.post'}" v-if="auth_user && (auth_user.name == '420portal' || auth_user.name == 'writer')">
                                        <img src="/imgs/add1.png" />
                                    </router-link>
                                </div>
                            </div>
                            <div class="card comment-section original" id="news_comment_box">
                                <div class="card-header comment-header" style="background-color: #000;">
                                    Comments
                                </div>
                                <div class="card-body p-0">
                                    <page-comment :page="post" model="news"></page-comment>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PageFooter from "~/components/PageFooter";
    import PageComment from "~/components/PageComment";
    import MiniComment from "~/components/mobile/MiniComment";
    import { mapGetters } from "vuex";
    export default {        
        components: {
            PageFooter,
            PageComment,
            MiniComment
        },        
        head () {
            return {
                title: this.post.title,
                meta: [
                    { hid: 'title', name: 'title', content: this.post.meta_title },
                    { hid: 'keywords', name: 'keywords', content: this.post.meta_keywords },
                    { hid: 'description', name: 'description', content: this.post.meta_description }
                ],
            }
        },
        serverPrefetch() {
            return this.fetchNews();
        },
        computed: mapGetters({
            auth_user: 'auth/user',
            news_data: 'prefetch/news_data',
            post: 'prefetch/news',
        }),
        data: function () {
            return {
                category_id: '',
                slug: this.$route.params.slug,
            };
        },
        mounted() {
            if (!this.post) {
                this.fetchNews();
            }
            this.$store.dispatch('prefetch/fetchNewsData', '');
        },
        watch: {
            $route: "fetchNews",
        },
        methods: {
            fetchNews() {
                return this.$store.dispatch('prefetch/fetchNews', this.$route.params.slug);
            },
            selectCategory(item) {
                this.$router.push({ name: 'news' })
            },
            changeDateFormat(post_date) {
                let date_only = post_date.split(' ')[0];
                let dArr = date_only.split("-");
                return dArr[1] + "/" + dArr[2] + "/" + dArr[0];
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
    #commentModal {
        .modal-header {
            color: #EFA720;
            font-size: 22px;
            padding: 6px 0.8rem;
            background: #000;
            border-radius: 0;
            -webkit-box-pack: start;
            justify-content: flex-start;
            -webkit-box-align: center;
            align-items: center;
            border-bottom: solid 1px white;
            .close {
                position: initial;
                margin-left: 0;
                padding: 0;
                color: #EFA720;
                z-index: 2;
                text-shadow: unset;
            }
            h5 {
                color: #EFA720 !important;
                padding-left: 25px;
                font-size: 22px
            }
        }
    } 
</style>