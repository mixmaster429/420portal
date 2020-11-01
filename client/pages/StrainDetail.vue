<template>
    <div class="row strains">
        <strain-nav></strain-nav>
        <div class="col-12">
            <div class="container" id="strain_show_page" v-if="strain_data">
                <div class="single__content d-flex text-center align-items-center mt-4">
                    <div class="single__img">
                        <div class="container_img">
                            <img class="img-fluid" :src="strain_data.main_media ? serverUrl(strain_data.main_media.url) : '~assets/imgs/strains/blue.jpg'" :alt="strain_data.strain.name + 'marijuana'">
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="strain-information d-flex justify-content-center">
                            <div class="single__post__container">
                                <div class="single__post__count">{{strain_data.posts_count}}</div>
                                <div class="single__post__label">Posts</div>
                            </div>
                            <div class="single__follow__container" style="cursor: pointer;">
                                <div class="single__follow__count btn-open-modal" id="followers_count">{{strain_data.followers_count}}</div>
                                <div class="single__follow__label btn-open-modal">Followers</div>
                            </div>                    
                        </div>
                        <div class="strain-information text-center strain-action">
                            <img src="~assets/imgs/follow-icon.png" alt class="btn-follow st-follow" :class="{'d-none': strain_data.is_follower == 1}" />
                            <img src="~assets/imgs/unfollow.png" alt class="btn-follow st-unfollow" :class="{'d-none': strain_data.is_follower != 1}" />
                            <div class="single__comment mt-1 mx-auto" style="width: 60px;" data-toggle="modal" data-target="#commentModal">
                                <fa :icon="['far', 'comment']" fixed-width class="single__icon" />
                                <p id="count_comments">0</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single__content mb-5">
                    <h1 class="single__name mt-2 text-white">
                        {{ strain_data.strain.name }}                  
                        <a href="javascript:;" data-toggle="modal" data-target="#editModal" v-if="user && user.name == '420portal'">
                            <img class="portal__edit" src="~assets/imgs/edit.png" width="28" alt />
                        </a>                        
                    </h1>
                    <div class="single__category">
                        {{ strain_data.strain.category.name }}
                    </div>
                </div>
                <h2 class="text-center my-4 category__header">{{ strain_data.strain.name }} Marijuana Strain</h2>

                <edit-description class="category__content" type="strain" :strain="strain_data.strain" :auth="user"></edit-description>

                <div class="strains__extra mt-3">
                    <ul class="nav extra__list justify-content-center mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-flowers-tab" data-toggle="pill" href="#pills-flowers" role="tab" aria-controls="pills-flowers" aria-selected="true">Flowers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-concentrates-tab" data-toggle="pill" href="#pills-concentrates" role="tab" aria-controls="pills-concentrates" aria-selected="false">Concentrates</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-vape-pens-tab" data-toggle="pill" href="#pills-vape-pens" role="tab" aria-controls="pills-vape-pens" aria-selected="false">Vape Pens</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-clones-tab" data-toggle="pill" href="#pills-clones" role="tab" aria-controls="pills-clones" aria-selected="false">Clones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-seeds-tab" data-toggle="pill" href="#pills-seeds" role="tab" aria-controls="pills-seeds" aria-selected="false">Seeds</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-pre-roll-tab" data-toggle="pill" href="#pills-pre-roll" role="tab" aria-controls="pills-pre-roll" aria-selected="false">Pre Roll</a>
                        </li>
                    </ul>
                    <client-only>
                    <div class="tab-content extra__content" style="border: none" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-flowers" role="tabpanel" aria-labelledby="pills-flowers-tab">
                            Flowers
                            <strain-menu :strain="strain_data.strain" category="flowers" type="2"></strain-menu>
                        </div>
                        <div class="tab-pane fade" id="pills-concentrates" role="tabpanel" aria-labelledby="pills-concentrates-tab">
                            Concentrates
                            <strain-menu :strain="strain_data.strain" category="concentrates" type="1"></strain-menu>
                        </div>
                        <div class="tab-pane fade" id="pills-vape-pens" role="tabpanel" aria-labelledby="pills-vape-pens-tab">
                            Vape Pens
                            <strain-menu :strain="strain_data.strain" category="vape-pens" type="1"></strain-menu>
                        </div>
                        <div class="tab-pane fade" id="pills-clones" role="tabpanel" aria-labelledby="pills-clones-tab">
                            Clones
                            <strain-menu :strain="strain_data.strain" category="clones" type="0"></strain-menu>
                        </div>
                        <div class="tab-pane fade" id="pills-seeds" role="tabpanel" aria-labelledby="pills-seeds-tab">
                            Seeds
                            <strain-menu :strain="strain_data.strain" category="seeds" type="0"></strain-menu>
                        </div>
                        <div class="tab-pane fade" id="pills-pre-roll" role="tabpanel" aria-labelledby="pills-pre-roll-tab">
                            Pre Roll
                            <strain-menu :strain="strain_data.strain" category="pre-roll" type="0"></strain-menu>
                        </div>
                    </div>
                    </client-only>
                </div>

                <h2 class="text-center my-4 category__header">{{ strain_data.strain.name }} Weed Pictures & Videos</h2>
            </div>
            <template v-if="strain_data.strain">                
                <keep-alive v-if="is_mobile">
                    <router-view></router-view>
                    <strain-mobile :strain="strain_data.strain"></strain-mobile>
                </keep-alive>
                <strain-media :strain="strain_data.strain" v-else></strain-media>
            </template>
        </div>
    </div>
