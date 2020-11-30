<template>
    <div class="company-coupon">
        <client-only>
            <div class="coupon-form" v-if="auth_user" v-show="!coupon || is_edit">
                <form action="" method="POST" class="mt-3" id="form_coupon" @submit.prevent="is_edit ? updateCoupon($event) : createCoupon($event)" enctype="multipart/form-data">
                    <div class="text-center" style="position:relative;">
                        <span class="btn-form-close" @click="closeForm()" v-if="is_edit"><i class="far fa-times-circle"></i></span>
                        <label class="mt-2 upload__label" for="postfile">
                            <div class="logo" for="coupon_media_select">
                                <img class="signup_logo" :src="coupon_media" v-show="coupon_media" />
                            </div>
                        </label>
                        <div style="width:50%;margin:auto;">
                            <div class="pa_progress progress d-block" style="height:10px;" v-if="uploading">
                                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" :style="{width: uploadProgress + '%', height: '10px'}"></div>
                            </div>
                        </div>

                        <file-upload
                            name="postfile"
                            class="text-420"
                            extensions="gif,jpg,jpeg,png,webp"
                            accept="image/png,image/gif,image/jpeg,image/webp"
                            ref="upload"
                            v-model="files"
                            :post-action="upload_url"
                            @input-file="inputFile"
                            @input-filter="inputFilter"
                        >{{coupon_media ? 'Change Media' : 'Add Media'}}</file-upload>
                    </div>
                    <div class="form-group floating-label">
                        <select name="category" class="form-control floating-select category" required @change="getStrain($event)" v-model="coupon_form.category_id">
                            <option value="" hidden></option>
                            <option :value="item.id" v-for="(item, index) of categories" :key="index">
                                {{item.price_type == 2 ? 'Flower - ' + item.name : item.name}}
                            </option>
                        </select>
                        <label for="category_add">Category</label>
                    </div>
                    <div class="form-group floating-label mt-4" v-if="selected_category">
                        <input type="text" name="brand_name" class="form-control floating-input brand_name" placeholder=" " v-model="coupon_form.brand_name" />
                        <label>Brand</label>
                    </div>
                    <div class="form-group floating-label mt-4" v-show="selected_category">
                        <textarea class="floating-textarea description" name="description" rows="2" id="coupon_description" maxlength="75" v-model="coupon_form.description"></textarea>
                        <label :class="{focused : coupon_form.description}">Description</label>
                    </div>
                    <div class="form-group floating-label mt-4" v-if="selected_category" v-show="selected_category.type == 'strain_selectable'">
                        <multiselect
                            ref="strain_multiselect"
                            v-model="selected_strain"
                            class="floating-label strain_add"
                            open-direction="bottom"
                            id="strain_add"
                            :options="strains"
                            label="name"
                            name="strain"
                            track-by="id"
                            placeholder=" "
                            :show-labels="false"
                            @select="selectedStrain"
                        >
                            <span slot="noResult" @click="setNewStrain()">Not Listed in our database. Click <span class="text-420">here</span>.</span>
                        </multiselect>
                        <label for="strain_add" :class="{focused : selected_strain}">Tag Marijuana Strain</label>
                    </div>                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" style="width: 120px;font-size:18px">Post</button>
                    </div>
                </form>
            </div>
        </client-only>
        <div class="coupon-detail result-container" v-if="coupon && !is_edit">
            <div class="result-media">
                <div class="media">
                    <img :src="serverUrl(coupon.media.url)" v-if="coupon.media && coupon.media.type == 'image'" alt="" />
                    <img :src="serverUrl(portal.media.url)" v-else-if="portal.media && portal.media.type == 'image'" alt="" />
                    <img src="/imgs/default.png" v-else alt="" />
                </div>
            </div>
            <div class="result-data">
                <p class="category-strain my-0">
                    <span class="category">{{coupon.category.price_type == 2 ? 'Flower | ' + coupon.category.name : coupon.category.name}}</span>
                    <span class="strain" v-if="coupon.strain"> | {{coupon.strain.name}}</span>
                </p>
                <h4 class="title">{{coupon.description}}</h4>
                <p class="sub-title my-0">by: {{coupon.brand_name}}</p>
                <div class="company-name">
                    <img class="store-type-img" src="/imgs/dispensary.png" alt v-if="portal.store_type == 1 || portal.store_type == 3" />
                    <img class="store-type-img" src="/imgs/delivery.png" alt v-if="portal.store_type == 2 || portal.store_type == 3" />
                    &nbsp;
                    {{portal.name}}
                </div>
                <div class="company-status">
                    <!-- <span class="shop_status open" v-if="portal.shop_status == 2">Open</span>
                    <span class="shop_status closed" v-else>Closed</span>
                    <span class="distance">{{distance}} Miles</span> -->
                </div>
                <h5 class="expire_date mt-2">Expires: {{new Date().toLocaleDateString()}}</h5>
            </div>
        </div>
        <div class="text-center" v-if="auth_user && coupon && !is_edit && (portal.id == auth_user.id || auth_user.id == 1)">
            <button class="btn btn-primary mr-3" @click="edit()">Edit</button>
            <button class="btn btn-420" @click="deleteCoupon()">Delete</button>
        </div>
    </div>
