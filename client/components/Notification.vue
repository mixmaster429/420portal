<template>
    <div id="notification_container">
        <div class="noti-header">
            <h1 class="text-420">
                <a @click.prevent="showFilter = !showFilter"><img src="/imgs/active.png" width="35"></a>
                Notification
            </h1>
        </div>
        <div class="noti-body">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-md-8 col-sm-9">
                    <div class="notification-board pb-2 px-2 px-md-4" id="notification_wrapper">
                        <div class="notification" v-for="(item, index) of notifications" :key="index" v-if="item.notifier">
                            <a :href="item.notifier.username" class="userlogo">
                                <img :src="serverUrl(item.notifier.profile_pic ? item.notifier.profile_pic.url : '/imgs/default_sm.png')" />
                            </a>                                
                            <div class="text notification-comment" v-if="item.type == 'comment'">
                                <a :href="item.notifier.username" class="username">{{item.notifier.name}}</a>
                                <p>Commented on your Media.</p>
                            </div>
                            <div class="text notification-reply" v-else-if="item.type == 'reply'">
                                <a :href="item.notifier.username" class="username">{{item.notifier.name}}</a>
                                <p>Replied to your <a class="notifiable" @click.prevent="goMedia(item)">Comment</a>.</p>
                            </div>
                            <div class="text notification-reply" v-else-if="item.type == 'reply_topic'">
                                <a :href="item.notifier.username" class="username">{{item.notifier.name}}</a>
                                <p>Replied to your <a class="notifiable" @click.prevent="goTopic(item)">Post</a>.</p>
                            </div>
                            <div class="text notification-like" v-else-if="item.type == 'like'">
                                <a :href="item.notifier.username" class="username">{{item.notifier.name}}</a>
                                <p>Likes your Media.</p>
                            </div>
                            <div class="text notification-like" v-else-if="item.type == 'like_comment'">
                                <a :href="item.notifier.username" class="username">{{item.notifier.name}}</a>
                                <p>Likes your <a class="notifiable" @click.prevent="goMedia(item)">Comment</a>.</p>
                            </div>
                            <div class="text notification-like" v-else-if="item.type == 'like_topic'">
                                <a :href="item.notifier.username" class="username">{{item.notifier.name}}</a>
                                <p>Likes your <a class="notifiable" @click.prevent="goTopic(item)">Post</a>.</p>
                            </div>
                            <div class="text notification-follow" v-else-if="auth_user.type == 'user' && item.type == 'follow'">
                                <a :href="item.notifier.username" class="username">{{item.notifier.name}}</a>
                                <p>Started following you. </p>
                            </div>
                            <div class="text notification-follow" v-else-if="item.type == 'follow_request'">
                                <a :href="item.notifier.username" class="username">{{item.notifier.name}}</a>
                                <p>Requested following you. </p>
                            </div>
                            
                            <a class="notifiable" style="color:#efa720" @click.prevent="follow(item.notifier_id, index)" v-show="!item.is_follower" v-if="auth_user.type == 'user' && item.type == 'follow'"><img src="/imgs/follow.png" class="btn-follow" /></a>
                            <a class="notifiable" style="color:#efa720" @click.prevent="acceptFollowRequest(item, index)" v-if="item.type == 'follow_request'">Accept</a>
                            <span class="notifiable" style="color:#efa720" v-if="item.type == 'like' || item.type == 'comment'">
                                <img :src="serverUrl(item.notifiable.url)" v-if="item.notifiable && item.notifiable.type == 'image'" class="img-media" />
                                <img :src="getPosterUrl(item.notifiable.url)" v-if="item.notifiable && item.notifiable.type == 'video'" class="img-media" />
                            </span>
                        </div>
                        <infinite-loading ref="infinite_loading" 
                            :distance="400" 
                            spinner="spiral" 
                            @infinite="getallnotifications"
                            force-use-infinite-wrapper="#notification_wrapper"
                        >                        
                            <div slot="no-more"></div>
                            <div slot="no-results"></div>
                        </infinite-loading>
                    </div>
                </div>
            </div>
        </div>
        <vs-popup id="email_notification_filter" type="border" title :active.sync="showFilter">
            <div class="email_notification_filter">
                <h3 class="text-420 text-center">Email Notification Filter</h3>
                <div>
                    <form action="">
                        <div class="custom-check d-flex">
                            <div class="round mr-1 pt-1">
                                <input type="checkbox" value="comment_reply" id="filter_comment_reply" name="email_notification_filter" v-model="email_notification_filter" />
                                <label for="filter_comment_reply"></label>
                            </div>
                            <label class="filter-label" for="filter_comment_reply">Comment / Reply</label>
                        </div>
                        <div class="custom-check d-flex">
                            <div class="round mr-1 pt-1">
                                <input type="checkbox" value="like" id="filter_like" name="email_notification_filter" v-model="email_notification_filter" />
                                <label for="filter_like"></label>
                            </div>
                            <label class="filter-label" for="filter_like">Like</label>
                        </div>
                        <div class="custom-check d-flex">
                            <div class="round mr-1 pt-1">
                                <input type="checkbox" value="message" id="filter_message" name="email_notification_filter" v-model="email_notification_filter" />
                                <label for="filter_message"></label>
                            </div>
                            <label class="filter-label" for="filter_message">Message</label>
                        </div>
                        <div class="custom-check d-flex">
                            <div class="round mr-1 pt-1">
                                <input type="checkbox" value="follow" id="filter_follow" name="email_notification_filter" v-model="email_notification_filter" />
                                <label for="filter_follow"></label>
                            </div>
                            <label class="filter-label" for="filter_follow">Follow</label>
                        </div>
                    </form>
                </div>
            </div>
        </vs-popup>
    </div>
