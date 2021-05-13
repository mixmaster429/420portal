<template>
  <!-- <div style="position: fixed"> -->
    <div class="slide_show">
        <div class="weedgram-header" id="weedgram-header" ref="weedgram_header" v-if="model == 'user' || model == 'portal' || model == 'strain'">
            <h4 class="my-1"><fa icon="arrow-left" fixed-width @click="goBack()" /> {{username}}</h4>
        </div>
        <div id="media_scroll_wrapper" style="max-height:100vh;overflow-y: auto;" :style="{'padding-top': model == 'user' || model == 'portal' || model == 'strain' ? '40px' : 'unset'}" ref="scroll_wrapper" @scroll="handleScroll()">
            <div :class="{header_show: model == 'user' || model == 'portal' || model == 'strain'}" ref="slide_container" id="slide_container">
                <div class="slide_media" v-for="(item, index) in posts" :key="index" :id="index+1">
                    <div class="media_header">
                        <div class="user_logo">
                            <a :href="'/' + item.user.username">
                                <img :src="serverUrl(item.user.profile_pic ? item.user.profile_pic.url : default_logo)" alt="Profile picture" />
                            </a>
                        </div>
                        <div class="user_details flex-column">
                            <div class="user_holder d-flex align-items-center">
                                <div class="user_data">
                                    <a :href="'/' + item.user.username" class="username">{{item.user.name}}</a>
                                    <span class="px-2" style="color: #fff !important;" v-show="logged_user_id != item.user_id">•</span>
                                    <span class="followuser" @click="follow(item.user_id)" v-show="logged_user_id != item.user_id" v-if="!item.isfollower">Follow</span>
                                    <img src="~assets/imgs/unfollow.png" alt class="pf-unfollow" @click="unfollow(item.user_id)" v-show="logged_user_id != item.user_id" v-if="item.isfollower" />
                                    <div class="user_icons" v-if="item.user.type != 'user'">
                                        <img src="~assets/imgs/delivery.png" v-if="item.user.store_type === 2 || item.user.store_type === 3" alt />
                                        <img src="~assets/imgs/dispensary.png" v-if="item.user.store_type === 1 || item.user.store_type === 3" alt />
                                        <img src="~assets/imgs/brand.png" v-if="item.user.type == 'brand'" alt />
                                    </div>
                                </div>
                                <div v-if="item.loged_user || item.user_role === 1" class="dropdown ml-auto">
                                    <a id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <fa icon="ellipsis-h" fixed-width class="dropdown-button"></fa>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <router-link :to="{ name: 'edit_media', params: {editData: item.id, mainData: item}}">
                                            <p v-if="item.model === 'post'" class="dropdown-item" style="color: #efa720;" href="#">Edit</p>
                                        </router-link>
                                        <a class="dropdown-item" href="#" @click.prevent="deleteMedia(item.id)">Delete</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="media_body">
                        <div class="slide_image" @click="user ? likeMedia(item) : () => false">
                            <img :src="serverUrl(item.url)" alt v-if="item.type == 'image'" />
                            <video :src="serverUrl(item.url)" alt v-if="item.type == 'video' && ($route.params.start_index === index+1)"  onclick="this.paused ? this.play() : this.pause();" disablepictureinpicture controlslist="nodownload" autoplay playsinline></video>
                            <video :src="serverUrl(item.url)" alt v-else-if="item.type == 'video'"  onclick="this.paused ? this.play() : this.pause();" disablepictureinpicture controlslist="nodownload" playsinline></video>
                        </div>
                        <div class="menu-panel" v-if="item.menu && item.menu.is_active" :class="{'brand-menu': item.user.type == 'brand'}">
                            <p class="menu-category">
                                <span>{{item.menu.category.price_type == 2 ? 'Flower | ' + item.menu.category.name : item.menu.category.name}}</span>
                                <span>{{item.menu.strain ? ' | ' + item.menu.strain.name : ''}}</span>
                            </p>
                            <p class="item_name">{{item.menu.item_name}}</p>
                            <p class="price_data mb-0" v-if="item.menu.portal && item.menu.portal.type == 'company'">
                                <span class="text-white pr-2" v-if="item.menu.price_each != null">{{item.menu.price_each}}<sup class="text-420">each</sup></span>
                                <span class="text-white pr-2" v-if="item.menu.price_half_gram != null">{{item.menu.price_half_gram}}<sup class="text-420">1/2g</sup></span>
                                <span class="text-white pr-2" v-if="item.menu.price_gram != null">{{item.menu.price_gram}}<sup class="text-420">g</sup></span>
                                <span class="text-white pr-2" v-if="item.menu.price_eighth != null">{{item.menu.price_eighth}}<sup class="text-420">1/8</sup></span>
                                <span class="text-white pr-2" v-if="item.menu.price_quarter != null">{{item.menu.price_quarter}}<sup class="text-420">1/4</sup></span>
                                <span class="text-white pr-2" v-if="item.menu.price_half_oz != null">{{item.menu.price_half_oz}}<sup class="text-420">1/2</sup></span>
                                <span class="text-white pr-2" v-if="item.menu.price_oz != null">{{item.menu.price_oz}}<sup class="text-420">oz</sup></span>
                            </p>
                        </div>
                    </div>
                    <div class="media_toolbar mt-2">
                        <div class="heart_icon" @click="likepost(index)" v-if="!item.user_liked">
                            <fa :icon="['far', 'heart']" fixed-width />
                            <p>{{item.likes}}</p>
                        </div>
                        <div class="heart_icon" @click="unlikepost(index)" v-else>
                            <fa icon="heart" fixed-width />
                            <p>{{item.likes}}</p>
                        </div>
                        <router-link :to="{ name: 'comment', params: {target : item.id, media: item}}">
                            <div class="comment_icon">
                                <fa :icon="['far', 'comment']" fixed-width />
                                <p class="text-white text-center">{{item.count_comment}}</p>
                            </div>
                        </router-link>
                        <div class="bookmark_icon" @click="addbookmark(index)" v-if="!item.user_saved">
                            <fa :icon="['far', 'bookmark']" fixed-width />
                        </div>
                        <div class="bookmark_icon" @click="removebookmark(index)" v-else>
                            <fa :icon="['fas', 'bookmark']" fixed-width />
                        </div>

                        <div class="bookmark_icon" @click="open_tag_dialog(item)" v-if="hasmediatags(item)">
                            <img src="/imgs/taged.png" alt="" width="27px">
                        </div>
                    </div>
                    <div class="media_description">
                        <p v-if="item.description">
                            <a :href="'/' + item.user.username">
                                <img :src="serverUrl(item.user.profile_pic ? item.user.profile_pic.url : default_logo)" alt />
                                {{item.user.name}}
                            </a>
                            <span>
                                <span v-if="item.description_expanded == true">{{item.description}}</span>
                                <span v-else>{{item.description.substring(0, 140)}}</span>
                                <span class="text-420 ml-1 btn-readmore" @click="showDescription(index)" v-show="item.description_expanded != true && item.description.length > 140">Read More</span>
                            </span>
                        </p>
                        <router-link :to="{ name: 'comment', params: {target : item.id, media: item}}" v-if="item.count_comment > 0">
                            <p class="allcomments mb-1">View all <span class="text-primary">{{item.count_comment}}</span> comments</p>
                        </router-link>
                        <div class="v_comment_box">
                            <div class="proifle-img">
                                <img class="userlogo" :src="serverUrl(user.profile_pic.url)" v-if="user !== null && user.profile_pic !== null" alt />
                                <img class="userlogo" v-else src="/imgs/default_sm.png" />
                            </div>
                            <div class="commentbox">
                                <textarea rows="1"
                                    v-model="item.comment_text"
                                    placeholder="Write a comment..."
                                    @focus="hideFooter()"
                                    @blur="showFooter($event, item.id, index)"
                                ></textarea>
                            </div>
                            <div class="button-group">
                                <button class="btn" :id="'btn_post_'+item.id" @click="postComment(item.id, index)">Post</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <infinite-loading
                ref="infinite_loading"
                :distance="400"
                spinner="spiral"
                @infinite="getallposts"
                force-use-infinite-wrapper="#media_scroll_wrapper"
            ><div slot="no-more" class="text-center">No Posts</div></infinite-loading>
        </div>

        <!-- <vue-bottom-dialog v-model="dialog" > -->
        <vs-popup class="media_tags" type="border" title="Tagged In This Media" :active.sync="dialog" v-if="selected">
          <div>
            <media-tags :media="selected" :logged_user_id="logged_user_id"></media-tags>
          </div>
        </vs-popup>
        <!-- </vue-bottom-dialog> -->
    </div>
  <!-- </div> -->