</template>

<script>
    import Multiselect from "vue-multiselect";
    import { mapGetters } from "vuex";
    export default {
        name: 'PortalCoupon',
        props: ['portal', 'distance'],
        components : {
            Multiselect,
        },
        data() {
            return {
                coupon_form: {
                    id: null,
                    portal_id: this.portal.id,
                    category_id: null,
                    strain_id: null,
                    media_id: null,
                    media_url: '',
                    brand_name: '',
                    description: '',
                    expire_date: new Date().toLocaleDateString(),
                },
                coupon: this.portal.coupon,
                coupon_media: '',
                is_edit: false,
                categories: [],
                selected_category: null,
                strains: [],
                selected_strain: null,
                media_progress: 0,
                    
                upload_url: process.env.serverUrl + '/api/media/upload',
                files: [],
                uploadProgress: 5,
                uploading: false,
            };
        },
        watch : {
            portal : function(newPortal, oldPortal){
                this.coupon = newPortal.coupon;
                this.coupon_form.portal_id = newPortal.id;
                this.loadCategories();
            }
        },
        computed: mapGetters({
            auth_user: 'auth/user',
        }),
        mounted: function() {
            this.loadCategories();
            var _this = this;
        },
        methods: {
            loadCategories() {
                this.axios.post('/categories')
                .then(response => {
                    this.categories = response.data;
                });
            },
            edit() {
                this.is_edit = true;
                this.coupon_form = {
                    id: this.coupon.id,
                    portal_id: this.coupon.portal_id,
                    category_id: this.coupon.category_id,
                    strain_id: this.coupon.strain_id,
                    media_id: this.coupon.media_id,
                    media_url: this.coupon.media ? this.coupon.media.url : '',
                    brand_name: this.coupon.brand_name,
                    description: this.coupon.description,
                    expire_date: new Date().toLocaleDateString(),
                };
                this.selected_category = this.categories.find(cat => cat.id == this.coupon.category_id);
                let url = '/category/strains';
                let data = {id : this.selected_category.id};
                this.axios.post(url, data)
                    .then(response => {
                        this.strains = response.data;
                        this.selected_strain = this.strains.find(st => st.id == this.coupon.strain_id);
                    });
                if(this.portal.coupon.media) {
                    this.coupon_media = this.serverUrl(this.portal.coupon.media.url);
                }
            },
            initForm() {
                this.coupon_form = {
                    id: null,
                    portal_id: this.portal.id,
                    category_id: null,
                    strain_id: null,
                    media_id: null,
                    media_url: '',
                    brand_name: '',
                    description: '',
                    expire_date: new Date().toLocaleDateString(),
                };
            },
            getStrain(event) {
                let id = event.target.value;
                this.selected_category = this.categories.find(cat => cat.id == id);
                let url = '/category/strains';
                let data = {id : id};
                this.axios.post(url, data)
                    .then(response => {
                        this.strains = response.data;
                    });
            },
            createCoupon() {
                const uri = '/coupon/store';
                this.axios.post(uri, this.coupon_form)
                    .then(response => {
                        window.location.reload();
                    });
            },
            updateCoupon() {
                const uri = `/coupon/${this.coupon.id}`;
                this.axios.post(uri, this.coupon_form)
                    .then(response => {
                        window.location.reload();
                    });
            },
            deleteCoupon() {
                if(!window.confirm('Are you sure?')) {
                    return false;
                }
                const uri = `/coupon/${this.coupon.id}`;
                this.axios.delete(uri)
                    .then(response => {
                        window.location.reload();
                    });
            },
            closeForm() {
                this.is_edit = false;
            },
            removeMedia() {
                this.coupon_media = '';
                this.coupon.media_id = '';
            },
            selectedStrain(option) {
                this.coupon_form.strain_id = option.id;
            },
            inputFile(newFile, oldFile){
                let _this = this;
                this.$refs.upload.active = true;
                if (newFile && oldFile) {
                    if (newFile.active !== oldFile.active) {
                        this.uploading = true
                    }
                    if (newFile.progress !== oldFile.progress) {
                        // console.log('progress', newFile.progress)
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
                            _this.coupon_form.media_url = newFile.response.fileurl,
                            _this.coupon_media = process.env.serverUrl + newFile.response.fileurl;
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
            serverUrl(item) {
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + 'imgs/default.png';
                }

                $("img[title='India']").parents('li.project').hide();
            }
        }
    }
</script>
<style lang="scss" scoped>
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
                    font-size: 15px;
                    margin-top: 0;
                    margin-bottom: 0;
                }
                .title {
                    color: #EFA720;
                    font-size: 18px;
                    margin-bottom: 0;
                }
                .sub-title {
                    margin-left: 8px;
                    color: gray;
                    font-size: 15px;
                }
                .company-name {
                    color: #EFA720;
                    font-size: 18px;
                    display: flex;
                    align-items: center;
                    margin-top: 8px;
                    .store-type-img {
                        width: 22px;
                    }
                }
                .company-status {
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
                        font-size: 15px;
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
                    .category-strain {
                        font-size: 14px;
                    }
                    .title {
                        font-size: 16px;
                    }
                    .sub-title {
                        font-size: 14px;
                    }
                    .company-name {
                        font-size: 16px;
                    }
                    .company-status {
                        margin-top: 2px;
                        .distance {                            
                            font-size: 14px;
                        } 
                    }
                }
            }
        }
    }
