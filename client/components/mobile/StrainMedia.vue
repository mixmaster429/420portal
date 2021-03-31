<template>
    <div class="row postmedia m-0 p-0" style="margin-bottom:60px !important;">
        <div class="col-12 text-center posts">
            <div class="container">
                <div class="row">
                    <div class="col-4 media_container" v-for="(item, index) in posts" :key="index">
                        <div class="media">
                            <router-link :to="{ name: 'weedgram', hash:`#${index+1}`, params: {allpost : posts, start_index: index+1, model:'strain', page: page, currentId: strain.id, url: `/strain-media/${strain.slug}`}}">
                                <img v-bind:src="serverUrl(item.url)" :alt="strain.name + ' marijuana'" v-if="item.type == 'image'" />
                                <img v-bind:src="getPosterUrl(item.url)" alt v-if="item.type == 'video' && $device.isIos" />
                                <video v-bind:src="serverUrl(item.url)" alt v-if="item.type == 'video' && $device.isAndroid"></video>
                                <img class="video__tag__mobile" v-if="item.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt />
                            </router-link>
                        </div>
                    </div>
                </div>
                <infinite-loading 
                    :distance="200" 
                    spinner="spiral" 
                    @infinite="getallposts"
                    force-use-infinite-wrapper="body"
                ><div slot="no-more"></div></infinite-loading>
            </div>
        </div>
    </div>
</template>

<script>
    import firebase from "../../Firebase";
    import { mapGetters } from "vuex";
    export default {
        props: ["strain"],
        name: "StrainMedia",
        components: {
            
        },
        data() {
            return {
                posts: [],
                loading: true,
                comments: null,
                selected: null,
                loading: false,
                page: 1,
            };
        },
        mounted() {

        },
        created() {},
        methods: {
            getallposts($state) {
                let uri = `/strain-media/${this.strain.slug}`;
                let params = {
                    target_id: this.strain.id,
                    model: "strain",
                    page: this.page,
                };
                this.loading = true;
                this.axios.post(uri, params).then(response => {
                    if (response.data.postdata.data.length) {
                        if(this.posts.length == 0) {
                            this.posts = response.data.postdata.data;
                            $(".single__post__count").text(response.data.postdata.total);
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
    };
</script>