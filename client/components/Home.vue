<template>
    <div class="row postmedia">
        <vs-sidebar position-right parent="body" default-index="1" color="primary" class="sidebarx" spacer v-model="active">                
            <div class="search-body mt-3">
                <form action="" @change="keywordChange('search')">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox filter-input mt-2">
                            <input type="checkbox" class="custom-control-input" name="account_type[]" value="user" v-model="account_type" id="search-users" />
                            <label class="custom-control-label" :for="'search-users'">Users</label>
                        </div>
                        <div class="custom-control custom-checkbox filter-input mt-2">
                            <input type="checkbox" class="custom-control-input" name="account_type[]" value="brand" v-model="account_type" id="search-brand" />
                            <label class="custom-control-label" :for="'search-brand'">Brands <img src="~assets/imgs/brand.png" width="25" alt="" /></label>
                        </div>
                        <div class="custom-control custom-checkbox filter-input mt-2" >
                            <input type="checkbox" class="custom-control-input" name="account_type[]" value="delivery" v-model="account_type" id="search-delivery" />
                            <label class="custom-control-label" :for="'search-delivery'">Delivery <img src="~assets/imgs/delivery.png" width="25" alt="" /></label>
                        </div>
                        <div class="custom-control custom-checkbox filter-input mt-2">
                            <input type="checkbox" class="custom-control-input" name="account_type[]" value="storefront" v-model="account_type" id="search-storefront" />
                            <label class="custom-control-label" :for="'search-storefront'">Storefront <img src="~assets/imgs/dispensary.png" width="25" alt="" /></label>
                        </div>
                    </div>
                    <div class="form-group floating-label mt-5" v-show="account_type.includes('storefront') || account_type.includes('delivery')">
                        <select class="form-control floating-select mt-2" id="search_distance" v-model="distance">
                            <option value="" hidden></option>
                            <option value="15">&lt; 15 Miles</option>
                            <option value="30">&lt; 30 Miles</option>
                            <option value="100">&lt; 100 Miles</option>
                            <option value="10000">To the Moon</option>
                        </select>
                        <label for="search_distance" :class="{focused : distance}">Distance</label>
                    </div>
                    <div class="form-group text-center mt-4">
                        <span class="text-420" id="reset_option" @click="resetSearchOption">Reset Options</span>
                    </div>
                </form>
            </div>
        </vs-sidebar>

        <div class="col-8 text-center posts">
            <div class="search">
                <img src="~assets/imgs/search_option.png" class="search_option" alt @click="active=!active" />
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" @click="getfollowingposts()">
                            <fa icon="user-plus" fixed-width />
                        </span>
                    </div>
                    <input type="text" class="form-control search_weedgram" placeholder="Search" @keypress="keywordChange($event)" v-model="keyword" />
                    <div class="input-group-prepend">
                        <span class="input-group-text" @click="getbookmarked()">
                            <fa icon="bookmark" fixed-width />
                        </span>
                    </div>
                </div>
            </div>
            <div class="container">
                <div id="media_scroll_wrapper" style="overflow-y: auto;height:100vh">  
                    <div class="row justify-content-center" v-if="defaultpost != null">
                        <div class="col-4 media_container">
                            <div class="media" @click="showdefault">
                                <img v-bind:src="imageLoad(defaultpost.url)" alt />
                            </div>
                        </div>
                    </div>
                                  
                    <div class="row">
                        <div class="col-4 media_container" v-for="(item, index) in posts" :key="index">
                            <div class="media" @click="changeimage(index)" @dblclick="like(item)">
                                <img v-bind:src="imageLoad(item.url)" alt v-if="item.type == 'image'" />
                                <video v-bind:src="imageLoad(item.url)" alt v-if="item.type == 'video'" disablePictureInPicture controlsList="nodownload" onclick="this.paused ? this.play() : this.pause();"></video>
                                <img class="video__tag__mobile" style="width:35px;" v-if="item.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                            </div>
                        </div>
                    </div>
                    <infinite-loading 
                        :distance="300" 
                        :identifier="infiniteId" 
                        spinner="spiral" 
                        @infinite="getallposts"
                        force-use-infinite-wrapper="#media_scroll_wrapper"
                    ><div slot="no-more"></div></infinite-loading>
                </div>
            </div>
        </div>
        <client-only>
            <div class="col-4">
                <fixed-comment :allposts="posts" :media="selected" v-if="selected"></fixed-comment>
            </div>
        </client-only>
        <page-footer></page-footer>
    </div>
</template>

<script>
import FixedComment from "./FixedComment";
import PageFooter from "./PageFooter";
import { mapGetters } from 'vuex'
import axios from 'axios';
import _ from 'lodash';


