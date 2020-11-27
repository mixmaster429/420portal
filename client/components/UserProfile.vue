<template>
    <div class="row pt-3" v-if="user" :class="{'pb-5': $device.isMobile}" id="userprofile">
        <div class="container">
            <div v-if="userdata" class="profile-data">
                <div class="profile_header row">
                    <div class="col-4 text-left mt-2 mt-md-0">
                        <div class="pf-logo">
                            <img :src="serverUrl(userdata.profile_pic ? userdata.profile_pic.url : '/imgs/default.png')" alt />
                        </div>
                    </div>
                    
                    <div class="pf-details col-8 text-center">
                        <div class="info-container d-flex text-center justify-content-around">
                            <div class="info_posts">
                                <span>{{posts_count}}</span>
                                <p class="mb-0">Posts</p>
                            </div>
                            <div class="info_followers">
                                <span @click="openfollow(userdata.id, 'followers')">{{this.followers}}</span>
                                <p class="mb-0" @click="openfollow(userdata.id, 'followers')">Followers</p>
                                <!-- Follow / Message -->
                                <div class="portal__extra mt-0 mt-md-2">
                                    <img src="~assets/imgs/follow-icon.png" alt class="pf-follow" @click="is_private ? followRequest(userdata.id, 'normal', 0, 1) : follow(userdata.id, 'normal', 0, 1)" v-show="logged_user_id != userdata.id" v-if="isfollower == 0" />
                                    <img src="~assets/imgs/unfollow.png" alt class="pf-unfollow" @click="unfollow(userdata.id, 'normal', 0, 1)" v-show="logged_user_id != userdata.id" v-if="isfollower == 1" />
                                    <button class="btn text-white pf-requested pf-follow" style="border:solid 1px #FFF;" v-show="logged_user_id != userdata.id" v-if="isfollower == 2">Requested</button>
                                    <div class="message btn-message" @click="openchat()" v-if="userdata.messenger && logged_user_id != userdata.id && !$device.isMobile && !isblockuser && !is_private">
                                        <img src="~assets/imgs/messenger-mobile.png" />
                                        <p class="my-0">Message</p> 
                                    </div>
                                    <router-link :to="{ name: 'mobile_chatbox', params: {userdetail: userdata}}" v-if="$device.isMobile">
                                        <div class="message btn-message" v-if="userdata.messenger && logged_user_id != userdata.id && !isblockuser && !is_private">
                                            <img src="~assets/imgs/messenger-mobile.png" />
                                            <p class="my-0">Message</p>
                                        </div>
                                    </router-link>
                                </div>
                            </div>
                            <div class="info_following" @click="openfollow(userdata.id, 'following')">
                                <span>{{this.following}}</span>
                                <p class="mb-0">Following</p>
                            </div>
                        </div>
                        <vs-popup class="userprofile__popup" title="Follower & Following" :active.sync="popupActive">
                            <div>
                                <Tabs :tabs="tabs" :currentTab="currentTab" :wrapper-class="'default-tabs'" :tab-class="'default-tabs__item'" :tab-active-class="'default-tabs__item_active'" :line-class="'default-tabs__active-line'" @onClick="handleClick" />
                                <div class="tabs-content">
                                    <div v-if="currentTab === 'followers'">
                                        <ul>
                                            <li v-for="(followerobj, index) of followerobjs" :key="`followerobj-${index}`">
                                                <div class="follower-logo" @click="gotouserprofile(followerobj.username)">
                                                    <img :src="serverUrl(followerobj.url)" />
                                                </div>
                                                <div class="username" @click="gotouserprofile(followerobj.username)">
                                                    <p>{{followerobj.name}}</p>
                                                </div>
                                                <div class="action">
                                                    <img src="~assets/imgs/follow-icon.png" v-if="followerobj.isfollower == 0" @click="follow(followerobj.id, 'tab', followerobj.isportal, 1)" />
                                                    <img src="~assets/imgs/unfollow.png" v-else @click="unfollow(followerobj.id, 'tab', followerobj.isportal, 1)" />
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div v-if="currentTab === 'following'">
                                        <ul>
                                            <li v-for="(followingobj, index) of followingobjs" :key="`followerobj-${index}`">
                                                <div class="follower-logo" @click="gotouserprofile(followingobj.username)">
                                                    <img :src="serverUrl(followingobj.url)" />
                                                </div>
                                                <div class="username" @click="gotouserprofile(followingobj.username)">
                                                    <p>{{followingobj.name}}</p>
                                                    <img class="store-type" src="~assets/imgs/dispensary.png" alt v-if="followingobj.store_type == 1 || followingobj.store_type == 3" />
                                                    <img class="store-type" src="~assets/imgs/delivery.png" alt v-if="followingobj.store_type == 2 || followingobj.store_type == 3" />
                                                </div>
                                                <div class="action">
                                                    <img src="~assets/imgs/follow-icon.png" v-if="followingobj.isfollower == 0" @click="follow(followingobj.id, 'tab', followingobj.isportal, 0)" />
                                                    <img src="~assets/imgs/unfollow.png" v-else @click="unfollow(followingobj.id, 'tab', followingobj.isportal, 0)" />
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </vs-popup>                        
                    </div>
                </div>
                <div class="profile__information mt-3 mb-5 d-flex flex-column">
                    <div class="profile__title d-flex align-items-center">
                        <h1 class="pf_username mr-2 mb-0 text-white">{{userdata.username}}</h1>
                        <div class="pf-dashboard" v-if="auth_user && (userdata.id == auth_user.id || auth_user.id == 1)">
                            <router-link v-if="$device.isMobile" :to="{ name: 'edit_profile', params: {editData: userdata.id, mainData: userdata}}">
                                <img src="~assets/imgs/edit.png" width="28px" alt />
                            </router-link>
                            <a v-else @click.prevent="openEditPopup = true" href="#" class="editprofilee">
                                <img src="~assets/imgs/edit.png" width="28px" alt />
                            </a>
                        </div>
                    </div>
                    <div class="is_private d-flex align-items-center" v-if="is_private">
                        <div class="lock-img">
                            <img src="~assets/imgs/lock.png" >
                        </div>
                        <div class="ml-2">
                            <p class="my-0 text-420" style="font-size:18px;">This Account is Private</p>
                            <p class="my-0">Follow this account to see their media.</p>
                        </div>
                    </div>
                    <span class="mt-2" v-if="userdata.description" v-show="!is_private">{{userdata.description}}</span>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row postmedia original" id="portaMedia" style="margin-right: 15px; margin: 0 auto;">
                <div class="col-md-8 posts" style="min-height: calc(100vh - 395px);" v-if="!is_private">
                    <div class="row">
                        <div class="col-4 media_container" v-for="(item, index) of posts" :key="index">
                            <div v-if="$device.isMobile" class="media">
                                <client-only>
                                    <router-link :to="{ 
                                            name: 'weedgram', 
                                            hash:`#${index+1}`, 
                                            params: {allpost: posts, start_index: index+1, page: page, model: 'user', username: userdata.username, currentId : userdata.id}
                                        }">
                                        <img v-bind:src="serverUrl(item.url)" alt v-if="item.type == 'image'" />
                                        <video v-bind:src="serverUrl(item.url)" alt v-if="item.type == 'video'" disablePictureInPicture controlsList="nodownload" onclick="this.paused ? this.play() : this.pause();"></video>
                                        <img class="video__tag__mobile" style="width:25px;" v-if="item.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                                    </router-link>
                                </client-only>
                            </div>
                            <div v-else class="media" @click="changeimage(index)" @dblclick="likeMedia(item)">
                                <img :src="serverUrl(item.url)" alt v-if="item.type == 'image'" />
                                <video v-bind:src="serverUrl(item.url)" alt v-if="item.type == 'video'" disablePictureInPicture controlsList="nodownload" onclick="this.paused ? this.play() : this.pause();"></video>
                                <img class="video__tag__mobile" style="width:35px;" v-if="item.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                            </div>
                        </div>
                    </div>
                    <infinite-loading 
                        :distance="300" 
                        spinner="spiral" 
                        @infinite="getallposts"
                        force-use-infinite-wrapper="body"
                    ><div slot="no-more"></div></infinite-loading>
                </div>
                <client-only>
                    <div v-if="!$device.isMobile && !is_private" class="col-md-4">
                        <fixed-comment v-if="selected" :media="selected" :allposts="posts"></fixed-comment>
                    </div>
                </client-only>
            </div>
        </div>
        <vs-popup v-if="user.id == logged_user_id || logged_user_id == 1" class="strains__popup media__add" type="border" title="Edit User" :active.sync="openEditPopup">
            <profile-form :editData="user.id" :mainData="user" mode="edit" v-if="!$device.isMobile"></profile-form>
        </vs-popup>
        <page-footer v-if="$device.isDesktop"></page-footer>
    </div>