</template>

<script>
    import firebase from "../Firebase";
    import { mapGetters } from "vuex";
    export default {
        name : 'notificaiton',
        components : {
            
        },
        computed: mapGetters({
            auth_user: 'auth/user',
        }),
        watch : {
            email_notification_filter : function(new_val, old_val) {
                this.axios.post('/email_notification_filter/save', {value : new_val}).then(response => {
                    // console.log(response.data);
                });
            }
        },
        data(){
            return {
                notifications : [],
                page : 1,
                loading : false,
                is_last : false,
                showFilter : false,
                email_notification_filter : [],
            }
        },
        methods : {
            getallnotifications($state) {
                let url = '/notification/get_all';
                let params = {page : this.page};
                this.axios.post(url, params).then(response => {
                    if(response.data.data.length) {
                        if(this.notifications.length == 0) {
                            this.notifications = response.data.data;
                        } else {
                            this.notifications = _.concat(this.notifications, response.data.data);
                        }
                        this.page++ ;
                        this.loading = false;
                        $state.loaded();
                    } else {
                        $state.complete();
                        this.is_last = true;
                    }
                    this.$store.dispatch('auth/getUnreadNotification');
                });
            },

            goMedia(item){
                // localStorage.setItem("selected_media_id", item.notifiable.id);
                // localStorage.setItem("selected_media_referece_type", item.type);
                window.location.href = '/media/' + item.notifiable_id;
            },
            follow(id, index) {
                let uri = '/user/follow';
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

                    this.notifications[index].is_follower = 1;

                });
            },

            acceptFollowRequest(item, index) {
                let uri = '/user/accept_follow_request';
                let params = {
                    notification_id: item.id,
                };
                this.axios.post(uri, params).then(response => {
                    if(response.data.status == 200) {                     
                        this.notifications.splice(index, 1);
                        // let noti_fb = firebase.database().ref('notifications/' + id).push();
                        // noti_fb.set({
                        //     notifier_id: this.auth_user.id,
                        //     type: 'follow',
                        // });   
                    }
                });
            },

            getEmailNotificationFilter(){
                let url = '/email_notification_filter/get_value';
                this.axios.get(url).then(response => {
                    this.email_notification_filter = response.data;
                });
            },
            replacetitle(title)
            {
                let strtitle = title.toLowerCase();
                let returnval = "";
                for (let index = 0; index < strtitle.length; index++) {
                    if(strtitle[index] == " ") {
                        returnval += "-";
                    } else {
                        returnval += strtitle[index];
                    }
                }
                return returnval;
            },
            goTopic(item){
                let url = '';
                if(item.notifiable.title) {
                    url = '/marijuana-forums/' + item.notifiable.slug + '/' + item.notifiable.id;
                } else {
                    url = '/marijuana-forums/' + item.notifiable.get_m_parent.slug + '/' + item.notifiable.get_m_parent.id;
                }                
                window.location.href = url;
            },
            serverUrl(item) {
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + 'imgs/default.png';
                }
            },
            getPosterUrl(url) {
                var newUrl = url.replace("/video/", "/image/");
                var pointPos = newUrl.lastIndexOf(".");
                newUrl = newUrl.substring(0, pointPos) + ".jpg";
                return process.env.serverUrl + newUrl;
            }

        },
        mounted(){
            this.getEmailNotificationFilter();
        }
    }
