<template>
    <div class="row postmedia original" ref="media_wrapper" id="portalprofile" style="margin-right: 15px;margin-bottom:100px;">
        <div class="col-md-8 posts" style="min-height: calc(100vh - 250px);">
            <div class="row">
                <div class="col-4 media_container" v-for="(item, index) of posts" :key="index">
                    <div v-if="mobile" class="media">
                        <router-link :to="{ name: 'Weedgram', hash:`#${index+1}`, params: {allpost : posts, start_index: index+1, page: page}}">
                            <img v-bind:src="serverUrl(item.url)" :alt="strain.name + ' marijuana'" v-if="item.type == 'image'" />
                            <video v-bind:src="serverUrl(item.url)" alt v-if="item.type == 'video'" disablePictureInPicture controlsList="nodownload" onclick="this.paused ? this.play() : this.pause();"></video>
                            <img class="video__tag__mobile" style="width:35px;" v-if="item.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                        </router-link>
                    </div>
                    <div v-else class="media" @click="changeimage(index)" @dblclick="likeMedia(item)">
                        <img :src="serverUrl(item.url)"  :alt="strain.name + ' marijuana'" v-if="item.type == 'image'" />                            
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
            <div class="col-md-4">
                <fixed-comment :media="selected" :allposts="posts" v-if="selected"></fixed-comment>
            </div>
        </client-only>
    </div>
</template>

<script>
    import FixedComment from "../FixedComment";
    import firebase from "../../Firebase";
    import axios from "axios";
    import { mapGetters } from "vuex";

    export default {
        props: ["strain", "mobile"],
        name: "StrainMedia",
        components: {
            FixedComment,
        },
        data() {
            return {
                posts: [],
                loading: true,
                comments: null,
                selected: null,
                loading: false,
                page: 1,
                last_page: 0,
            };
        },
        computed: mapGetters({
            user: 'auth/user',
            is_mobile: 'auth/is_mobile',
        }),
        mounted() {
            if (process.client) {
                this.scroll();
            }
        },
        created() {  },
        methods: {
            getallposts($state) {
                let uri = `/strain-media/${this.strain.slug}`;
                let params = {
                    target_id: this.strain.id,
                    model: "strain",
                    page: this.page,
                };
                this.loading = true;
                axios.post(uri, params).then(response => {
                    if (response.data.postdata.data.length) {
                        if(this.posts.length == 0) {
                            this.posts = response.data.postdata.data;
                            // $(".single__post__count").text(response.data.postdata.total);
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
            scroll() {
                var header = this.$refs.media_wrapper;
                var sticky = header.offsetTop;
                var sticky = sticky - 175;

                window.onscroll = () => {
                    if (window.pageYOffset > sticky) {
                        header.classList.remove("original");
                    } else {
                        header.classList.add("original");
                    }
                };
            },
            likeMedia(item) {
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
                            if(this.user.id != item.user_id) {                           
                                let noti_fb = firebase.database().ref('notifications/' + item.user_id).push();
                                noti_fb.set({
                                    notifier_id: this.user.id,
                                    type: 'like',
                                }); 
                            }
                        }
                    });
                } else {
                    $("#loginmodal").modal("show");
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
    #portalprofile {
        .media_container{
            .overlap {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                padding: 10px;
                background-color: rgba($color: #000000, $alpha: 0.5);
                visibility: hidden;
                div {
                    text-align: center;
                    padding-top: 40%;
                    color: #EFA720;
                    font-size: 18px;
                    img {
                        width: 22px;
                    }
                }
            }
            &:hover .overlap {
                visibility: visible;
            }
        }
    }
</style>