</template>

<script>
    import firebase from "../../Firebase";
    import MediaTags from "~/components/media/MediaTags"
    import { mapGetters } from 'vuex'
    import _ from 'lodash';
    export default {
        name : 'Weedgram',
        components: {
            MediaTags,
        },
        watch : {
            '$route.params.start_index' : function(new_index) {
                // this.setScroll();
            }
        },
        computed: mapGetters({
            user: 'auth/user',
        }),
        data() {
            return {
                posts: [],
                start_index: this.$route.params.start_index,
                from: 0,
                auth_user: null,
                logged_user_id: 0,
                default_logo: '/imgs/default_sm.png',
                keyword: this.$route.params.keyword ? this.$route.params.keyword : '',
                page: this.$route.params.page,
                model: this.$route.params.model,
                bookmark: this.$route.params.bookmark,
                following: this.$route.params.following,
                currentId: this.$route.params.currentId,
                category: this.$route.params.category,
                initial: true,
                username : this.$route.params.username,
                comment_text : '',
                focused_index : null,
                clicks: 0,
                timer: null,
                dialog: false,
                selected: null
            };
        },
        created() {
            this.posts = this.$route.params.allpost;
        },
        mounted() {
            if(!this.posts){
                if(this.model == 'portal' || this.model == 'user') {
                    window.location.href = "/" + username;
                } else if(this.model == 'strain') {
                    window.location.href = '/';
                } else {
                    window.location.href = '/';
                }
            }
            if(process.client) {
                let scroll_div = document.getElementById(this.start_index);
                let offset = 63;
                if(this.model == 'user' || this.model == 'portal') {
                    offset = 40;
                } else if(this.model == 'strain') {
                    offset = 160;
                }
                // // check iphone chrome
                // if(/CriOS/i.test(navigator.userAgent) && /iphone|ipod|ipad/i.test(navigator.userAgent)){
                //     offset = offset + 119;
                // }

                let scroll_to = scroll_div ? scroll_div.offsetTop - offset : 0;

                if(this.posts.length > 20) scroll_to = scroll_to - 65
                this.$refs.scroll_wrapper.scrollTo(0, scroll_to);
            }
            if (this.user) {
                this.logged_user_id = this.user.id;
            }

            if(this.model == 'strain') {
                $('#strain_show_page').hide();
            }
        },
        methods: {
            hasmediatags(item) {
                if(item.model === "menu") {
                    if(item.menu.strain) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    if(item.tagged_portal || item.tagged_strain || item.tagged_users.length > 0) {
                        return true;
                    } else {
                        return false
                    }
                }
            },
            open_tag_dialog(item) {
                this.dialog = true
                this.selected = item;
            },
            getallposts($state) {
                let uri = '';
                let params = {};
                if(this.model) {
                    if(this.model == 'user') {
                        uri = "/profile/getallposts";
                        params = {
                            model : 'user',
                            currentId : this.currentId,
                            page : this.page,
                        };
                    } else if(this.model == 'portal') {
                        uri = "/profile/getallposts";
                        params = {
                            model : 'portal',
                            target_id : this.currentId,
                            page : this.page,
                        };
                    } else if(this.model == 'strain') {
                        uri = this.$route.params.url;
                        params = {
                            model: "strain",
                            target_id: this.currentId,
                            page: this.page,
                        };
                    } else if(this.model == 'brand') {
                        uri = '/brand/get_category_media';
                        params = {
                            category: this.category,
                            page: this.page,
                        };
                    }
                } else {
                    uri = "/post/allhomepost";
                    params = {
                        keyword : this.keyword,
                        following : this.following,
                        bookmark : this.bookmark,
                        per_page : 9,
                        page : this.page,
                    };
                }

                this.axios.post(uri, params)
                    .then(response => {
                        this.defaultpost = response.data.default;
                        let allposts = this.model ? response.data.postdata : response.data.allposts;
                        if (allposts.data.length) {
                            this.posts = _.concat(this.posts, allposts.data);
                            this.page++;
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                    });
            },
            deleteMedia(id) {
                if (!window.confirm('Are you sure?')) {
                    return false;
                }
                let uri = `/media/${id}`;
                let selectedItem = this.posts;
                // console.log(selectedItem);
                this.axios.delete(uri).then(res => {
                    const data = item => item.id === res.data.id;
                    const index = selectedItem.findIndex(data);
                    selectedItem.splice(index, 1);
                    // this.selected = selectedItem[0];
                    this.$toast.success("Media has been deleted", "");
                });
            },
            likepost(index) {
                if (this.user) {
                    const params = {
                        target_id: this.posts[index].id,
                        target_model: "post"
                    };
                    let uri = "/like/addlike";
                    this.axios.post(uri, params).then(response => {
                        this.posts[index].likes = response.data;
                        this.posts[index].user_liked = true;
                        if(widnow.user != this.posts[index].user_id) {
                            let noti_fb = firebase.database().ref('notifications/' + this.posts[index].user_id).push();
                            noti_fb.set({
                                notifier_id: this.user.id,
                                type: 'like',
                            });
                        }
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            unlikepost(index) {
                if (this.user) {
                    const params = {
                        target_id: this.posts[index].id,
                        target_model: "post"
                    };
                    let uri = "/like/unlike";
                    this.axios.post(uri, params).then(response => {
                        this.posts[index].likes = response.data;
                        this.posts[index].user_liked = false;
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            likeMedia(item) {
                if (this.user) {
                    this.clicks++
                    if(this.clicks === 1) {
                        var self = this
                        this.timer = setTimeout(function() {
                            self.clicks = 0
                        }, 700);
                    } else {
                        clearTimeout(this.timer);
                        this.clicks = 0;
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
                                if(this.user.id != item.user_id) {
                                    let noti_fb = firebase.database().ref('notifications/' + item.user_id).push();
                                    noti_fb.set({
                                        notifier_id: this.user,
                                        type: 'like',
                                    });
                                }
                            }
                        });
                    }
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            showlogin() {
                $("#loginmodal").modal("show");
            },
            follow(id) {
                if (this.user) {
                    if(this.user.type != 'user') return false;
                    let uri = '/user/follow';
                    let params = {
                        user_id: this.user.id,
                        follower_id: id
                    };
                    this.axios.post(uri, params).then(response => {
                        if(this.user.type == 'user') {
                            if(this.user.id != id) {
                                let noti_fb = firebase.database().ref('notifications/' + id).push();
                                noti_fb.set({
                                    notifier_id: this.user.id,
                                    type: 'follow',
                                });
                            }
                        }
                        for (let i = 0; i < this.posts.length; i++) {
                            if (this.posts[i]['user']['id'] === id)
                                this.posts[i]['isfollower'] = !this.posts[i]['isfollower'];
                        }
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },
            unfollow(id) {
                if (this.user) {
                    if(this.user.type != 'user') return false;
                    let uri = '/user/unfollow';
                    let params = {
                        user_id: this.user.id,
                        follower_id: id
                    };
                    this.axios.post(uri, params).then(response => {
                        for (let i = 0; i < this.posts.length; i++) {
                            if (this.posts[i]['user']['id'] === id)
                                this.posts[i]['isfollower'] = !this.posts[i]['isfollower'];

                        }
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },
            addbookmark(index) {
                if (this.user) {
                    let uri = "/bookmark/store";
                    let params = {
                        target_id: this.posts[index].id,
                    };
                    this.axios.post(uri, params).then(response => {
                        this.posts[index].user_saved = response.data;
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            removebookmark(index) {
                let uri = "/bookmark/delete";
                let params = {
                    target_id: this.posts[index].id,
                };
                this.axios.post(uri, params).then(response => {
                    this.posts[index].user_saved = response.data;
                });
            },
            handleScroll(){
                let scroll_wrapper = document.getElementById('media_scroll_wrapper');
                let scroll_wrapper_top = scroll_wrapper.scrollTop;
                let scroll_wrapper_bottom = scroll_wrapper_top + scroll_wrapper.offsetHeight;
                var video_elements = document.getElementsByTagName('video');
                video_elements.forEach(element => {
                    let parent = element.closest('.slide_media');
                    if(parent) {
                        let element_top = parent.offsetTop + 60;
                        let element_bottom = element_top + parent.offsetWidth;
                        if(element_top > scroll_wrapper_top && element_bottom < scroll_wrapper_bottom) {
                            element.play();
                        } else {
                            element.pause();
                        }
                    }
                });
            },
            goBack(){
                this.$router.go(-1);
            },
            postComment(id, index) {
                if (this.user) {
                    let selected = this.posts[index];
                    if (selected.comment_text == "") {
                        return true;
                    } else {
                        const params = {
                            text: selected.comment_text,
                            target_id: id,
                            target_model: "media",
                            reply: null
                        };

                        let uri = "/comment/add";

                        this.axios.post(uri, params).then(response => {
                            if(this.user != this.posts[index].user_id) {
                                let noti_fb = firebase.database().ref('notifications/' + this.posts[index].user_id).push();
                                noti_fb.set({
                                    notifier_id: this.user.id,
                                    type: 'comment',
                                });
                            }
                            this.posts[index].comment_text = '';
                            this.posts[index].count_comment ++;
                            $("#app").removeClass('focus_comment');
                        });
                    }
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            showDescription(index) {
                this.posts[index].description_expanded = true;
                console.log(this.posts[index]);
            },
            hideFooter() {
                 $("#app").addClass('focus_comment');
            },
            showFooter(event, id, index) {
                if(event.relatedTarget && event.relatedTarget.id == 'btn_post_' + id) {
                    // this.postComment(id, index);
                } else {
                    $("#app").removeClass('focus_comment');
                }
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
    };
</script>
<style lang="scss" scoped>
    .slide_media {
        border-bottom: solid 1px gray;
        padding: 4px 0 10px;
    }
    .dropdown {
        &-button,
        &-menu {
            color: #efa720;
            a {
                color: currentColor;
                &:hover {
                    background-color: #f8f9fa !important;
                }
            }
        }
        &-menu {
            background: #000;
            min-width: 4rem;
        }
    }
    .media_description {
        margin: 0 10px;
    }

    .media_description img {
        width: 30px;
        height: 30px;
        border-radius: 50px;
        object-fit: cover;
    }

    .v_comment_box {
        display: flex;
        border: solid 1px white;
        padding: 5px;
        .profile-img {
            .userlogo {
                width: 30px;
                object-fit: cover;
            }
        }
        .commentbox {
            width: 100%;
            height: 31px;
            textarea {
                border: none;
                width: 100%;
                height: 36px;
                font-size: 16px;
                padding: 5px !important;
                resize: none;
                border: none !important;
                background: transparent !important;
            }
        }
        .button-group {
            button {
                background-color: #EFA720;
                border-radius: 0;
                padding: 3px 5px;
            }
        }
    }

    .menu-panel {
        position: absolute;
        bottom: 0;
        width: 100%;
        border-top: solid 1px black;
        background-color: rgba($color: #000000, $alpha: 0.7);
        padding: 0 8px;
        .menu-category {
            color: gray;
            margin-bottom: 0;
            font-size: 14px;
        }
        .item_name {
            color: #EFA720;
            margin-bottom: 0;
            font-size: 17px !important;
            line-height: 19px;
        }
        &.brand-menu {
            padding-bottom: 6px;
        }
        .price_data {
            span {
                sup {
                    padding-left: 3px;
                }
            }
        }
    }
    .weedgram-header {
        background-color: #000;
        color: #EFA720;
        position: fixed;
        transform: translate3d(0,0,0);
        width: 100%;
        padding: 3px 10px;
        border-bottom: solid 1px #FFF;
        z-index: 1;
        h4 {
            font-size: 20px;
            max-width: 90%;
            white-space: pre;
            overflow: auto;
            text-overflow: ellipsis;
        }
    }
    .header_show {
        padding-top: 37px;
    }
</style>