</template>

<script>
    import FixedComment from "./FixedComment";
    import ProfileForm from "./ProfileForm";
    import PageFooter from "./PageFooter";
    import Tabs from 'vue-tabs-with-active-line';
    import firebase from "../Firebase";
    import { mapGetters } from "vuex";

    export default {
        name: "UserProfile",
        components: {
            FixedComment,
            ProfileForm,
            PageFooter,
            Tabs,
        },
        props: ["user"],
        computed: mapGetters({
            auth_user: 'auth/user',
        }),
        data() {
            return {
                posts: [],
                userdata: null,
                selected: null,
                comments: null,
                loading: true,
                logged_user_id: 0,
                followers: 0,
                following: 0,
                isfollower: 0,
                followerobjs: [],
                followingobjs: [],
                isblockuser: false,
                popupActive: false,
                tabs: [{
                    title: 'Followers',
                    value: 'followers'
                }, {
                    title: 'Following',
                    value: 'following'
                }],
                currentTab: 'followers',
                loading: false,
                page: 1,
                posts_count : 0,
                is_private: false,
                openEditPopup: false,
            };
        },
        mounted() {
            if(process.client) {
                this.scroll();
            }
            $('#user_follower_modal').modal('hide');
            this.getfollow();
            this.getIsFollower();
            if (this.auth_user) {
                this.logged_user_id = this.auth_user.id
            }
            setTimeout(function(){document.documentElement.scrollTop = 0;}, 2000);
        },
        methods: {
            handleClick(newTab) {
                this.currentTab = newTab;
            },
            getallposts($state) {
                let uri = "/profile/getallposts";
                let params = {
                    currentId: this.user.id,
                    model: 'user',
                    page: this.page,
                };
                this.loading = true;
                this.axios.post(uri, params).then(response => {
                    this.userdata = response.data.userdata;
                    this.checkPrivate();
                    if(this.auth_user) {
                        this.checkblockuser();
                    }
                    
                    if (response.data.postdata.data.length) {
                        this.posts_count = response.data.postdata.total;
                        if(this.posts.length == 0) {
                            this.posts = response.data.postdata.data;
                            this.selected = this.posts[0];
                        } else {
                            this.posts = _.concat(this.posts, response.data.postdata.data);
                        }
                        this.page++ ;
                        this.loading = false;
                        $state.loaded();
                    } else {
                        this.$parent.loading = true;
                        $state.complete();
                    }
                });
            },
            getfollow() {
                let uri = "/profile/getfollow";
                let params = {
                    user_id: this.user.id
                }
                this.axios.post(uri, params).then(response => {
                    this.followers = response.data.followers;
                    this.following = response.data.following;
                });
            },
            getIsFollower() {
                if (this.auth_user) {
                    let uri = "/profile/getisfollower";
                    let params = {
                        user_id: this.auth_user.id,
                        follower_user_id: this.user.id
                    }
                    this.axios.post(uri, params).then(response => {
                        this.isfollower = response.data.status;
                        this.checkPrivate();
                    });
                }
            },
            openfollow(id, currentTab) {
                let uri = "/user/allfollows";
                let params = {
                    user_id: id
                }
                this.axios.post(uri, params).then(response => {
                    this.followerobjs = response.data.followers;
                    this.followingobjs = response.data.followings;
                    this.currentTab = currentTab;
                    this.popupActive = true;
                });
            },
            gotouserprofile(username) {
                let url = "/" + username;
                window.location.href = url;
            },
            changeimage(index) {
                this.selected = this.posts[index];
            },
            openchat() {
                if (this.auth_user) {
                    let user = {
                        from: this.auth_user.id,
                        to: this.userdata.id,
                        name: this.userdata.name,
                        username: this.userdata.username,
                        url: this.userdata.profile_pic ? this.userdata.profile_pic.url : '/imgs/default_sm.png',
                        type: this.userdata.type,
                        store_type: this.userdata.store_type,
                    };

                    this.$parent.pushchatuserlist(user);
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            follow(id, type, isportal, isfollower) {
                if (this.auth_user) {
                    if(this.auth_user.type != 'user') return false;
                    if (this.auth_user.id == id && isportal == 0)
                        return false;
                    let uri = '/user/follow';
                    if (isportal)
                        uri = '/company/follow';
                    let params = {
                        user_id: this.auth_user.id,
                        follower_id: id
                    };
                    this.axios.post(uri, params).then(response => {
                        let noti_fb = firebase.database().ref('notifications/' + id).push();
                        noti_fb.set({
                            notifier_id: this.auth_user.id,
                            type: 'follow',
                        });
                        if (type == 'normal') {
                            this.followers++;
                            this.isfollower = 1;
                            this.checkPrivate();
                        } else {
                            if (isfollower) {
                                for (let i = 0; i < this.followerobjs.length; i++) {
                                    if (this.followerobjs[i]['id'] === id)
                                        this.followerobjs[i]['isfollower'] = !this.followerobjs[i]['isfollower'];
                                }
                            } else {
                                for (let i = 0; i < this.followingobjs.length; i++) {
                                    if (isportal) {
                                        if (this.followingobjs[i]['id'] === id && this.followingobjs[i]['isportal'] == 1) {
                                            this.followingobjs[i]['isfollower'] = !this.followingobjs[i]['isfollower'];
                                        }
                                    } else {
                                        if (this.followingobjs[i]['id'] === id && this.followingobjs[i]['isportal'] == 0) {
                                            this.followingobjs[i]['isfollower'] = !this.followingobjs[i]['isfollower'];
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },
            unfollow(id, type, isportal, isfollower) {
                if (this.auth_user) {
                    if(this.auth_user.type != 'user') return false;
                    if (this.auth_user.id == id && isportal == 0)
                        return false;
                    let uri = '/user/unfollow';
                    if (isportal)
                        uri = '/company/unfollow';
                    let params = {
                        user_id: this.auth_user.id,
                        follower_id: id
                    };
                    this.axios.post(uri, params).then(response => {
                        if (type == 'normal') {
                            this.followers--;
                            this.isfollower = 0;
                            this.checkPrivate();
                        } else {
                            if (isfollower) {
                                for (let i = 0; i < this.followerobjs.length; i++) {
                                    if (this.followerobjs[i]['id'] === id)
                                        this.followerobjs[i]['isfollower'] = !this.followerobjs[i]['isfollower'];
                                }
                            } else {
                                for (let i = 0; i < this.followingobjs.length; i++) {
                                    if (isportal) {
                                        if (this.followingobjs[i]['id'] === id && this.followingobjs[i]['isportal'] == 1) {
                                            this.followingobjs[i]['isfollower'] = !this.followingobjs[i]['isfollower'];
                                        }
                                    } else {
                                        if (this.followingobjs[i]['id'] === id && this.followingobjs[i]['isportal'] == 0) {
                                            this.followingobjs[i]['isfollower'] = !this.followingobjs[i]['isfollower'];
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },
            followRequest(id, type, isportal, isfollower) {
                if(this.auth_user) {
                    if(this.auth_user.type != 'user') return false;
                    if (this.auth_user.id == id && isportal == 0)
                        return false;
                    let uri = '/user/follow_request';
                    let params = {
                        user_id: this.auth_user.id,
                        follower_id: id
                    };
                    this.axios.post(uri, params).then(response => {
                        if(response.data.status == 2) {
                            let noti_fb = firebase.database().ref('notifications/' + id).push();
                            noti_fb.set({
                                notifier_id: this.auth_user.id,
                                type: 'follow_request',
                            });
                            if (type == 'normal') {
                                this.isfollower = 2;
                            } else {
                                this.followingobjs.find(obj => obj.id === id).isfollower = 2;
                            }
                        }
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },
            checkblockuser() {
                let url = "/user/isblockuser";

                let params = {
                    receiver: this.userdata.id,
                    sender: this.auth_user.id,
                    type: 'user',
                };

                return this.axios.post(url, params).then((response) => {
                    // 1: block, 0: unblock
                    this.isblockuser = response.data;
                });
            },
            scroll() {
                var header = document.getElementById("portaMedia");
                var sticky = header.offsetTop;
                var sticky = sticky + 180;
                window.onscroll = () => {
                    if (window.pageYOffset > sticky) {
                        header.classList.remove("original");
                    } else {
                        header.classList.add("original");
                    }
                };
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
            checkPrivate() {
                if(this.userdata && this.userdata.is_private) {
                    if(!this.auth_user) {
                        this.is_private = true;
                    } else {
                        if(this.auth_user.id == this.userdata.id) {
                            this.is_private = false;
                        } else {
                            this.is_private = this.isfollower == 1 ? false : true;
                        }
                    }
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
        },
    };
</script>

<style lang="scss" scoped>
    #userpfofile {
        .profile_header {
            .pf-details {
                .pf-follow {
                    margin-right: 43px;
                    margin-top: 0px;
                    height: 100%;
                    cursor: pointer;
                    @media (max-width: 600px) {
                        margin-right: 32px;
                    }
                }
                .pf-unfollow {
                    width: 40px;
                    margin-right: 25px;
                    margin-top: 0px;
                    height: 85%;
                    cursor: pointer;
                }
                
                .portal__extra {
                    text-align: center;
                    font-size: .9rem;
                    .btn-message {
                        margin-top: 10px;
                        margin-right: 43px;
                        cursor: pointer;
                        @media (max-width: 600px) {
                            margin-top: 1px;
                            margin-right: 32px;
                            p {
                                margin-top: -3px !important;
                            }
                        }
                        img {
                            height: 22px;
                            width: auto;
                        }                        
                    }
                    .pf-requested {
                        background-color: #000;
                        border-color: #FFF;
                        color: #FFF;
                        &:hover, &:focus {
                            background-color: #000;
                        }
                    }
                }
            }            
        }        
    }
</style>