</script>

<style lang="scss">
    .noti-header {
        padding: 20px 0;
        h1 {
            text-align: center;
            img {
                width: 32px;
                margin-top: -8px;
                cursor: pointer;
            }
        }
    }
    .noti-body {
        .notification-board {
            border: solid 1px white;
            border-radius: 8px;            
            max-height: 450px;
            overflow-y: auto;
            .notification {
                font-size: 16px;
                padding: 0;
                display: flex;
                align-items: center;
                border-bottom: solid 1px gray;
                .text {
                    color: white;
                    padding-left: 5px;
                    flex-grow: 1;
                    p {
                        margin: 0;
                    }
                }
                .userlogo {
                    img {
                        width: 40px;
                        height: 40px;
                        border-radius: 100px;
                        object-fit: cover;
                    }
                }
                .username {
                    color: #EFA720;
                    cursor: pointer;
                    text-decoration: none;
                    display: block;
                    max-width: 250px;
                    overflow: auto;
                    white-space: pre;
                    text-overflow: ellipsis;
                }
                .notifiable {
                    color: blue;
                    cursor: pointer;
                    .btn-follow {
                        height: 25px;
                    }
                    .img-media {
                        height: 40px;
                        width: 40px;
                        object-fit: cover;
                    }
                }
            }
        }  
        @media only screen and (max-width: 800px) {
            .notification-board {
                border: none;
                .notification {
                    font-size: 16px;
                }
            }
        } 
    }
    @media only screen and (max-width: 800px) {
        .noti-header{
            h1 {
                font-size: 26px;
                padding-bottom: 15px;
                border-bottom: solid 1px white;
                img {
                    width: 25px;
                }
            }
        }        
    } 
    #email_notification_filter {
        .filter-label {
            margin-left: 10px;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        .round {
        position: relative;
    }
    .round label {
        background-color: #fff;
        border: 1px solid #ccc;
        cursor: pointer;
        border-radius: 50%;
        height: 20px;
        left: 0;
        position: absolute;
        top: 0;
        width: 20px;
        transition: all .1s linear;
    }
    .round label:after {
        border: 2px solid #fff;
        border-top: none;
        border-right: none;
        content: "";
        height: 6px;
        left: 3px;
        opacity: 0;
        position: absolute;
        top: 5px;
        transform: rotate(-45deg);
        width: 12px;
    }
    .round input[type="checkbox"] {
        visibility: hidden;
    }
    .round input[type="checkbox"]:checked+label {
        background-color: #efa720;
        border-color: #efa720;
    }
    .round input[type="checkbox"]:checked+label:after {
        opacity: 1;
    }
    }
</style>