</style>

<style lang="scss" scoped>
    .logo {
        height: 160px;
        width: 160px;
        background: url(/imgs/strains/template.png) center;
        background-size: cover;
        padding: 10px;
        margin: auto;
        border-radius: 100%;
        cursor: pointer;

        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 100%;
        }

        .companies__select .multiselect {
            color: #fff;
        }

        .companies__select .multiselect__tags {
            background-color: transparent !important;
        }
    }

    // Floating
    .floating-label {
        position: relative;
        margin-bottom: 22px;
    }
    .floating-input,
    .floating-select {
        font-size: 16px;
        padding: 4px 2px;
        display: block;
        width: 100%;
        background-color: transparent;
        border: none;
        box-shadow: unset;
    }

    .multi-select__label {
        position: absolute;
        z-index: 1;
        top: 10px;
    }
    .multi-select__label.float-label {
        top: -13px;
        color: gray;
    }

    .floating-label label {
        font-size: 16px;
        position: absolute !important;
        pointer-events: none;
        // left: 5px;
        top: 30%;
        transition: 0.2s ease all;
        -moz-transition: 0.2s ease all;
        -webkit-transition: 0.2s ease all;
    }

    label.focused,
    .floating-input:focus ~ label,
    .floating-select:focus ~ label,
    .floating-input:not(:placeholder-shown) ~ label,
    .floating-textarea.focused ~ label,
    .multiselect--active ~ label,
    .floating-textarea ~ label.focused,
    .floating-select:not([value='']):valid ~ label {
        top: -15px;
        color: gray;
    }

    /* active state */
    .floating-input:focus ~ .bar:before,
    .floating-input:focus ~ .bar:after,
    .floating-select:focus ~ .bar:before,
    .floating-select:focus ~ .bar:after {
        width: 50%;
    }

    .floating-textarea {
        min-height: 40px;
        max-height: 260px;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .btn-form-close {
        color: #EFA720;
        position: absolute;
        right: 17px;
        font-size: 23px;
    }
    .page-data {
        @media (max-width: 600px) {
            padding-top: 55px;
        }
    }
</style>

<style lang="css">
    .floating-label div.emojionearea {
        background-color: transparent !important;
        border: none !important;
        border-bottom: 1px solid #fff !important;
        border-radius: 0 !important;
        height: 60px !important;
    }
    #form_menu .emojionearea-editor {
        font-size: 16px;
        white-space: normal !important;
    }
    .emojionearea.focused {
        box-shadow: unset !important;
    }
    .strains__popup .vs-popup {
        width: 620px !important;
        min-height: 500px;
    }

    .multiselect__tags {
        background: transparent !important;
        border-radius: 0;
        border: none;
        border-bottom: solid 1px white;
        padding-left: 0px;
    }
    .multiselect__tags .multiselect__single {
        background : transparent !important;
        color: white;
    }
    .multiselect__input {
        border: none !important;
        padding-bottom: 0;
        background: transparent !important;
    }

    .multiselect__content-wrapper {
        background: #000 !important;
        color: white;
    }
    .multiselect__option--highlight {
        background-color: #efa720 !important;
    }
    
</style>
