<template>
    <div class="row postmedia" :class="{'pb-5': $device.isMobile}" id="portalprofile" v-if="portal_detail">
        <div class="container-fluid m-0 p-0" v-if="portal_detail.type == 'company'">
            <client-only>
                <div class="header_map">
                    <GmapMap
                        ref="mapRef"
                        :options="{ mapTypeControl: false, streetViewControl: false }"
                        :center="current_position"
                        :zoom="11"
                        map-type-id="roadmap"
                        style="width: 100%; height: 100%"
                    >
                        <GmapMarker
                            :position="{lat: Number(portal_detail.latitude), lng: Number(portal_detail.longitude)}"
                            :icon="markerOptions[portal_detail.store_type-1]"
                        />
                    </GmapMap>
                </div>
            </client-only>
        </div>
        <div class="container">
            <div class="portal_details row mb-2">
                <div class="col-4 col-md-2 text-left">
                    <div class="logo_image">
                        <img :src="serverUrl(portal_detail.profile_pic ? portal_detail.profile_pic.url : '/imgs/default.png')" alt />
                    </div>
                </div>
                
                <div class="pf-details col-8 text-center mt-md-1">
                    <div class="info-container d-flex text-center justify-content-center">
                        <div class="info_posts">
                            <span>{{portal_detail.posts_count}}</span>
                            <p class="mb-0">Posts</p>
                        </div>
                        <div class="info_followers" @click="openfollow(portal_detail.id)">
                            <span>{{this.followers}}</span>
                            <p class="mb-0">Followers</p>
                        </div>
                    </div>
                    <vs-popup class="portalprofile__popup" title="Follower & Following" :active.sync="popupActive">
                        <div>
                            <Tabs
                                :tabs="tabs"
                                :currentTab="currentTab"
                                :wrapper-class="'default-tabs'"
                                :tab-class="'default-tabs__item'"
                                :tab-active-class="'default-tabs__item_active'"
                                :line-class="'default-tabs__active-line'"
                                @onClick="handleClick"
                            />
                            <div class="tabs-content">
                                <div v-if="currentTab === 'followers'">
                                    <ul>
                                        <li v-for="(followerobj, index) of followerobjs" :key="`followerobj-${index}`">
                                            <div class="follower-logo" @click="gotouserprofile(followerobj.username)">
                                                <img :src="serverUrl(followerobj.url)"/>
                                            </div>
                                            <div class="username" @click="gotouserprofile(followerobj.username)">
                                                <p>{{followerobj.name}}</p>
                                            </div>
                                            <div class="action">
                                                <img src="~assets/imgs/follow-icon.png" v-if="followerobj.isfollower == 0" @click="follow(followerobj.id, 'tab', followerobj.isportal, 1)"/>
                                                <img src="~assets/imgs/unfollow.png" v-else @click="unfollow(followerobj.id, 'tab', followerobj.isportal, 1)"/>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </vs-popup>
                    <div class="portal__extra mt-md-0 mx-md-auto">
                        <img src="~assets/imgs/follow-icon.png" alt class="pf-follow" @click="follow(portal_detail.id, 'normal', 0, 1)" v-show="logged_user_id != portal_detail.id" v-if="!isfollower"/>
                        <img src="~assets/imgs/unfollow.png" alt class="pf-unfollow" @click="unfollow(portal_detail.id, 'normal', 0, 1)" v-show="logged_user_id != portal_detail.id" v-if="isfollower"/>
                        <div class="message mx-auto" style="width: 60px;" @click="openchat()" v-if="portal_detail.messenger && logged_user_id != portal_detail.id && !$device.isMobile && !isblockuser">
                            <img src="~assets/imgs/messenger-mobile.png" alt="Message"/>
                            <p class="mb-0">Message</p>
                        </div>
                        <!-- <router-link :to="{ name: 'mobilechatbox', params: {userdetail: portal_detail}}">
                            <div class="message btn-message mx-auto" v-if="portal_detail.messenger && logged_user_id != portal_detail.id && mobile && !isblockuser">
                                <img src="imgs/messenger-mobile.png" />
                                <p class="mb-0">Message</p>
                            </div>
                        </router-link> -->
                    </div>
                </div>
            </div>
            <div class="portal_info clearfix">
                <div class="d-flex align-items-center">
                    <h1 class="username mr-2 mb-0 float-left">{{portal_detail.name}}
                    </h1>
                    <a href="javascript:;" @click="openEditPopup = true" v-if="logged_user_id == portal_detail.id || logged_user_id == 1">
                        <img class="portal__edit" src="@/assets/imgs/edit.png" alt />
                    </a>
                </div>
                <p class="font-weight-bold" style="color: gray;margin-left: 15px;margin-top: -5px; clear: both;">@{{portal_detail.username}}</p>
                <p class="store_type" v-if="portal_detail.type == 'company'">
                    <img
                        src="@/assets/imgs/dispensary.png"
                        alt
                        v-if="portal_detail.store_type == 1 || portal_detail.store_type == 3"
                    />
                    <img
                        src="@/assets/imgs/delivery.png"
                        alt
                        v-if="portal_detail.store_type == 2 || portal_detail.store_type == 3"
                    />

                    <span v-if="portal_detail.store_type == 3">(Storefront + Delivery)</span>
                    <span v-if="portal_detail.store_type == 1">(Storefront)</span>
                    <span v-if="portal_detail.store_type == 2">(Delivery)</span>

                    <span
                        v-if="portal_detail.shop_status == 2"
                        class="text-center ml-2 clickable-title text-success"
                        data-toggle="modal"
                        data-target="#companyTiming"
                    >
                        Open
                        <fa icon="angle-down" class="ml-1"></fa>
                    </span>

                    <span
                        v-else
                        class="text-center ml-2 clickable-title text-danger"
                        data-toggle="modal"
                        data-target="#companyTiming"
                    >
                        Closed
                        <fa icon="angle-down" class="ml-1"></fa>
                    </span>
                </p>

                <div class="portal__information mt-1">
                    <div class="address" v-if="portal_detail.type == 'company'">
                        <p class="state_license" v-if="portal_detail.state_license">State License # {{portal_detail.state_license}}</p>
                        <p class="recreational my-0 pl-2" v-if="portal_detail.recreational && portal_detail.medical">(Recreational - Medical)</p>
                        <p class="recreational my-0 pl-2" v-else-if="portal_detail.recreational">(Recreational)</p>
                        <p class="medical my-0 pl-2" v-else-if="portal_detail.medical">(Medical)</p>
                        <p style="height:5px"></p>
                        <div class="portal__address">
                            <p v-if="portal_detail.store_type !== 2">
                                {{portal_detail.address}},
                                <span v-if="portal_detail.suite">(Suite {{portal_detail.suite}}) </span>
                            </p>
                            <p>{{portal_detail.city}}, {{portal_detail.state}} {{portal_detail.postal}}</p>
                        </div>
                    </div>

                    <p v-if="portal_detail.phone_number && portal_detail.type == 'company'" class="telephone">
                        <a :href="`tel:+${portal_detail.phone_number}`" target="_blank">
                            <fa icon="phone-alt"></fa>
                            {{portal_detail.phone_number}}
                        </a>
                    </p>

                    <!-- <editdescription class="category__content mt-3" type="portal" :strain="portal_detail"></editdescription> -->
                    <p class="mt-3 description" v-if="portal_detail.description && portal_detail.description.length <= description_max_length">{{portal_detail.description}}</p>
                    <p class="mt-3 description" v-if="portal_detail.description && portal_detail.description.length > description_max_length">
                        {{show_description}}
                        <span class="text-420 ml-3 btn-readmore" v-if="description_expanded" @click="description_expanded = false">Read Less</span>
                        <span class="text-420 ml-3 btn-readmore" v-else @click="description_expanded = true">Read More</span>
                    </p>

                    <!-- {{portal_detail}} -->

                    <div class="social mt-2">
                        <span v-if="portal_detail.website_url" class="website">
                            <a :href="`${portal_detail.website_url}`" target="_blank">
                                <fa icon="globe-americas"></fa>
                            </a>
                        </span>
                        <span v-if="portal_detail.facebook_url" class="facebook">
                            <a :href="`https://${portal_detail.facebook_url}`" target="_blank">
                                <fa :icon="['fab', 'facebook-f']"></fa>
                            </a>
                        </span>
                        <span v-if="portal_detail.twitter_url" class="twitter">
                            <a :href="`https://${portal_detail.twitter_url}`" target="_blank">
                                <fa :icon="['fab', 'twitter']"></fa>
                            </a>
                        </span>
                        <span v-if="portal_detail.instagram_url" class="instagram">
                            <a :href="`https://${portal_detail.instagram_url}`" target="_blank">
                                <fa :icon="['fab', 'instagram']"></fa>
                            </a>
                        </span>
                        <span v-if="portal_detail.youtube_url" class="youtube">
                            <a :href="`https://${portal_detail.youtube_url}`" target="_blank">
                                <fa :icon="['fab', 'youtube']"></fa>
                            </a>
                        </span>
                        <span v-if="portal_detail.email" class="email">
                            <a :href="`mailto:${portal_detail.email}`">
                                <fa icon="envelope"></fa>
                            </a>
                        </span>
                    </div>
                    <div class="amenities">
                        <img src="~assets/imgs/atm.png" alt="" v-if="portal_detail.atm" />
                        <img src="~assets/imgs/security.png" alt="" v-if="portal_detail.security" />
                    </div>
                </div>
            </div>
            <div class="portal_options mb-2 d-flex justify-content-center">
                <div class="menu" @click="openMenuModal">
                    <img class="img-fluid" src="~assets/imgs/menu1.png" alt="Menu" />
                    <span class="options__span">{{menu_counts}}</span>
                </div>
                <div class="coupon" @click="openCouponModal()" v-if="portal_detail.type == 'company'">
                    <img class="img-fluid" src="~assets/imgs/coupon1.png" alt="Coupon" />
                    <span class="options__span">{{portal_detail.coupon ? 1 : 0}}</span>
                </div>
                <div class="comment" data-toggle="modal" data-target="#commentModal">
                    <fa :icon="['far', 'comment']"></fa>
                    <span class="options__span" id="count_comments">{{portal_detail.total_comments}}</span>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row postmedia original" id="portaMedia" style="margin-right: 15px; margin: 0 auto;">
                <div class="col-md-8 posts" style="min-height: calc(100vh - 210px);" >
                    <div class="row">
                        <div class="col-4 media_container" v-for="(item, index) of posts" :key="index">
                            <div v-if="$device.isMobile" class="media">
                                <router-link :to="{ name: 'weedgram', hash:`#${index+1}`, params: {allpost : posts, start_index: index+1, page: page, model: 'portal', currentId : portal_detail.id}}" >
                                    <img v-bind:src="serverUrl(item.url)" alt v-if="item.type == 'image'" />
                                    <video v-bind:src="serverUrl(item.url)" alt v-if="item.type == 'video'" disablePictureInPicture controlsList="nodownload" onclick="this.paused ? this.play() : this.pause();"></video>
                                    <img class="video__tag__mobile" style="width:25px;" v-if="item.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                                </router-link>
                            </div>
                            <div v-else class="media" @click="changeimage(index)" @dblclick="likeMedia(item)">
                                <img :src="serverUrl(item.url)" alt v-if="item.type == 'image'" />
                                <video v-bind:src="serverUrl(item.url)" alt v-if="item.type == 'video'" disablePictureInPicture controlsList="nodownload" onclick="this.paused ? this.play() : this.pause();"></video>
                                <img class="video__tag__mobile" style="width:35px;" v-if="item.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                                <!-- <div class="menu-info" v-if="portal_detail.type == 'brand' && item.menu">
                                    <p class="category">{{item.menu.category.price_type == 2 ? 'Flower | ' + item.menu.category.name : item.menu.category.name}}{{item.menu.strain ? ' | '+item.menu.strain.name : ''}}</p>
                                    <p class="item_name">{{item.menu.item_name}}</p>
                                </div> -->
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
                <div v-if="!$device.isMobile && selected" class="col-md-4">
                    <fixed-comment :media="selected" :allposts="posts" />
                </div>
            </div>
        </div>

        <page-footer v-if="$device.isDesktop"></page-footer>

        <!-- Timing modal -->
        <div class="modal fade" id="companyTiming" tabindex="-1" role="dialog" aria-labelledby="companyTimingLabel" aria-hidden="true" v-if="portal_detail.type == 'company'">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- <div class="modal-header">
                        <h5 class="modal-title" id="companyTimingLabel">{{portal_detail.name}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>-->
                    <div class="modal-body">
                        <table class="table table-sm table-bordered table-hover table-dark">
                            <thead>
                                <tr>
                                    <!-- <th scope="col">#</th> -->
                                    <th scope="col">Day</th>
                                    <th scope="col">Open</th>
                                    <th scope="col">Closed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <!-- <th scope="row">1</th> -->
                                    <td>Monday</td>
                                    <td colspan="2" v-if="portal_detail.mon_closed === 2">Open 24 Hours</td>
                                    <td colspan="2" v-else-if="portal_detail.mon_closed === 1">Closed</td>
                                    <td v-if="portal_detail.mon_open">{{portal_detail.mon_open.replace(/^0+/, '')}}</td>
                                    <td v-if="portal_detail.mon_close">{{portal_detail.mon_close.replace(/^0+/, '')}}</td>
                                </tr>
                                <tr>
                                    <!-- <th scope="row">2</th> -->
                                    <td>Tuesday</td>
                                    <td colspan="2" v-if="portal_detail.tue_closed === 2">Open 24 Hours</td>
                                    <td colspan="2" v-else-if="portal_detail.tue_closed === 1">Closed</td>
                                    <td v-if="!portal_detail.tue_closed">{{portal_detail.tue_open.replace(/^0+/, '')}}</td>
                                    <td v-if="!portal_detail.tue_closed">{{portal_detail.tue_close.replace(/^0+/, '')}}</td>
                                </tr>
                                <tr>
                                    <!-- <th scope="row">3</th> -->
                                    <td>Wednesday</td>
                                    <td colspan="2" v-if="portal_detail.wed_closed === 2">Open 24 Hours</td>
                                    <td colspan="2" v-else-if="portal_detail.wed_closed === 1">Closed</td>
                                    <td v-if="!portal_detail.wed_closed">{{portal_detail.wed_open.replace(/^0+/, '')}}</td>
                                    <td v-if="!portal_detail.wed_closed">{{portal_detail.wed_close.replace(/^0+/, '')}}</td>
                                </tr>
                                <tr>
                                    <!-- <th scope="row">2</th> -->
                                    <td>Thursday</td>
                                    <td colspan="2" v-if="portal_detail.thu_closed === 2">Open 24 Hours</td>
                                    <td colspan="2" v-else-if="portal_detail.thu_closed === 1">Closed</td>
                                    <td v-if="!portal_detail.thu_closed">{{portal_detail.thu_open.replace(/^0+/, '')}}</td>
                                    <td v-if="!portal_detail.thu_closed">{{portal_detail.thu_close.replace(/^0+/, '')}}</td>
                                </tr>
                                <tr>
                                    <!-- <th scope="row">2</th> -->
                                    <td>Friday</td>
                                    <td colspan="2" v-if="portal_detail.fri_closed === 2">Open 24 Hours</td>
                                    <td colspan="2" v-else-if="portal_detail.fri_closed === 1">Closed</td>
                                    <td v-if="!portal_detail.fri_closed">{{portal_detail.fri_open.replace(/^0+/, '')}}</td>
                                    <td v-if="!portal_detail.fri_closed">{{portal_detail.fri_close.replace(/^0+/, '')}}</td>
                                </tr>
                                <tr>
                                    <!-- <th scope="row">2</th> -->
                                    <td>Saturday</td>
                                    <td colspan="2" v-if="portal_detail.sat_closed === 2">Open 24 Hours</td>
                                    <td colspan="2" v-else-if="portal_detail.sat_closed === 1">Closed</td>
                                    <td v-if="!portal_detail.sat_closed">{{portal_detail.sat_open.replace(/^0+/, '')}}</td>
                                    <td v-if="!portal_detail.sat_closed">{{portal_detail.mon_close.replace(/^0+/, '')}}</td>
                                </tr>
                                <tr>
                                    <!-- <th scope="row">2</th> -->
                                    <td>Sunday</td>
                                    <td colspan="2" v-if="portal_detail.sun_closed === 2">Open 24 Hours</td>
                                    <td colspan="2" v-else-if="portal_detail.sun_closed === 1">Closed</td>
                                    <td v-if="!portal_detail.sun_closed">{{portal_detail.sun_open.replace(/^0+/, '')}}</td>
                                    <td v-if="!portal_detail.sun_closed">{{portal_detail.sun_close.replace(/^0+/, '')}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comment Modal -->
        <div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document" style>
                <div class="modal-content comment_page">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><fa icon="arrow-left"></fa></span></button>
                        <h5 class="modal-title" id="couponModalLabel">Reviews - Comments</h5>
                    </div>
                    <div class="modal-body" style="padding: 0;">
                        <!-- <profile-comment :user="portal_detail"></profile-comment> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu -->

        <vs-popup v-if="portal_detail && $device.isMobile" fullscreen class="strains__popup portal_menu" type="border" title :active.sync="openPortalMenu">
            <portal-menu :portal="portal_detail" v-if="portal_detail.type == 'company'"></portal-menu>
            <brand-menu :brand="portal_detail" v-else></brand-menu>
        </vs-popup>

        <vs-popup v-if="portal_detail && $device.isMobile" class="strains__popup portal_menu" type="border" title :active.sync="openPortalMenu">
            <portal-menu :portal="portal_detail" v-if="portal_detail.type == 'company'"></portal-menu>
            <brand-menu :brand="portal_detail" v-else></brand-menu>
        </vs-popup>

        <!-- Coupon -->
        <vs-popup type="border" title :active.sync="openCouponPopup" v-if="portal_detail.type == 'company'">
            <portal-coupon :portal="portal_detail" :distance="distance"></portal-coupon>
        </vs-popup>

        <!-- Edit -->
        <!-- <client-only>
            <vs-popup class="edit_portal edit-brand" type="border" title :active.sync="openEditPopup" fullscreen v-if="$device.isMobile && editable_portal">
                <edit-portal :from="editable_portal" v-if="editable_portal.type == 'company'"></edit-portal>
                <div class="portal_addpage" v-else>
                    <brand-form :from="editable_portal" :is_edit="true"></brand-form>
                </div>
            </vs-popup>
        </client-only> -->

        <vs-popup class="edit_portal edit-brand" type="border" title :active.sync="openEditPopup" v-if="!$device.isMobile && editable_portal">
            <edit-portal :from="editable_portal" v-if="editable_portal.type == 'company'"></edit-portal>
            <div class="portal_addpage" v-else>
                <brand-form :from="editable_portal" :is_edit="true"></brand-form>
            </div>
        </vs-popup>

        <!-- First Sign Up -->
        <vs-popup type="border" title :active.sync="openGreetingPopup">
            <div class="greeting-content">
                <p>Thank you for Signing up on 420Portal.</p>
                <p v-if="portal_detail.type == 'company'">Please take a moment and add your Menu & Coupon.</p>
                <p v-if="portal_detail.type == 'brand'">Please take a moment and add your Menu.</p>
                <p>Remember, The More you Post the More you Grow!</p>
                <p>🌱</p>
            </div>
        </vs-popup>
    </div>
</template>

<script>
import FixedComment from "./FixedComment";
import PageFooter from "./PageFooter";
// import ProfileComment from "./ProfileComment";
import PortalMenu from './portal/Menu';
// import EditPortal from './portal/EditPortal';
import PortalCoupon from './portal/Coupon';
import BrandMenu from './brand/Menu';
import Tabs from 'vue-tabs-with-active-line';
import firebase from "../Firebase";
import { mapGetters } from "vuex";

export default {
    props: ["profile", "flatchat"],
    name: "PortalProfile",
    components: {
        FixedComment,
        PageFooter,
        PortalMenu,
        BrandMenu,
        PortalCoupon,
        // EditPortal,
        Tabs,
    },
    watch: {
        description_expanded : function(new_flag, old_flag) {
            if(new_flag) {
                this.show_description = this.profile.description;
            } else {
                this.show_description = this.profile.description ? this.profile.description.substring(0, this.description_max_length) : '';
            }
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
    }),
    data() {
        return {
            current_position: { lat: 0, lng: 0 },
            portal_detail: this.profile,
            editable_portal: null,
            logged_user_id: 0,
            markerOptions: [
                {
                    url: "/imgs/dispensary.png",
                    size: { width: 30, height: 30, f: "px", b: "px" },
                    scaledSize: { width: 30, height: 30, f: "px", b: "px" }
                },
                {
                    url: "/imgs/delivery.png",
                    size: { width: 30, height: 30, f: "px", b: "px" },
                    scaledSize: { width: 30, height: 30, f: "px", b: "px" }
                },
                {
                    url: "/imgs/dispensary.png",
                    size: { width: 30, height: 30, f: "px", b: "px" },
                    scaledSize: { width: 30, height: 30, f: "px", b: "px" }
                }
            ],
            posts: [],
            loading: true,
            comments: null,
            selected: null,
            hasLiked: this.profile.user_liked,
            totalLikes: this.profile.combinedLikes,
            chatuserlist: [],
            openPortalMenu : false,
            followers: 0,
            isfollower: 0,
            isblockuser: false,
            menu_counts : 0,
            followerobjs: [],
            popupActive: false,
            openCouponPopup: false,
            openEditPopup: false,
            openGreetingPopup: false,
            tabs: [
                { title: 'Followers', value: 'followers' }
            ],
            loading : false,
            currentTab: 'followers',
            page: 1,
            posts_count: 0,
            show_description: this.profile.description ? this.profile.description.substring(0, 100) : '',
            description_expanded : false,
            description_max_length: this.$device.isMobile ? 80 : 200,
            distance: '',
        };
    },
    mounted() {
        if(process.client) {
            this.scroll();
        }        
        this.current_position.lat = Number(this.portal_detail.latitude);
        this.current_position.lng = Number(this.portal_detail.longitude);
        this.getfollow();
        this.getIsFollower();
        if(this.auth_user) {
            this.checkVisited();
            this.checkblockuser();
            this.logged_user_id = this.auth_user.id
        }
        this.menu_counts = this.portal_detail.menus.filter(menu => menu.is_active ==1).length;
        this.$parent.loading = false;
        setTimeout(function(){document.documentElement.scrollTop = 0;}, 2000);
        var _this = this;
        // $(document).ready(function () {
            _this.editable_portal = _this.portal_detail;
        // })
    },
    created() {
        if(process.client) {           
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.getDistance);
            } else {
                alert(
                    "Geolocation is not supported by this browser. \n Please enable it."
                );
            } 
        }
    },
    methods: {
        getDistance(position) {
            let latitude = position.coords.latitude;
            let longitude = position.coords.longitude;
            let uri = '/portal/get_distance';
            let params = {
                id: this.profile.id,
                latitude: latitude,
                longitude: longitude,
            };
            this.axios.post(uri, params).then(response => {
                if(response.data.status == 200) {
                    this.distance = response.data.distance;
                    this.profile.distance = response.data.distance;
                    this.portal_detail.distance = response.data.distance;
                }
            });
        },
        handleClick(newTab) {
            this.currentTab = newTab;
        },
        checkVisited() {
            if(!this.portal_detail.is_visited && this.auth_user.id == this.portal_detail.id) {
                this.openGreetingPopup = true;
                this.axios.get(`/user/visited/${this.portal_detail.id}`).then(response => {
                    this.portal_detail.is_visited = 1;
                })
            }
        },
        getallposts($state) {
            let uri = "/profile/getallposts";
            let params = {
                target_id: this.portal_detail.id,
                model: this.portal_detail.type == 'brand' ? 'brand' : 'portal',
                page: this.page,
            };
            this.loading = true;
            this.axios.post(uri, params).then(response => {
                if (response.data.postdata.data.length) {
                    this.posts_count = response.data.postdata.total;
                    if(this.page == 1 && this.posts.length) {                            
                        this.posts = [];
                    }
                    if(this.posts.length == 0) {
                        this.posts = response.data.postdata.data;
                    } else {                        
                        this.posts = _.concat(this.posts, response.data.postdata.data);
                    }
                    if (this.posts.length > 0) {
                        this.selected = this.posts.filter(post => !post.menu || (post.menu && post.menu.is_active == 1))[0];
                    }
                    this.page++;
                    $state.loaded();
                } else {
                    this.$parent.loading = true;
                    $state.complete();
                }
            });
        },
        getfollow(){
            let uri = "/profile/getfollow";
            let params = {
                user_id: this.profile.id,
            }
            this.axios.post(uri, params).then(response => {
                this.followers = response.data.followers;
            });
        },
        getIsFollower() {
            if(this.auth_user) {
                let uri = "/profile/getisfollower";
                let params = {
                    user_id: this.auth_user.id,
                    follower_user_id: this.profile.id
                }
                this.axios.post(uri, params).then(response => {
                    if(response.data.status == 1)
                        this.isfollower = 1;
                });
            }
        },
        openfollow(id) {
            let uri = "/user/allfollows";
            let params = {
                user_id: id
            }
            this.axios.post(uri, params).then(response => {
                this.followerobjs = response.data.followers;
                this.popupActive = true;
            });
        },
        gotouserprofile(username){
            let url = "/" + username;
            window.location.href = url;
        },
        changeimage(index) {
            this.selected = this.posts[index];
        },
        openchat(){
            if(this.auth_user){
                let user = {
                    from: this.auth_user.id,
                    to: this.portal_detail.id,
                    name: this.portal_detail.name,
                    username: this.portal_detail.username,
                    url: this.portal_detail.profile_pic ? this.portal_detail.profile_pic.url : '/imgs/default_sm.png',
                    type: this.portal_detail.type,
                    store_type: this.portal_detail.store_type,
                };
                this.$parent.pushchatuserlist(user);
            }else{
                $("#loginmodal").modal("show");
            }

        },
        openMenuModal() {
            this.openPortalMenu = true;
        },
        follow(id, type, isportal, isfollower){
            if(this.auth_user){
                if(this.auth_user.type != 'user') return false;
                if(this.auth_user.id == id && isportal == 0)
                    return false;
                if(this.auth_user.type != 'user') {
                    return false;
                }
                let uri = '/user/follow';
                let params = {
                    user_id: this.auth_user.id,
                    follower_id: id
                };
                this.axios.post(uri, params).then(response => {
                    if(type == 'normal'){
                        this.followers++;
                        this.isfollower = 1;
                    }else{
                        if(isfollower){
                            for (let i = 0; i < this.followerobjs.length; i++) {
                                if (this.followerobjs[i]['id'] === id)
                                    this.followerobjs[i]['isfollower'] = !this.followerobjs[i]['isfollower'];
                            }
                        }
                    }
                });
            }else{
                $("#loginmodal").modal('show');
            }
        },
        unfollow(id, type, isportal, isfollower){
            if(this.auth_user){
                if(this.auth_user.type != 'user') return false;
                if(this.auth_user.id == id && isportal == 0)
                    return false;
                if(this.auth_user.type != 'user') {
                    return false;
                }
                let uri = '/user/unfollow';                
                let params = {
                    user_id: this.auth_user.id,
                    follower_id: id
                };
                this.axios.post(uri, params).then(response => {
                    if(type == 'normal'){
                        this.followers--;
                        this.isfollower = 0;
                    }else{
                        if(isfollower){
                            for (let i = 0; i < this.followerobjs.length; i++) {
                                if (this.followerobjs[i]['id'] === id)
                                    this.followerobjs[i]['isfollower'] = !this.followerobjs[i]['isfollower'];
                            }
                        }
                    }
                });
            }else{
                $("#loginmodal").modal('show');
            }
        },
        checkblockuser() {
            let url = "/user/isblockuser";

            let params = {
                receiver: this.portal_detail.id,
                sender: this.auth_user.id,
                type: 'portal',
            };

            return this.axios.post(url, params).then((response) => {
                // 1: block, 0: unblock
                this.isblockuser = response.data;
            });
        },
        scroll() {
            var header = document.getElementById("portaMedia");
            var sticky = header.offsetTop;
            var sticky = sticky - 74;
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
        openCouponModal() {
            if((this.auth_user && (this.auth_user.id == this.portal_detail.id || this.auth_user.id == 1)) || this.portal_detail.coupon) {                
                this.openCouponPopup = true;
            } else {
                if(this.portal_detail.coupon) {
                    this.openCouponPopup = true;
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
    #commentModal {
        .modal-dialog {
            margin-top: 80px;
            max-width: 480px;
        }
        @media only screen and (max-width: 600px) {
            .modal-dialog {
                margin: 0px auto;
            }
        }
        .emojionearea-button {
            top: 30% !important;
        }

        .modal-header {
            padding: 6px 0.8rem;
            background: #000;
            border-radius: 0;
            border-bottom: solid 1px white;

            justify-content: flex-start;
            align-items: center;
            .close {
                color: #EFA720;
                margin: 0;
                margin-right: 10px;
                padding: 0;
                position: initial;
                text-shadow: none;
            }
            .modal-title {
                color: #EFA720;
                font-size: 1.3rem;
            }
        }
    }

    .portal_info a {
        color: #efa720;
        font-size: 18px;
    }

    @media (max-width: 800px) {
        .portal_info a {
            font-size: 19px;
        }
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
            font-size: 13.5px;
        }
        .item_name {
            margin: 0;
            color: #EFA720;
            font-size: 16px;
        }
    }

    .coupon-detail {
        &.result-container {
            display: flex;
            .result-media {
                position: relative;
                width: 125px;
                .media {
                    img, video {
                        width: 125px;
                        height: 125px;
                        object-fit: cover;
                    }
                    img.video__tag__mobile {
                        position: absolute;
                        height: 25px;
                        width: auto;
                        top: -2px;
                        right: 0;
                    }
                }
            }
            .result-data {
                flex-grow: 1;
                padding-left: 40px;
                .category-strain {
                    color: gray;
                    font-size: 13px;
                    margin-top: 0;
                    margin-bottom: 0;
                }
                .title {
                    color: #EFA720;
                    font-size: 18px;
                    margin-bottom: 0;
                }
                .sub-title {
                    color: gray;
                    margin-left: 8px;
                    font-size: 15px;
                }
                .company-name {
                    font-size: 18px;
                    color: #EFA720;
                    display: flex;
                    .store-type-img {
                        width: 22px;
                        height: 22px;
                        margin-top: 2px;
                    }
                }
                .company-status {
                    font-size: 15px;
                    margin-left: 8px;
                    .shop_status {
                        margin-right: 5px;
                        &.open {
                            color: #93E612;
                        }
                        &.closed {
                            color: #FF0000;
                        }
                    }
                    .distance {                        
                        color: gray;
                    }
                }
                .expire_date {
                    font-size: 15px;
                }
            }
            @media (max-width: 600px) {
                .result-media {
                    width: 80px;
                    .media {
                        img, video {
                            width: 80px;
                            height: 80px;
                        }
                        img.video__tag__mobile {
                            position: absolute;
                            height: 25px;
                            width: auto;
                            top: -2px;
                            right: 0;
                        }
                    }
                }
                .result-data {
                    padding-left: 10px;
                    .title {
                        font-size: 18px;
                    }
                    .company-type {
                        font-size: 18px;
                    }
                    .company-status {
                        margin-top: 2px;
                        .shop_status {
                            font-size: 15px;
                        }
                    }
                }
            }
        }
    }

    .con-vs-popup.fullscreen {
        .vs-popup--content {
            max-height: calc(100vh - 45px);
            padding-top: 0;
        }
    }
    .greeting-content {
        text-align: center;
        p {
            margin: 5px 0;
            font-size: 18px;
            @media (max-width: 600px) {
                font-size: 13px
            }
        }
    }
</style>