export default {
    name: "DesktopHome",
    data() {
        return {
            defaultpost: null,
            posts: [],
            selected: null,
            bookmark: false,
            following: false,
            active: false,
            loading: false,
            keyword: '',
            page : 1,
            last_page: 0,
            infiniteId: +new Date(),
            is_last : false,

            account_type: [],
            distance : '',

            latitude : 0,
            longitude : 0,
        };
    },
    watch: {
        $route: "getallposts",
    },
    computed: mapGetters({
        user: 'auth/user',
    }),
    methods: {
        getallposts($state) {
            let uri = "/post/allhomepost";
            if(this.following || this.bookmark) {
                this.keyword = '';
            }
            let params = {
                keyword : this.keyword,
                bookmark : this.bookmark,
                following : this.following,
                account_type : this.account_type,
                distance : this.distance,
                latitude : this.latitude,
                longitude : this.longitude,
                page : this.page,
            };
            this.loading = true;
            axios.post(uri, params).then(response => {

                this.defaultpost = response.data.default;
                // console.log(JSON.stringify(response.data.allposts.data[0]).length)
                if(response.data.allposts.data.length) {
                    if(this.posts.length == 0) {
                        this.posts = response.data.allposts.data;
                        this.selected = this.defaultpost;
                    } else {
                        this.posts = _.concat(this.posts, response.data.allposts.data);
                    }
                    this.page++ ;
                    this.loading = false;

                    $state.loaded();
                } else {
                    $state.complete();
                    this.is_last = true;
                }
            });
        },
        getbookmarked() {
            if (this.user) {
                this.following = false;
                this.bookmark = true;
                this.keywordChange('bookmark');
            } else {
                $("#loginmodal").modal("show");
            }
        },
        getfollowingposts(){
            if (this.user) {
                this.bookmark = false;
                this.following = true;
                this.keywordChange('following');
            } else {
                $("#loginmodal").modal("show");
            }
        },
        showdefault() {
            this.selected = this.defaultpost;
        },
        changeimage(index) {
            this.selected = this.posts[index];
        },
        keywordChange(e) {
            if(e == 'search' || e == 'bookmark' || e == 'following' || e.keyCode == 13){                
                if(e.keyCode){
                    this.bookmark = this.following = false;
                }
                this.page = 1;
                this.posts = [],
                this.infiniteId += 1;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        resetSearchOption(){
            this.account_type = [];
            this.distance = '';
            this.keywordChange('search')
        },
        getPosition(position){
            this.latitude = position.coords.latitude;
            this.longitude = position.coords.longitude;
        },
        changeDistance(){
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.getPosition);
            } else {
                alert(
                    "Geolocation is not supported by this browser. \n Please enable it."
                );
            }
        },
        like(item) {
            if (this.user) {
                const params = {
                    target_id: item.id,
                    target_model: "post"
                };
                let uri = "/like/addlike";
                axios.post(uri, params).then(response => {
                    item.likes = response.data;
                    if(item.user_liked) {
                        item.user_liked = false;
                    } else {
                        item.user_liked = true;
                        if(this.user != item.user_id) {                           
                            let noti_fb = firebase.database().ref('notifications/' + item.user_id).push();
                            noti_fb.set({
                                notifier_id: this.user,
                                type: 'like',
                            }); 
                        }
                    }
                });
            } else {
                $("#loginmodal").modal("show");
            }
        },
        imageLoad(item) {
            if(item.charAt(0) != '/'){item = '/' + item;}
            try {
                return process.env.serverUrl + item;
            } catch (error) {
                return process.env.serverUrl + 'imgs/default.png';
            }
        }
        
    },
    mounted() {
        this.changeDistance();
    },
    components: {
        FixedComment,
        PageFooter,
    }
};
</script>

<style lang="scss" scoped>
    .search-header {
        padding: 8.5px 0;
        border-bottom: solid 1px white;
        text-align: center;
    }
    .search-body {
        padding: 20px;
        #reset_option {
            cursor: pointer;
        }
    }

    .custom-checkbox {
        .custom-control-label {
            font-size: 18px;
            padding-left: 5px; 
            cursor: pointer;
            &::before {
                border-radius: 0;
                border-color: #d2d1d0;
                background-color: #000;
                color: #000;
                width: 21px;
                height: 21px;
            }
            &::after {
                left: -1.59rem;
                background: no-repeat 80% / 80% 80%;
                width: 21px;
                height: 21px;
            }
        }
        .custom-control-input:checked ~ .custom-control-label::after {
            background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OENBRjk0MUE2NjBBMTFFQTlEOTVGMzFBMEE3NjhGMzciIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OENBRjk0MUI2NjBBMTFFQTlEOTVGMzFBMEE3NjhGMzciPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo4Q0FGOTQxODY2MEExMUVBOUQ5NUYzMUEwQTc2OEYzNyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo4Q0FGOTQxOTY2MEExMUVBOUQ5NUYzMUEwQTc2OEYzNyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Piya4HIAAA0RSURBVHjazFlpdFXV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');
        }
    }

    .floating-label {
        position: relative;
        margin-bottom: 22px;
    }
    .floating-label label {
        // color: #999;
        // font-size: 14px;
        // font-weight: normal;
        position: absolute !important;
        pointer-events: none;
        // left: 5px;
        top: 30%;
        transition: 0.2s ease all;
        -moz-transition: 0.2s ease all;
        -webkit-transition: 0.2s ease all;
    }
    .floating-input,
    .floating-select {
        // font-size: 14px;
        padding: 4px 2px;
        display: block;
        width: 100%;
        background-color: transparent;
        border: none;
        box-shadow: unset;
    }
    .floating-input:focus ~ label,
    .floating-select:focus ~ label,
    .floating-input:not(:placeholder-shown) ~ label,
    .floating-textarea.focused label,
    .floating-select ~ label.focused {
        top: -15px;
        color: gray;
    }
</style>