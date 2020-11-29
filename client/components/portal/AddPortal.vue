<template>
    <div class="pa_addform col-xl-3 col-lg-4 col-md-6 col-sm-6 mx-auto">
        <div class="pa-logo">
            <h4 class="pa-logotitle" style="cursor: pointer;">Company Logo*</h4>
            <label for="pa_imageselect" class="d-block">
                <div class="logo_preview" style="cursor: pointer;">
                    <img :src="profileMedia" alt="" id="pa_logo_image" name="pa_logo_image" v-show="profileMedia">
                </div>
            </label>
            <div class="progress" id="progress_logo" style="height: 10px; width: 150px; margin: 10px auto; display: none;" v-show="uploading">
                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" :style="{width: uploadProgress + '%'}"></div>
            </div>
            <file-upload
                name="postfile"
                input-id="pa_imageselect"
                class="text-420"
                extensions="gif,jpg,jpeg,png,webp"
                accept="image/*"
                ref="upload"
                v-model="files"
                :data="{media_type: 'user', username : portal.username}"
                :post-action="upload_url"
                @input-file="inputFile"
                @input-filter="inputFilter"
            >Select</file-upload>
        </div>
        <form action="" class="pt-3" id="pa_addform" method="post" ref="pa_addform" @submit.prevent="savePortal()">
            <div class="pa-logo">
                <input type="hidden" name="pa_logourl" id="pa_logourl" :value="pa_logourl">
            </div>

            <div class="form-group floating-label mt-4">
                <select name="store_type" id="pa_store_type" class="floating-select" v-model="portal.store_type" required>
                    <option value="" hidden></option>
                    <option value="1">Storefront</option>
                    <option value="2">Delivery</option>
                    <option value="3">Storefront + Delivery</option>
                </select>
                <label for="" class="pa_type">Company Type *</label>
            </div>

            <div class="floating-label mt-4">
                <input type="text" id="portal_username" class="floating-input" name="name" required v-model="portal.name" placeholder=" ">
                <span class="invalid-feedback"><strong></strong></span>
                <label for="name">Company Name *</label>
            </div>

            <div class="floating-label">
                <input type="text" class="form-control floating-input" name="username" required v-model="portal.username" maxlength="25" placeholder=" ">
                <span class="invalid-feedback"><strong></strong></span>
                <label>Company Username *</label>
            </div>

            <div class="floating-label">
                <input type="password" class="form-control floating-input" name="password" required v-model="portal.password" placeholder=" ">
                <span class="invalid-feedback"><strong></strong></span>
                <label>Password *</label>
            </div>

            <div class="floating-label">
                <input type="text" class="floating-input" name="state_license" id="state_license" placeholder=" " v-model="portal.state_license" required />
                <span class="invalid-feedback"><strong></strong></span>
                <label for="state_license">State License # *</label>
            </div>

            <div class="custom-check d-flex">
                <div class="round mr-1">
                    <input type="checkbox" value="1" name="recreational" id="recreational" v-model="portal.recreational" />
                    <label for="recreational"></label>
                </div>
                <label class="ml-2 text-white" for="recreational">License to Sell Recreational Cannabis.</label>
            </div>
            <hr style="border-top: 1px solid white; margin-top: .1rem;" />

            <div class="custom-check d-flex">
                <div class="round mr-1">
                    <input type="checkbox" value="1" name="medical" id="medical" v-model="portal.medical" />
                    <label for="medical"></label>
                </div>
                <label class="ml-2 text-white" for="medical">License to Sell Medical Cannabis.</label>
            </div>                    
            <hr style="border-top: 1px solid white; margin-top: .1rem;" />

            <div class="floating-label">
                <input type="text" class="form-control floating-input" name="phone_number" v-model="portal.phone_number" placeholder=" ">
                <label>Company Phone *</label>
            </div>

            <div class="floating-label mb-2">
                <input type="text" class="floating-input" name="email" v-model="portal.email" placeholder="Email" required>
                <label>Company Email *</label>
            </div>

            <!-- dispensary and delivery -->
            <div class="ap_delivery" v-show="portal.store_type == 2">
                <p>Move icon to desired location.</p>
                <div id="ap_map">
                    <GmapMap
                        ref="mapRef"
                        :options="{ mapTypeControl: false, streetViewControl: false }"
                        :center="map_center"
                        :zoom="11"
                        map-type-id="roadmap"
                        style="width: 100%; height: 100%"
                    >
                        <GmapMarker
                            :position="{lat: Number(portal.latitude), lng: Number(portal.longitude)}"
                            :draggable="true"
                            :icon="markerOptions[portal.store_type-1]"
                            @drag="updateCoordinates($event)"
                        />
                    </GmapMap>
                </div>
            </div>

            <div class="ap_dispensary mt-4" v-show="portal.store_type == 1 || portal.store_type == 3">
                <client-only>
                    <div class="floating-label">
                        <gmap-autocomplete
                            class="floating-input"
                            @place_changed="updateAddress"
                        ></gmap-autocomplete>
                        <label>Company Address *</label>
                    </div>
                </client-only>

                <input type="hidden" id="address_name" name="address_name" :value="portal.address" />
                <input type="hidden" id="city" name="city" :value="portal.city" />
                <input type="hidden" id="state" name="state" :value="portal.state" />
                <input type="hidden" id="postal" name="postal" :value="portal.postal" />

                <div class="floating-label">
                    <input type="text" class="form-control floating-input" id="ad_suit" name="suite" v-model="portal.suite" placeholder="suite">
                    <label>Suite #</label>
                </div>
            </div>

            <input type="hidden" name="latitude" id="pa_latitude" v-model="portal.latitude">
            <input type="hidden" name="longitude" id="pa_longitude" v-model="portal.longitude">

            <div class="form-label-group portal_description floating-portal floating-label" style="margin-bottom:12px;margin-top:35px;">
                <textarea class="form-control comment_text" name="description" id="ap-description" placeholder=" " v-model="portal.description"></textarea>
                <label class="companyLabel" :class="{floating : portal.description}">Company Bio</label>
            </div>

            <div class="pa-time mt-4 mb-3">
                <h4>Social Media</h4>
            </div>

            <div class="floating-label">
                <input type="text" class="floating-input" name="website_url" id="website" data-value="http://" v-model="portal.website_url" placeholder=" ">
                <label for="website">Website Url</label>
            </div>

            <div class="floating-label">
                <input type="text" class="floating-input" name="facebook_url" id="facebook" v-model="portal.facebook_url" data-value="facebook.com/" placeholder="fb">
                <label for="facebook">Facebook Url</label>
            </div>

            <div class="floating-label">
                <input type="text" class="floating-input" name="twitter_url" id="twitter" v-model="portal.twitter_url" data-value="twitter.com/" placeholder="tw">
                <label for="twitter">Twitter Url</label>
            </div>

            <div class="floating-label">
                <input type="text" class="floating-input" name="instagram_url" id="instagram" v-model="portal.instagram_url" data-value="instagram.com/" placeholder="is">
                <label for="instagram">Instagram Url</label>
            </div>

            <div class="floating-label">
                <input type="text" class="floating-input" name="youtube_url" id="youtube" v-model="portal.youtube_url" data-value="youtube.com/user/" placeholder=" ">
                <label for="youtube">Youtube Url</label>
            </div>

            <!-- ***** Amenities ***** -->

            <div class="pa-time mt-4 mb-3">
                <h4>Amenities</h4>
            </div>

            <div class="custom-check d-flex">
                <div class="round mr-1">
                    <input type="checkbox" value="1" name="atm" id="atm" v-model="portal.atm" />
                    <label for="atm"></label>
                </div>
                <label class="ml-2 text-white" for="atm">ATM</label>
            </div>                    
            <hr style="border-top: 1px solid white; margin-top: .1rem;" />

            <div class="custom-check d-flex">
                <div class="round mr-1">
                    <input type="checkbox" value="1" name="security" id="security" v-model="portal.security" />
                    <label for="security"></label>
                </div>
                <label class="ml-2 text-white" for="security">Security</label>
            </div>                    
            <hr style="border-top: 1px solid white; margin-top: .1rem;" />

            <!-- ***** Company Hours ***** -->

            <div class="pa-time mt-4 mb-3">
                <h4>Company Hours *</h4>
            </div>

            <div class="time-frame">
                <h5 class="text-center">Monday</h5>

                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="monday_times_" value="0">
                        <input class="24_hours_open" type="checkbox" value="2" id="monday_times" name="mon_closed" @change="openPortal('monday')" :checked="portal.mon_closed === 2" />
                        <label for="monday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="monday_times">Open 24 hours</label>
                </div>
                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="cmonday_times_" value="0">
                        <input class="closed_24_hour" type="checkbox" value="1" name="mon_closed" id="cmonday_times" @change="closePortal('monday')" :checked="portal.mon_closed === 1" />
                        <label for="cmonday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="cmonday_times">Closed</label>
                </div>
                <div class="monday_times cmonday_times" v-show="!portal.mon_closed">
                    <div class="openclose">
                        <span class="label">Open :</span>
                        <input class="timepicker tp-open" id="m_open" width="100" readonly name="mon_open" v-model="portal.mon_open" v-timepicker="'mon_open'" />
                    </div>
                    <div class="openclose">
                        <span class="label">Closed :</span>
                        <input class="timepicker tp-close" id="m_close" width="100" readonly name="mon_close" v-model="portal.mon_close"  v-timepicker="'mon_close'" />
                    </div>
                </div>
            </div>

            <div class="time-frame">
                <h5 class="text-center">Tuesday</h5>

                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="tuesday_times_" value="0">
                        <input class="24_hours_open" type="checkbox" value="2" id="tuesday_times" name="tue_closed" @change="openPortal('tuesday')" :checked="portal.tue_closed === 2" />
                        <label for="tuesday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="tuesday_times">Open 24 hours</label>
                </div>
                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="ctuesday_times_" value="0">
                        <input class="closed_24_hour" type="checkbox" value="1" name="tue_closed" id="ctuesday_times" @change="closePortal('tuesday')" :checked="portal.tue_closed === 1" />
                        <label for="ctuesday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="ctuesday_times">Closed</label>
                </div>
                <div class="tuesday_times ctuesday_times" v-show="!portal.tue_closed">
                    <div class="openclose">
                        <span class="label">Open :</span>
                        <input class="timepicker tp-open" id="t_open" width="100" readonly name="tue_open" v-model="portal.tue_open" v-timepicker="'tue_open'" />
                    </div>
                    <div class="openclose">
                        <span class="label">Closed :</span>
                        <input class="timepicker tp-close" id="t_close" width="100" readonly name="tue_close" v-model="portal.tue_close"  v-timepicker="'tue_close'" />
                    </div>
                </div>
            </div>

            <div class="time-frame">
                <h5 class="text-center">Wednesday</h5>

                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="wednesday_times_" value="0">
                        <input class="24_hours_open" type="checkbox" value="2" id="wednesday_times" name="wed_closed" @change="openPortal('wednesday')" :checked="portal.wed_closed === 2" />
                        <label for="wednesday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="wednesday_times">Open 24 hours</label>
                </div>
                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="cwednesday_times_" value="0">
                        <input class="closed_24_hour" type="checkbox" value="1" name="wed_closed" id="cwednesday_times" @change="closePortal('wednesday')" :checked="portal.wed_closed === 1" />
                        <label for="cwednesday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="cwednesday_times">Closed</label>
                </div>
                <div class="wednesday_times cwednesday_times" v-show="!portal.wed_closed">
                    <div class="openclose">
                        <span class="label">Open :</span>
                        <input class="timepicker tp-open" id="w_open" width="100" readonly name="wed_open" v-model="portal.wed_open" v-timepicker="'wed_open'" />
                    </div>
                    <div class="openclose">
                        <span class="label">Closed :</span>
                        <input class="timepicker tp-close" id="w_close" width="100" readonly name="wed_close" v-model="portal.wed_close"  v-timepicker="'wed_close'" />
                    </div>
                </div>
            </div>

            <div class="time-frame">
                <h5 class="text-center">Thursday</h5>

                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="thursday_times_" value="0">
                        <input class="24_hours_open" type="checkbox" value="2" id="thursday_times" name="thu_closed" @change="openPortal('thursday')" :checked="portal.thu_closed === 2" />
                        <label for="thursday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="thursday_times">Open 24 hours</label>
                </div>
                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="cthursday_times_" value="0">
                        <input class="closed_24_hour" type="checkbox" value="1" name="thu_closed" id="cthursday_times" @change="closePortal('thursday')" :checked="portal.thu_closed === 1" />
                        <label for="cthursday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="cthursday_times">Closed</label>
                </div>
                <div class="thursday_times cthursday_times" v-show="!portal.thu_closed">
                    <div class="openclose">
                        <span class="label">Open :</span>
                        <input class="timepicker tp-open" id="th_open" width="100" readonly name="thu_open" v-model="portal.thu_open" v-timepicker="'thu_open'" />
                    </div>
                    <div class="openclose">
                        <span class="label">Closed :</span>
                        <input class="timepicker tp-close" id="th_close" width="100" readonly name="thu_close" v-model="portal.thu_close"  v-timepicker="'thu_close'" />
                    </div>
                </div>
            </div>

            <div class="time-frame">
                <h5 class="text-center">Friday</h5>

                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="friday_times_" value="0">
                        <input class="24_hours_open" type="checkbox" value="2" id="friday_times" name="fri_closed" @change="openPortal('friday')" :checked="portal.fri_closed === 2" />
                        <label for="friday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="friday_times">Open 24 hours</label>
                </div>
                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="cfriday_times_" value="0">
                        <input class="closed_24_hour" type="checkbox" value="1" name="fri_closed" id="cfriday_times" @change="closePortal('friday')" :checked="portal.fri_closed === 1" />
                        <label for="cfriday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="cfriday_times">Closed</label>
                </div>
                <div class="friday_times cfriday_times" v-show="!portal.fri_closed">
                    <div class="openclose">
                        <span class="label">Open :</span>
                        <input class="timepicker tp-open" id="f_open" width="100" readonly name="fri_open" v-model="portal.fri_open" v-timepicker="'fri_open'" />
                    </div>
                    <div class="openclose">
                        <span class="label">Closed :</span>
                        <input class="timepicker tp-close" id="f_close" width="100" readonly name="fri_close" v-model="portal.fri_close"  v-timepicker="'fri_close'" />
                    </div>
                </div>
            </div>

            <div class="time-frame">
                <h5 class="text-center">Saturday</h5>

                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="saturday_times_" value="0">
                        <input class="24_hours_open" type="checkbox" value="2" id="saturday_times" name="sat_closed" @change="openPortal('saturday')" :checked="portal.sat_closed === 2" />
                        <label for="saturday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="saturday_times">Open 24 hours</label>
                </div>
                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="csaturday_times_" value="0">
                        <input class="closed_24_hour" type="checkbox" value="1" name="sat_closed" id="csaturday_times" @change="closePortal('saturday')" :checked="portal.sat_closed === 1" />
                        <label for="csaturday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="csaturday_times">Closed</label>
                </div>
                <div class="saturday_times csaturday_times" v-show="!portal.sat_closed">
                    <div class="openclose">
                        <span class="label">Open :</span>
                        <input class="timepicker tp-open" id="s_open" width="100" readonly name="sat_open" v-model="portal.sat_open" v-timepicker="'sat_open'" />
                    </div>
                    <div class="openclose">
                        <span class="label">Closed :</span>
                        <input class="timepicker tp-close" id="s_close" width="100" readonly name="sat_close" v-model="portal.sat_close"  v-timepicker="'sat_close'" />
                    </div>
                </div>
            </div>

            <div class="time-frame">
                <h5 class="text-center">Sunday</h5>

                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="sunday_times_" value="0">
                        <input class="24_hours_open" type="checkbox" value="2" id="sunday_times" name="sun_closed" @change="openPortal('sunday')" :checked="portal.sun_closed === 2" />
                        <label for="sunday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="sunday_times">Open 24 hours</label>
                </div>
                <div class="custom-check d-flex">
                    <div class="round mr-1">
                        <input type="hidden" id="csunday_times_" value="0">
                        <input class="closed_24_hour" type="checkbox" value="1" name="sun_closed" id="csunday_times" @change="closePortal('sunday')" :checked="portal.sun_closed === 1" />
                        <label for="csunday_times"></label>
                    </div>
                    <label class="ml-2 text-white" for="csunday_times">Closed</label>
                </div>
                <div class="sunday_times csunday_times" v-show="!portal.sun_closed">
                    <div class="openclose">
                        <span class="label">Open :</span>
                        <input class="timepicker tp-open" id="sun_open" width="100" readonly name="sun_open" v-model="portal.sun_open" v-timepicker="'sun_open'" />
                    </div>
                    <div class="openclose">
                        <span class="label">Closed :</span>
                        <input class="timepicker tp-close" id="sun_close" width="100" readonly name="sun_close" v-model="portal.sun_close"  v-timepicker="'sun_close'" />
                    </div>
                </div>
            </div>

            <input type="hidden" id="timezone" name="timezone" v-model="portal.timezone">

            <div class="custom-check d-flex">
                <div class="round mr-1">
                    <input type="checkbox" value="1" name="old_condition" id="old_condition" checked />
                    <label for="old_condition"></label>
                </div>
                <label class="ml-2 text-white" for="old_condition">Are you 21+ Years Old or Older?</label>
            </div>
            <div class="custom-check d-flex">
                <div class="round mr-1">
                    <input type="checkbox" value="1" name="terms_condition" id="terms_condition" checked />
                    <label for="terms_condition"></label>
                </div>
                <label class="ml-2 text-white" for="terms_condition">Accept</label><a href="javascript:;" style="color: #EFA720;font-size:16px;margin-left:4px;" data-toggle="modal" data-target="#user_agreement_modal">User Agreement</a>
            </div>

            <div class="submit text-center">                        
                <button type="button" class="btn btn-primary btn-block mb-3 mt-5" disabled v-if="loading">
                    <div class="spinner spinner-border text-white" role="status"></div>
                </button>
                <button type="submit" class="btn btn-primary btn-block mb-3 mt-5" v-else>Post</button>
            </div>
        </form>
    </div>
