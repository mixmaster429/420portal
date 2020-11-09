<template>
    <div id="brand_show" style="max-height:100vh;overflow-y: auto;">
        <h1 class="page-title">
            <img src="/imgs/brand.png" width="35" alt="" />
            &nbsp;Marijuana Brands
        </h1>
        <div class="container px-0">
            <div class="brand-nav strains__extra mt-4 mt-md-5">
                <ul class="nav extra__list justify-content-center mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: tab == 'flowers'}" @click="openTab('flowers')">Flowers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: tab == 'concentrates'}" @click="openTab('concentrates')">Concentrates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: tab == 'vape-pens'}" @click="openTab('vape-pens')">Vape Pens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: tab == 'clones'}" @click="openTab('clones')">Clones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: tab == 'seeds'}" @click="openTab('seeds')">Seeds</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: tab == 'pre-roll'}" @click="openTab('pre-roll')">Pre Roll</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: tab == 'all-brands'}" @click="openTab('all-brands')">All Brands</a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="container-fluid" v-if="tab != 'all-brands'">
            <div class="row postmedia original" id="brand_menus">
                <div class="col-md-8 posts" style="min-height: calc(100vh - 250px);">
                    <div class="row mx-n1">
                        <div class="col-4 media_container" v-for="(item, index) of posts" :key="index">
                            <div v-if="$device.isMobile" class="media">
                                <router-link :to="{ name: 'Weedgram', hash:`#${index+1}`, params: {allpost : posts, start_index: index+1, page: page, model: 'brand', category : tab}}">
                                    <img :src="item.url" v-if="item.type == 'image'" />
                                    <video :src="item.url" alt v-if="item.type == 'video'" disablePictureInPicture controlsList="nodownload" onclick="this.paused ? this.play() : this.pause();"></video>
                                    <img class="video__tag__mobile" style="width:35px;" v-if="item.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                                </router-link>
                            </div>
                            <div v-else class="media" @click="changeimage(index)" @dblclick="likeMedia(item)">
                                <img :src="item.url" v-if="item.type == 'image'" />                            
                                <video :src="item.url" alt v-if="item.type == 'video'" disablePictureInPicture controlsList="nodownload" onclick="this.paused ? this.play() : this.pause();"></video>
                                <img class="video__tag__mobile" style="width:35px;" v-if="item.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                                <div class="menu-info">
                                    <p class="category">{{item.menu.category.price_type == 2 ? 'Flower | ' + item.menu.category.name : item.menu.category.name}}{{item.menu.strain ? ' | '+item.menu.strain.name : ''}}</p>
                                    <p class="item_name">{{item.menu.item_name}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <infinite-loading 
                        :distance="300" 
                        spinner="spiral" 
                        :identifier="infiniteId"
                         @infinite="getallposts"
                         force-use-infinite-wrapper="#brand_show"
                    ><div slot="no-more"></div></infinite-loading>
                </div>
                <div class="col-md-4" v-if="!$device.isMobile && selected">
                    <fixed-comment :user="selected" :allposts="posts"></fixed-comment>
                </div>
            </div>
        </div>
        <div class="container all-brands" v-else>
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <div class=" float-right offset-md-4 form-group floating-label">
                        <multiselect
                            v-model="selected_brand"
                            class="floating-label filter_portal"
                            id="filter_portal"
                            :options="brands"
                            label="name"
                            track-by="id"
                            placeholder=" "
                            :show-labels="false"
                            @select="selectBrand"
                        >
                            <span slot="noResult">No Results</span>
                        </multiselect>                            
                        <label for="">Search Brand</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 col-4 media_container px-1" v-for="(item, index) of brands" :key="index">
                    <div class="media" @click="goToProfile(item)">
                        <img :src="serverUrl(item.profile_pic ? item.profile_pic.url : '/imgs/default.png')" />
                        <div class="menu-info">
                            <p class="item_name">{{item.name}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import FixedComment from "../FixedComment";
    import Multiselect from "vue-multiselect";
    import firebase from "../../Firebase";
    import _ from "lodash";
    import { mapGetters } from "vuex";
    export default {
        name: 'BrandShow',        
        components: {
            FixedComment,
            Multiselect,
        },
        data() {
            return  {
                tab: 'flowers',
                posts: [],
                brands: [],
                selected_brand: null,
                selected: null,
                page: 1,
                infiniteId: +new Date(),
            };
        },
        computed: mapGetters({
            auth_user: 'auth/user',        
        }),
        mounted() {
            this.getBrands();
            if(process.client) {
                this.scroll();
            }
        },
        methods: {
            getBrands() {
                let uri = '/brand/get_all';
                this.axios.get(uri).then(response => {
                    this.brands = response.data.brands;
                });
            },
            openTab(slug) {
                this.tab = slug;
                this.selected = null;
                this.posts = [];
                this.page = 1;
                if(slug != 'all-brands') {
                    this.infiniteId++;
                }
            },
            getallposts($state) {
                let uri = '/brand/get_category_media';
                let params = {
                    category: this.tab,
                    page: this.page,
                };
                this.axios.post(uri, params).then(response => {
                    if (response.data.postdata.data.length) {
                        if(this.posts.length == 0) {
                            this.posts = response.data.postdata.data;
                        } else {
                            this.posts = _.concat(this.posts, response.data.postdata.data);
                        }
                        if (this.posts.length > 0) {
                            this.selected = this.posts[0];
                        }
                        this.page++ ;
                        this.loading = false;
                        $state.loaded();
                    } else {
                        $state.complete();
                    }
                });
            },
            changeimage(index) {
                this.selected = this.posts[index];
            },
            likeMedia(item) {
                if (this.auth_user) {
                    const params = {
                        target_id: item.id,
                        target_model: "post"
                    };
                    let uri = "/like/addlike";
                    this.axios.post(uri, params).then(response => {
                        item.likes = response.data;
                        if(item.user_liked) {
                            item.user_liked = false;
                        } else {
                            item.user_liked = true;
                            if(this.auth_user.id != item.user_id) {                           
                                let noti_fb = firebase.database().ref('notifications/' + item.user_id).push();
                                noti_fb.set({
                                    notifier_id: this.auth_user.id,
                                    type: 'like',
                                }); 
                            }
                        }
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            goToProfile(item) {
                window.location.href = "/" + item.username;
            },
            scroll() {
                var header = document.getElementById("brand_menus");
                var sticky = header.offsetTop;
                var sticky = sticky - 100;
                window.onscroll = () => {
                    if (window.pageYOffset > sticky) {
                        header.classList.remove("original");
                    } else {
                        header.classList.add("original");
                    }
                };
            },
            selectBrand(selected_brand, id) {
                 $(".filter_portal").siblings('label').addClass('focused');
                window.location.href = "/" + selected_brand.username;
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
<style lang="scss" scoped>
    .page-title {
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #EFA720;
        margin-top: 60px;
        @media (max-width: 600px) {
            margin-top: 30px;
            font-size: 28px;
        }
    }
    .brand-nav {
        .nav-item {
            .nav-link {
                cursor: pointer;
                color: #EFA720;
            }
        }
    }
    #brand_show .postmedia.original .right_side {
        position: initial !important;
        width: 100%;
        max-width: 500px;
    }
    
    .menu-info {
        position: absolute;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.8);
        width: 100%;
        padding: 2px 10px;
        .category {
            margin: 0;
            color: gray;
            font-size: 12.5px;
        }
        .item_name {
            margin: 0;
            color: #EFA720;
            font-size: 18px;
        }
    }
    .all-brands {
        .media_container {
            .media {
                position: relative;
                padding-bottom: 100%;
                cursor: pointer;
                img {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                .menu-info {
                    padding: 10px;
                    @media (max-width: 600px) {
                        padding: 3px 5px;
                        .item_name {
                            font-size: 14px;
                        }
                    }
                }
            }
        }
    }

    .floating-label {
        position: relative;
        margin-bottom: 22px;
        width: 100%;
    }

    .floating-label label {
        position: absolute !important;
        pointer-events: none;
        top: 30%;
        transition: 0.2s ease all;
        -moz-transition: 0.2s ease all;
        -webkit-transition: 0.2s ease all;
    }

    .multiselect--active ~ label,
    .floating-multiselect.selected > label {
        top: -15px;
        color: gray;
    }

</style>