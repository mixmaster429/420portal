<template>
    <div class="row justify-content-center coupon_page">
        <div class="container" style="min-height:calc(100vh - 522px);">
            <h1 class="text-center coupon_header mt-3" data-toggle="modal" data-target="#couponModal">
                <img src="/imgs/h_icon3.png" width="35" style="margin-top: -8px;" alt="" />
                Marijuana Coupons
            </h1>
            <div class="row mt-3">
                <div class="col-md-6 col-12" v-for="(item, index) of coupons" :key="index">
                    <a :href="item.portal.username" class="coupon-item">
                        <div class="coupon-detail result-container">
                            <div class="result-media">
                                <div class="media">
                                    <img :src="serverUrl(item.media.url)" v-if="item.media && item.media.type == 'image'" alt="" />
                                    <img :src="serverUrl(item.portal.media.url)" v-else-if="item.portal.media && item.portal.media.type == 'image'" alt="" />
                                    <img src="/imgs/default.png" v-else alt="" />
                                </div>
                            </div>
                            <div class="result-data">
                                <p class="category-strain my-0">
                                    <span class="category">{{item.category.price_type == 2 ? 'Flower | ' + item.category.name : item.category.name}}</span>
                                    <span class="strain" v-if="item.strain"> | {{item.strain.name}}</span>
                                </p>
                                <h4 class="title">{{item.description}}</h4>
                                <p class="sub-title my-0">by {{item.brand_name}}</p>
                                <div class="company-name">
                                    <img class="store-type-img" src="/imgs/dispensary.png" alt v-if="item.portal.store_type == 1 || item.portal.store_type == 3" />
                                    <img class="store-type-img" src="/imgs/delivery.png" alt v-if="item.portal.store_type == 2 || item.portal.store_type == 3" />
                                    &nbsp;{{item.portal.name}}
                                </div>
                                <p class="company-status mb-0">
                                    <span class="shop_status open" v-if="item.portal.shop_status == 2">Open</span>
                                    <span class="shop_status closed" v-else>Closed</span>
                                    <span class="distance">{{item.distance}} Miles</span>
                                </p>
                                <h5 class="expire_date mt-1 mb-0">Expires: {{new Date().toLocaleDateString()}}</h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <page-footer></page-footer>
        
        <div class="modal fade" id="couponModal">
            <div class="modal-dialog modal-lg border-0">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <button type="button" class="close text-420" data-dismiss="modal"><fa icon="times" fixed-width></fa></button>
                    </div>
                    <div class="modal-body" style="background:white !important;">
                        <h2 class="text-center">Find Your Marijuana Coupons</h2>
                        <p>If you're looking for Marijuana Coupons, look no further. 420Portal has Coupons Uploaded by Marijuana Dispensaries and Deliveries.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PageFooter from '~/components/PageFooter'
    export default {
        components: {
            PageFooter,
        },
        head () {
            return { 
                title: 'Marijuana Coupons',
                meta: [
                    { hid: 'title', name: 'title', content: 'Marijuana Coupons - Weed Coupons' },
                    { hid: 'keywords', name: 'keywords', content: 'marijuana, weed, coupons, dispensary, delivery, deals, cannabis' },
                    { hid: 'description', name: 'description', content: 'Find your Marijuana Coupons. Dispensaries and Deliveries Selling Cannabis Provide Weed Coupons to Save you Money.' }
                ],
            }
        },
        data() {
            return {
                expiredate: '',
                current_position: {lat: 0, lng: 0},
                coupons: []
            }
        },
        created() {
            if(process.client) {
                this.expiredate = new Date().toLocaleDateString();
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(this.showPosition);
                } else {
                    alert(
                        "Geolocation is not supported by this browser. \n Please enable it."
                    );
                }  
            }
        },
        methods: {
            showPosition(position) {
                this.current_position.lat = position.coords.latitude;
                this.current_position.lng = position.coords.longitude;
                this.getCoupons();
            },
            getCoupons() {
                let uri = "/marijuana-coupons";
                let params = {
                    lat: this.current_position.lat,
                    lng: this.current_position.lng
                };
                this.axios.post(uri, params).then(response => {
                    this.coupons = response.data;
                });
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
    .coupon-detail {
        &.result-container {
            display: flex;
            height: 155px;
            @media (max-width: 600px) {
                height: 145px;
            }
            .result-media {
                position: relative;
                width: 125px;
                .media {
                    margin-top: 3px;
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
                padding-left: 25px;
                display: flex;
                flex-direction: column;
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
                    max-height: 40px;
                    overflow: auto;
                    margin-top: -3px;
                }
                .sub-title {
                    color: gray;
                    font-size: 13px;
                    padding-left: 8px;
                }
                .company-name {
                    font-size: 18px;
                    display: flex;
                    margin-top: auto;
                    color: #EFA720;
                    line-height: 20px;
                    .store-type-img {
                        width: 22px;
                        height: 22px;
                    }
                }
                .company-status {
                    line-height: 16px;
                    margin-left: 8px;
                    .shop_status {
                        font-size: 15px;
                        margin-right: 5px;
                        &.open {
                            color: #93E612;
                        }
                        &.closed {
                            color: #FF0000;
                        }
                    }
                    .distance {
                        font-size: 13px;                      
                        color: gray;
                    }
                }
                .price_data {
                    font-size: 13px;
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
                    .category-strain {
                        font-size: 14px;
                    }
                    .title {
                        font-size: 18px;
                    }
                    .sub-title {
                        font-size: 14px;
                    }
                    .company-name {
                        margin-top: 3px;
                        font-size: 16px;
                    }
                    .company-status {
                        margin-top: 2px;
                        font-size: 14px;
                    }
                    .price_data {
                        font-size: 14px;
                    }
                    .expire_date {
                        font-size: 14px;
                    }
                }
            }
        }
    }
</style>