</template>

<script>
    import _ from 'lodash';
    var tzlookup = require("tz-lookup");
    if(process.client) {
        require('gijgo');
        require('gijgo/css/gijgo.min.css');
    }
    export default {
        name: 'AddPortal',
        data : function(){
            return {
                portal: {
                    name: '',
                    username: '',
                    store_type: '',
                    state_license: '',
                    suite: '',
                    recreational: false,
                    medical: false,
                    phone_number: '',
                    email: '',
                    latitude: '',
                    longitude: '',
                    address: '',
                    city: '',
                    state: '',
                    postal: '',
                    description: '',
                    website_url: '',
                    facebook_url: '',
                    twitter_url: '',
                    instagram_url: '',
                    youtube_url: '',
                    atm: false,
                    security: false,
                    mon_open: null,
                    mon_close: null,
                    mon_closed: null,
                    tue_open: null,
                    tue_close: null,
                    tue_closed: null,
                    wed_open: null,
                    wed_close: null,
                    wed_closed: null,
                    thu_open: null,
                    thu_close: null,
                    thu_closed: null,
                    fri_open: null,
                    fri_close: null,
                    fri_closed: null,
                    sat_open: null,
                    sat_close: null,
                    sat_closed: null,
                    sun_open: null,
                    sun_close: null,
                    sun_closed: null,
                },
                pa_logourl : '',
                profileMedia:  '',
                loading: false,
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
                map_center : {lat: 0, lng: 0},
                upload_url: process.env.serverUrl + '/api/media/upload',
                files: [],
                uploadProgress: 5,
                uploading: false,
            }
        },
        created() {
            if(process.client) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(this.setPosition);
                } else {
                    alert(
                        "Geolocation is not supported by this browser. \n Please enable it."
                    );
                }  
            }
        },
        mounted() {
            this.init();
            var self = this;
            $("#ap-description").emojioneArea({
                pickerPosition: "top",
                search: false,
                autocomplete: false,
                placeholder: " ",
                events: {
                    blur: function (editor, event) {
                        let description = $("#ap-description").data('emojioneArea').getText();
                        console.log(description);
                        self.portal.description = description;
                        if(description == '') {
                            $("#ap-description").siblings('label').removeClass('floating');
                        }
                    },
                    focus: function (editor, event) {
                        $("#ap-description").siblings('label').addClass('floating');
                    }
                }
            });
        },
        methods: {
            setPosition(position) {
                this.map_center.lat = this.portal.latitude = position.coords.latitude;
                this.map_center.lng = this.portal.longitude = position.coords.longitude;                 
            },
            init() {
                this.makeInitialTextReadOnly(document.getElementById('website'));
                this.makeInitialTextReadOnly(document.getElementById('facebook'));
                this.makeInitialTextReadOnly(document.getElementById('twitter'));
                this.makeInitialTextReadOnly(document.getElementById('instagram'));
                this.makeInitialTextReadOnly(document.getElementById('youtube'));
            },
            updateCoordinates:_.debounce(function(event) {
                this.portal.latitude = event.latLng.lat();
                this.portal.longitude = event.latLng.lng();
                this.setTimezone();
                this.getAddressFromCoordinates()
            }, 500),
            getCompanyAddress(addressData, placeResultData, id) {
                console.log(addressData);
            },
            openPortal(day) {
                console.log(this.portal);
                switch (day) {
                    case 'monday':
                        this.portal.mon_closed = this.portal.mon_closed == 2 ? 0 : 2;
                        break;
                    case 'tuesday':
                        this.portal.tue_closed = this.portal.tue_closed == 2 ? 0 : 2;
                        break;
                    case 'wednesday':
                        this.portal.wed_closed = this.portal.wed_closed == 2 ? 0 : 2;
                        break;
                    case 'thursday':
                        this.portal.thu_closed = this.portal.thu_closed == 2 ? 0 : 2;
                        break;
                    case 'friday':
                        this.portal.fri_closed = this.portal.fri_closed == 2 ? 0 : 2;
                        break;
                    case 'saturday':
                        this.portal.sat_closed = this.portal.sat_closed == 2 ? 0 : 2;
                        break;
                    case 'sunday':
                        this.portal.sun_closed = this.portal.sun_closed == 2 ? 0 : 2;
                        break;
                    default:
                        break;
                }
            },
            closePortal(day) {
                switch (day) {
                    case 'monday':
                        this.portal.mon_closed = this.portal.mon_closed == 1 ? 0 : 1;
                        break;
                    case 'tuesday':
                        this.portal.tue_closed = this.portal.tue_closed == 1 ? 0 : 1;
                        break;
                    case 'wednesday':
                        this.portal.wed_closed = this.portal.wed_closed == 1 ? 0 : 1;
                        break;
                    case 'thursday':
                        this.portal.thu_closed = this.portal.thu_closed == 1 ? 0 : 1;
                        break;
                    case 'friday':
                        this.portal.fri_closed = this.portal.fri_closed == 1 ? 0 : 1;
                        break;
                    case 'saturday':
                        this.portal.sat_closed = this.portal.sat_closed == 1 ? 0 : 1;
                        break;
                    case 'sunday':
                        this.portal.sun_closed = this.portal.sun_closed == 1 ? 0 : 1;
                        break;
                    default:
                        break;
                }
            },
            updateAddress(place){
                this.portal.latitude = place.geometry['location'].lat();
                this.portal.longitude = place.geometry['location'].lng();
                this.setTimezone();

                for (var i = 0; i < place.address_components.length; i++) {
                    for (var j = 0; j < place.address_components[i].types.length; j++) {
                        if (place.address_components[i].types[j] === "postal_code") {
                            this.portal.postal = place.address_components[i].long_name;
                        }
                        if (place.address_components[i].types[j] === "administrative_area_level_1") {
                            this.portal.state = place.address_components[i].short_name;
                        }
                        if (place.address_components[i].types[j] === "locality") {
                            this.portal.city = place.address_components[i].long_name;
                        }
                    }
                }
                this.portal.address = place.name;
            },
            setTimezone() {
                this.portal.timezone = tzlookup(this.portal.latitude, this.portal.longitude);
            },
            async savePortal() {

                let url = `/portals/store`;
                const form_data = new FormData($("#pa_addform")[0]);
                console.log(form_data);
                this.loading = true;
                const { data } = await this.axios.post(url, form_data);
                this.loading = false;
                if(data.status == 200) {
                    // Log in the user.
                    const { data: { token } } = await this.axios.post('/login', {username: this.portal.username, password: this.portal.password})
                    // Save the token.
                    this.$store.dispatch('auth/saveToken', { token })
                    // Update the user.
                    await this.$store.dispatch('auth/updateUser', { user: data })
                    // Redirect home.
                    window.location.href = "/" + this.portal.username;
                }
            },
            makeInitialTextReadOnly(input) {
                var readOnlyLength;
                var type;
                input.addEventListener('keydown', function(event) {
                    var which = event.which;
                    if (((which == 8) && (input.selectionStart <= readOnlyLength)) ||
                        ((which == 46) && (input.selectionStart < readOnlyLength))) {
                        event.preventDefault();
                    }
                });
                input.addEventListener('click', function(event) {
                    if(input.value !== '') {
                        type = 1;

                        switch (input.id) {
                            case "website":
                                readOnlyLength = 7;
                                break;

                            case "facebook":
                                readOnlyLength = 13;
                                break;

                            case "twitter":
                                readOnlyLength = 12;
                                break;

                            case "instagram":
                                readOnlyLength = 14;
                                break;

                            case "youtube":
                                readOnlyLength = 17;
                                break;

                            default:
                                break;
                        }
                        return;
                    }
                    input.value = input.dataset.value;
                    readOnlyLength = input.value.length;
                    // console.log('readonly length is', input);
                });
                input.addEventListener('blur', function(event) {
                    if (readOnlyLength === input.value.length) {
                        input.value = "";
                    }
                });
                input.addEventListener('keypress', function(event) {
                    var which = event.which;

                    if ((event.which != 0) && (input.selectionStart < readOnlyLength)) {
                        event.preventDefault();
                    }
                });
            },
            getAddressFromCoordinates() {
                const lat = this.portal.latitude;
                const lon = this.portal.longitude;
                let url = `https://cors-anywhere.herokuapp.com/https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lon}&sensor=true&key=AIzaSyAs_LAuZVVqNTEdv765oUp6arI5Tjxs43s`;
                let config = {
                        headers : {
                            'Access-Control-Allow-Origin': '*',
                        }
                    }
                this.axios.get(url, config).then(response => {
                    let add_components = response.data.results;
                    add_components.forEach(element => {
                        for (var i = 0; i < element.address_components.length; i++) {
                            for (var j = 0; j < element.address_components[i].types.length; j++) {
                                if (element.address_components[i].types[j] === "postal_code") {
                                    this.portal.postal = element.address_components[i].long_name;
                                }
                                if (element.address_components[i].types[j] === "administrative_area_level_1") {
                                    this.portal.state = element.address_components[i].short_name;
                                }
                                if (element.address_components[i].types[j] === "locality") {
                                    this.portal.city = element.address_components[i].long_name;
                                }
                            }
                        }
                    });
                });
            },
            serverUrl(item) {
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + '/imgs/default.png';
                }
            },
            
            inputFile(newFile, oldFile){
                let _this = this;
                this.$refs.upload.active = true;
                if (newFile && oldFile) {
                    if (newFile.active !== oldFile.active) {
                        this.uploading = true
                    }
                    if (newFile.progress !== oldFile.progress) {
                        this.uploadProgress = newFile.progress
                    }
                    // Uploaded error
                    if (newFile.error !== oldFile.error) {
                        alert('Sorry, upload is failed. Please try again');
                    }
                    // Uploaded successfully
                    if (newFile.success !== oldFile.success) {
                        setTimeout(function(){
                            _this.uploading = false;
                            _this.pa_logourl = newFile.response.fileurl;
                            _this.profileMedia = process.env.serverUrl + newFile.response.fileurl;
                        }, 1000);
                    }
                }
            },
            inputFilter: function (newFile, oldFile, prevent) {
                if (newFile && !oldFile) {
                    // Filter non-image file
                    if (!/\.(jpeg|jpg|gif|png|webp)$/i.test(newFile.name)) {
                        return prevent()
                    }
                }
            },
        },
        directives: {
            timepicker: {
                inserted: function (el, binding, vnode) {
                    let timepickerconf = { value: el.value, modal: true, header: true, footer: true, mode: 'ampm', format: 'hh:MM TT' };
                    $(el).timepicker(
                        $.extend(timepickerconf, {
                            change: function (e) {
                                vnode.context.portal[binding.value] = e.target.value;
                            }
                        })
                    );
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    input, select, textarea {font-size: 16px;}
    label {font-size : 15px;}
    .popup-activate {
        // Floating
        .floating-label {
            position: relative;
        }
        .floating-input {
            padding: 4px 2px;
            display: block;
            width: 100%;
            background-color: transparent;
            border: none;
            box-shadow: unset;
        }

        .floating-label label {
            position: absolute !important;
            pointer-events: none;
            left: 0px;
            top: 30%;
            transition: 0.2s ease all;
            -moz-transition: 0.2s ease all;
            -webkit-transition: 0.2s ease all;
        }

        .floating-input:not(:placeholder-shown) ~ label,
        .floating-input:focus ~ label {
            top: -15px;
            color: gray;
        }
    }
</style>


<style lang="scss">
    .floating-label div.emojionearea {
        background-color: transparent !important;
        border: none !important;
        border-bottom: 1px solid #fff !important;
        border-radius: 0 !important;
        height: 60px !important;
    }
    #pa_addform .emojionearea-editor {
        white-space: normal !important;
    }
    .emojionearea.focused {
        box-shadow: unset !important;
    }
    .gj-modal {
        z-index: 20000 !important;
    }
    .edit_portal .vs-popup {
        width: 450px !important;
    }
    .edit_portal .vs-popup .vs-popup--content {
        max-height: calc(100vh - 170px);
        margin: 0 !important;
        width: 100%;
        padding-top: 0;
    }
    @media screen and (max-width: 600px) {
        .edit_portal .vs-popup .vs-popup--content {
            max-height: calc(100vh - 30px);
        }
        .edit_portal.con-vs-popup .vs-popup {
            max-width: unset;
            max-height: unset;
            margin: 0;
        }
    }
    .pac-container {
        z-index: 20000;
    }
    .portal_addpage {
        .emojionearea-button {
            top: 7px;
            div {
                background-image: url(https://i.imgur.com/xljqgrH.png) !important;
            }
        } 
    }
</style>