</template>
<script>
    import { mapGetters } from "vuex";    
    import axios from 'axios';
    import _ from 'lodash';
    import StrainNav from "~/components/strain/StrainNav";
    import StrainMenu from "~/components/strain/StrainMenu";
    import StrainMedia from "~/components/strain/StrainMedia";
    import EditDescription from "~/components/strain/EditDescription";

    export default {
        components: {
            StrainNav,
            StrainMenu,
            StrainMedia,
            EditDescription,
        },
        head () {
            return { 
                title: this.$route.params.strain,
            }
        },
        data() {
            return {
                slug: this.$route.params.strain,
            }
        },
        watch: {

        },
        serverPrefetch () {
            // return the Promise from the action
            // so that the component waits before rendering
            return this.fetchStrainData()
        },
        computed: {
            ...mapGetters({
                user: 'auth/user',
                is_mobile: 'auth/is_mobile',
                strain_data: 'strain/data',
            }),
        },
        mounted() {
            if (!this.strain_data) {
                this.fetchStrainData();
                console.log('fetched strain data');
            }
        },
        methods: {
            fetchStrainData () {
                return this.$store.dispatch('strain/fetchStrainData', this.$route.params.strain)
            },
            getStrain() {
                let url = `/get_strain/${this.slug}`;
                axios.get(url).then(response => {
                    this.strain = response.data.strain;
                    this.posts_count = response.data.posts_count;
                    this.followers_count = response.data.followers_count;
                    this.is_follower = response.data.is_follower;
                    this.main_media = response.data.main_media;
                    this.tagged_media = response.data.taggedMedia;
                })
            },
            serverUrl(item) {
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
    #pills-tabContent table tr td{
        color: #EFA720;
    }
    #pills-tabContent table tr td sup {
        color: white;
    }
    #commentModal .modal-header {
        color: #EFA720;
        padding: 6px 0.8rem;
        background: #000;
        border-radius: 0;
        -webkit-box-pack: start;
        justify-content: flex-start;
        -webkit-box-align: center;
        align-items: center;
        border-bottom: solid 1px white;
    }
    #commentModal .modal-header .close {
        position: initial;
        margin-left: 0;
        padding: 0;
        color: #EFA720;
        text-shadow: none;
    }
    #commentModal .modal-dialog {
        margin: 80px auto;
    }
    @media (max-width: 600px) {
        #commentModal .modal-dialog {
            margin: 0;
        }
    }
    .image-panel {
        position: absolute;
        display: none;
        padding: 8px;
        border: solid 1px white;
        border-radius: 5px;
        background-color: #000;
        z-index: 1000;
    }
    .image-panel .menu-media img {
        width: 200px;
        height: 200px;
        object-fit: cover;
    }
    .image-panel .menu-description {
        color: gray;
        max-width: 300px;
    }
    .container_img{
        width: 100%;
        height: 100%;
        position: relative;
        overflow: hidden;
        border-radius: 50%;
    }
    .container_img img {
        display: inline;
        margin: 0 auto;
        min-height: 100%;
        width: auto;
    }
    #editModal {
        color: #FFF;
        font-size: 16px;
    }
</style>