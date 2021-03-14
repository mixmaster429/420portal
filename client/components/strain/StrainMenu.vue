<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 mt-2" v-for="(item, index) of menus" :key="index">
                <div class="result-container">
                    <div class="result-media">
                        <div class="media" @mouseover="item.show_description = true" @mouseleave="item.show_description = false" v-if="item.media">
                            <img v-if="item.media.type == 'image'" :src="serverUrl(item.media.url)" alt="" />
                            <video v-if="item.media.type == 'video'" :src="serverUrl(item.media.url)" disablepictureinpicture controlslist="nodownload">
                                <source :src="item.media.url" type="video/mp4" />
                                <source :src="item.media.url" type="video/webm" />
                                <source :src="item.media.url" type="video/ogg" />Your browser does not support the video tag.
                            </video>
                            <img class="video__tag__mobile" v-if="item.media.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                        </div>
                        <div class="media" @mouseover="item.show_description = true" @mouseleave="item.show_description = false" v-else>
                            <img :src="serverUrl(strain.url.url)" alt="" />
                        </div>
                        <div class="item-description" v-if="item.description && item.show_description" @mouseover="item.show_description = true" @mouseleave="item.show_description = false">{{item.description}}</div>
                    </div>
                    <div class="result-data" @click="goPortalProfile(item)">
                        <p class="category-strain my-0">
                            <span class="category">{{item.category.price_type == 2 ? 'Flower | ' + item.category.name : item.category.name}}</span>
                            <span class="strain" v-if="item.strain"> | {{item.strain.name}}</span>
                        </p>
                        <h4 class="title">{{item.item_name}}</h4>
                        <div class="company-name">
                            <img class="store-type-img" src="/imgs/dispensary.png" alt v-if="item.portal.store_type == 1 || item.portal.store_type == 3" />
                            <img class="store-type-img" src="/imgs/delivery.png" alt v-if="item.portal.store_type == 2 || item.portal.store_type == 3" />
                            &nbsp;{{item.portal.name}}
                        </div>
                        <div class="company-status">
                            <span class="shop_status open" v-if="item.shop_status == 2">Open</span>
                            <span class="shop_status closed" v-else>Closed</span>
                            <span class="distance">{{item.distance}} Miles</span>
                        </div>
                        <div class="price_data mb-0">
                            <span class="text-white pr-2" v-if="item.price_each != null">{{item.price_each}}<sup class="text-420">each</sup></span>
                            <span class="text-white pr-2" v-if="item.price_half_gram != null">{{item.price_half_gram}}<sup class="text-420">1/2g</sup></span>
                            <span class="text-white pr-2" v-if="item.price_gram != null">{{item.price_gram}}<sup class="text-420">g</sup></span>
                            <span class="text-white pr-2" v-if="item.price_eighth != null">{{item.price_eighth}}<sup class="text-420">1/8</sup></span>
                            <span class="text-white pr-2" v-if="item.price_quarter != null">{{item.price_quarter}}<sup class="text-420">1/4</sup></span>
                            <span class="text-white pr-2" v-if="item.price_half_oz != null">{{item.price_half_oz}}<sup class="text-420">1/2</sup></span>
                            <span class="text-white pr-2" v-if="item.price_oz != null">{{item.price_oz}}<sup class="text-420">oz</sup></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from "vuex";
    export default {
        props : ['strain', 'category', 'type'],
        data(){
            return {
                menus: {},
                latitude: 0, 
                longitude: 0,
            };
        },
        computed: mapGetters({
            user: 'auth/user',
        }),
        created() {
          console.log(this.strain)
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.showPosition);
            } else {
                alert(
                    "Geolocation is not supported by this browser. \n Please enable it."
                );
            }
        },
        methods: {
            get_menus() {
                let url = '/strain/get_menus';
                let params = {
                    id: this.strain.id, 
                    category : this.category,
                    latitude : this.latitude,
                    longitude : this.longitude,
                };
                this.axios.post(url, params).then(response => {
                    this.menus = response.data;
                    console.log(response.data)
                });
            },
            showPosition(position) {
                this.latitude = position.coords.latitude;
                this.longitude = position.coords.longitude;
                this.get_menus();
            },
            goPortalProfile(item) {
                window.location.href = "/" + item.portal.username;
            },
            serverUrl(item) {
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + this.strain.url.url;
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .result-container {
        display: flex;
        border: solid 1px gray;
        padding: 5px;
        @media (min-width: 800px) {
            height: 153px;
        }        
        .result-media {
            position: relative;
            width: 140px;
            .media {
                img, video {
                    width: 140px;
                    height: 140px;
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
            
            .item-description {
                width: 520px;
                top: 75px;
                min-height: 70px;
                border: solid 1px gray;
                background-color: #000;
                position: absolute;
                z-index: 1;
                border-radius: 3px;
                padding: 5px 10px;
                font-size: 14.4px;
                @media(max-width: 600px) {
                    top: 70px;
                    width: 315px;
                }
            }
        }
        .result-data {
            flex-grow: 1;
            padding-left: 25px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            .category-strain {
                color: gray;
                font-size: 13px;
                margin-top: 0;
                margin-bottom: 0;
            }
            .title {
                color: #EFA720;
                font-size: 19px;
                margin-bottom: 0;
            }
            .sub-title {
                color: gray;
                font-size: 13px;
                line-height: 15px;
                margin-left: 8px;
            }
            .company-name {
                margin-top: auto !important;
                color: #EFA720;
                font-size: 17px;
                display: flex;
                .store-type-img {
                    width: 22px;
                    height: 22px;
                    margin-top: 2px;
                }
            }
            .company-status {
                line-height: 18px;
                margin: -2px 0 3px 8px;
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
                margin-top: 2px;
                font-size: 15px;
            }
        }
    }
    @media (max-width: 600px) {
        .result-container {
            .result-media {
                position: relative;
                width: 80px;
                .media {
                    img, video {
                        width: 80px;
                        height: 80px;
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
                
                .item-description {
                    width: 315px;
                    top: 70px;
                    font-size: 14.4px;
                }
            }
            .result-data {
                padding-left: 10px;
                .category-strain {
                    font-size: 14px;
                }
                .title {
                    font-size: 20px;
                }
                .sub-title {
                    font-size: 14px;
                    margin-bottom: 5px;
                }
                .company-name {
                    line-height: 20px;
                    margin-top: 3px;
                    .store-type-img {
                        width: 22px;
                        margin-top: 0;
                    }
                }
                .company-status {
                    line-height: 16px;
                    margin-top: 2px;
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
                        font-size: 14px;
                    }
                }
                .price_data {
                    font-size: 14.4px;
                }
            }
        }
    }
</style>