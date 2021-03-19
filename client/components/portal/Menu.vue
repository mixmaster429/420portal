<template>
    <div id="menuForm">
        <div v-if="$device.isMobile" class="btn-close-popup">
            <fa @click="closeMenu()" icon="times" class="mr-2"></fa> 
            <h5 class="page-title">
                Menu
                <img src="/imgs/search_option.png" width="25" @click="show_filter = !show_filter">
                <span class="results-count" @click="show_filter = false;">{{menus.length}} Results</span>
            </h5>
        </div>
        <div class="page-data">            
            <button class="btn btn-block btn-lg btn-primary mt-1 mt-md-3" v-if="portal.id === auth_user_id || auth_user_id == 1" @click="addMenu">Add Menu Item</button>
            <hr class="mb-0" style="background-color : #EFA720; height: 3px;" v-if="portal.id === auth_user_id || auth_user_id == 1">
            <form action="" method="POST" class="mt-3" id="form_menu" v-show="is_open" @submit.prevent="formSubmit()" enctype="multipart/form-data">
                <input type="hidden" name="portal_id" :value="portal.id" />
                <input type="hidden" name="menu_id" :value="menu_id" />
                <div class="text-center" style="position:relative;">
                    <span class="btn-form-close" @click="closeForm"><fa icon="times"></fa></span>
                    <label class="mt-2 upload__label" for="menu_media">
                        <video id="menu_media_video" v-if="mediaType === 'video'" :src="mediaData" width="320" height="240" controls disablepictureinpicture controlslist="nodownload">
                            <source v-bind:src="mediaData" type="video/mp4" />
                            <source v-bind:src="mediaData" type="video/webm" />
                            <source v-bind:src="mediaData" type="video/ogg" />Your browser does not support the video tag.
                        </video>
                        <div class="logo" for="menu_media" v-else>
                            <img class="signup_logo" :src="mediaData" alt="" v-show="mediaData" />
                        </div>
                        <p style="color: #EFA720;font-size: 15px;cursor:pointer;">{{mediaData ? 'Change Media' : 'Add Media'}}</p>
                    </label>
                    <span class="btn-remove-media" @click="removeMedia()" v-if="is_editting && mediaData"><fa icon="times-circle"></fa></span>
                    <input type="hidden" name="remove_media" :value="remove_media" />
                    <input type="file" hidden id="menu_media" name="file"  class="btn-file" @change="previewImage" accept="image/*|video/*" />
                </div>
                <div class="form-group floating-label">
                    <select name="category" class="form-control floating-select category" required @change="getStrain($event)" v-model="category_id">
                        <option value="" hidden></option>
                        <option :value="item.id" v-for="(item, index) of category" :key="index">{{ item.name | category_name }}</option>
                    </select>
                    <label for="category_add" :class="{focused : category_id}">Category</label>
                </div>
                <div class="form-group floating-label mt-4" v-show="selected_category.id">
                    <input type="text" name="item_name" class="form-control floating-input item_name" placeholder=" " maxlength="50" v-model="item_name" />
                    <label>Item Name</label>
                </div>
                <div class="form-group floating-label mt-4" v-show="selected_category.type == 'strain_selectable'">
                    <multiselect
                        ref="strain_multiselect"
                        v-model="strain"
                        class="floating-label strain_add"
                        id="strain_add"
                        :options="taggedStrains"
                        label="name"
                        name="strain"
                        track-by="id"
                        placeholder=" "
                        :show-labels="false"
                        @select="selectStrain"
                        @search-change="asyncFind($event)"
                    >
                        <span slot="noResult" @click="setNewStrain()">Not Listed in our database. Click <span class="text-420">here</span>.</span>
                    </multiselect>
                    <label for="strain_add" :class="{focused : strain.name}">Tag Marijuana Strain</label>
                </div>
                
                <div class="form-group floating-label mt-4" v-show="selected_category.id">
                    <textarea class="form-control floating-textarea description" name="description" rows="2" id="description_add" v-model="description"></textarea>
                    <label>Description</label>
                </div>
                <div class="form-group floating-label mt-4" v-if="selected_category.price_type >= 0">
                    <input type="number" name="price_each" class="form-control floating-input price_each" placeholder=" " v-model="price_each" />
                    <label for="price_each_add">$ Each</label>
                </div>
                <div class="form-group floating-label mt-4" v-if="selected_category.price_type == 1">
                    <input type="number" name="price_half_gram" class="form-control floating-input price_gram" placeholder=" " v-model="price_half_gram" />
                    <label for="price_half_gram_add">$ 1/2 G</label>
                </div>
                <div class="form-group floating-label mt-4" v-if="selected_category.price_type == 2 || selected_category.price_type == 1">
                    <input type="number" name="price_gram" class="form-control floating-input price_gram" placeholder=" " v-model="price_gram" />
                    <label for="price_gram_add">$ G</label>
                </div>
                <div class="form-group floating-label mt-4" v-if="selected_category.price_type == 2">
                    <input type="number" name="price_eighth" class="form-control floating-input price_eighth" placeholder=" " v-model="price_eighth" />
                    <label for="price_eighth_add">$ 1/8</label>
                </div>
                <div class="form-group floating-label mt-4" v-if="selected_category.price_type == 2">
                    <input type="number" name="price_quarter" class="form-control floating-input price_quarter" placeholder=" " v-model="price_quarter" />
                    <label for="price_quarter_add">$ 1/4</label>
                </div>
                <div class="form-group floating-label mt-4" v-if="selected_category.price_type == 2">
                    <input type="number" name="price_half_oz" class="form-control floating-input price_half_oz" placeholder=" " v-model="price_half_oz" />
                    <label for="price_half_oz_add">$ 1/2 Oz</label>
                </div>
                <div class="form-group floating-label mt-4" v-if="selected_category.price_type == 2">
                    <input type="number" name="price_oz" class="form-control floating-input price_oz" placeholder=" " v-model="price_oz" />
                    <label for="price_oz">$ Oz</label>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" style="width: 120px;font-size:18px">Post</button>
                </div>
                <hr class="mb-0" style="background-color : #EFA720; height: 3px;" v-if="auth_user && (portal.id === auth_user_id || auth_user_id == 1)">
            </form>
            <div class="section-search" v-if="$device.isMobile">
                <div class="search-container" v-show="show_filter">
                    <div class="container-fluid search-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="custom-control custom-radio filter-input" style="padding-top: 3px;" v-for="(item, c_index) in category" :key="c_index" v-show="getCategoryResults(item)">
                                    <input type="radio" class="custom-control-input" name="category" :id="'menu_category_' + item.slug" :value="item.id" v-model="menu_filter.category_id" @change="menu_filter.selected_category = item" />
                                    <label class="custom-control-label" :for="'menu_category_' + item.slug">{{item.price_type == 2 ? 'Flower - ' + item.name : item.name}}<span class="category-count"> ({{getCategoryResults(item)}})</span></label>
                                </div>                              
                            </div>
                            <div class="col-6">
                                <div class="pt-5">
                                    <div class="form-group floating-label" v-if="menu_filter.category_id">
                                        <select class="form-control floating-select mt-2" id="mobile_weight_amount" v-model="menu_filter.price_type">
                                            <option value="price_each">Each</option>
                                            <option value="price_half_gram" v-show="menu_filter.selected_category.price_type == 1">0.5g</option>
                                            <option value="price_gram" v-show="menu_filter.selected_category.price_type > 0">g</option>
                                            <option value="price_eighth" v-show="menu_filter.selected_category.price_type == 2">1/8</option>
                                            <option value="price_quarter" v-show="menu_filter.selected_category.price_type == 2">1/4</option>
                                            <option value="price_half_oz" v-show="menu_filter.selected_category.price_type == 2">1/2</option>
                                            <option value="price_oz" v-show="menu_filter.selected_category.price_type == 2">oz</option>
                                        </select>
                                        <label for="mobile_weight_amount" :class="{focused : menu_filter.price_type}">Weight Amount</label>
                                    </div>
                                    <div class="form-group floating-label" v-if="menu_filter.category_id && menu_filter.price_type">
                                        <input type="number" min="0"
                                            class="form-control floating-input mt-2"
                                            id="mobile_max_price"
                                            placeholder=" "
                                            v-model="menu_filter.price_max"
                                        />
                                        <label for="mobile_max_price">Max $</label>
                                    </div>

                                    <h5 class="mt-3"><a href="javascript:;" class="text-420" @click="resetSearchOptions()">Reset Options</a></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu-panel mt-3" v-show="!$device.isMobile || ($device.isMobile && !show_filter)">
                <div class="desktop-search" v-if="!$device.isMobile">
                    <h5 class="search-type">Filters</h5>
                    <div class="custom-control custom-radio filter-input" style="padding-top: 3px;" v-for="(item, c_index) in category" :key="c_index" v-show="getCategoryResults(item)">
                        <input type="radio" class="custom-control-input" name="category" :id="'menu_category_' + item.slug" :value="item.id" v-model="menu_filter.category_id" @change="menu_filter.selected_category = item" />
                        <label class="custom-control-label" :for="'menu_category_' + item.slug">{{item.price_type == 2 ? 'Flower - ' + item.name : item.name}}<span class="category-count"> ({{getCategoryResults(item)}})</span></label>
                    </div>
                    <h5 class="search-type" v-if="menu_filter.category_id">Weight</h5>
                    <div class="form-group floating-label" v-if="menu_filter.category_id">
                        <select class="form-control floating-select mt-2" style="padding-left: 0;" id="mobile_weight_amount" v-model="menu_filter.price_type">
                            <option value="price_each">Each</option>
                            <option value="price_half_gram" v-show="menu_filter.selected_category.price_type == 1">0.5g</option>
                            <option value="price_gram" v-show="menu_filter.selected_category.price_type > 0">g</option>
                            <option value="price_eighth" v-show="menu_filter.selected_category.price_type == 2">1/8</option>
                            <option value="price_quarter" v-show="menu_filter.selected_category.price_type == 2">1/4</option>
                            <option value="price_half_oz" v-show="menu_filter.selected_category.price_type == 2">1/2</option>
                            <option value="price_oz" v-show="menu_filter.selected_category.price_type == 2">oz</option>
                        </select>
                        <label for="mobile_weight_amount" :class="{focused : menu_filter.price_type}">Weight Amount</label>
                    </div>
                    <h5 class="search-type" v-if="menu_filter.category_id && menu_filter.price_type">Price</h5>
                    <div class="form-group floating-label" v-if="menu_filter.category_id && menu_filter.price_type">
                        <input type="number" min="0"
                            class="form-control floating-input mt-2"
                            id="mobile_max_price"
                            placeholder=" "
                            v-model="menu_filter.price_max"
                        />
                        <label for="mobile_max_price">Max $</label>
                    </div>

                    <h5 class="mt-3"><a href="javascript:;" class="text-420" @click="resetSearchOptions()">Reset Options</a></h5>
                </div>
                <div class="menu-list">
                    <template v-for="category of portal_categories" v-if="categoryMenu(category.id).length">
                        <h5 class="text-center mt-2 mb-0">{{category.price_type == 2 ? 'Flower - ' + category.name : category.name}}</h5>
                        <div class="menu-container" v-for="menu of categoryMenu(category.id)">
                            <div class="menu-media">
                                <div class="media" v-if="menu.media" @click="showDescriptionClick(menu)" @mouseover="showDescriptionHover(menu)" @mouseleave="menu.show_description = false">
                                    <img v-if="menu.media.type == 'image'" :src="serverUrl(menu.media.url)" width="125" height="125" alt="" />
                                    <video v-if="menu.media.type == 'video'" :src="serverUrl(menu.media.url)" width="125" height="125" disablepictureinpicture controlslist="nodownload">
                                        <source v-bind:src="serverUrl(menu.media.url)" type="video/mp4" />
                                        <source v-bind:src="serverUrl(menu.media.url)" type="video/webm" />
                                        <source v-bind:src="serverUrl(menu.media.url)" type="video/ogg" />Your browser does not support the video tag.
                                    </video>
                                    <img class="video__tag__mobile" v-if="menu.media.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                                </div>
                                <div class="media" @click="showDescriptionClick(menu)" @mouseover="showDescriptionHover(menu)" @mouseleave="menu.show_description = false" v-else>
                                    <img src="/imgs/default.png" width="125" height="125" alt="">
                                </div>
                                <p class="btn-group mb-0" v-if="portal.id === auth_user_id || auth_user_id == 1">
                                    <a href="javascript:;" @click="editMenu(menu)" class="btn-action" data-toggle="tooltip" title="Edit"><fa icon="edit" fixed-width></fa></a>
                                    <a href="javascript:;" @click="deleteMenu(menu.id)" class="btn-action" data-toggle="tooltip" title="Delete"><fa icon="trash-alt" fixed-width></fa></a>
                                    <a href="javascript:;" @click="deactiveMenu(menu.id, menu.is_active)" class="btn-action" data-toggle="tooltip" :title="menu.is_active ? 'Deactivate' : 'Activate'">
                                        <fa icon="ban" fixed-width v-if="menu.is_active == 1"></fa>
                                        <fa icon="check-circle" fixed-width v-else></fa>
                                    </a>
                                </p>
                                <div class="menu-description" v-if="menu.description && menu.show_description" @mouseover="menu.show_description = true" @mouseleave="menu.show_description = false">{{menu.description}}</div>
                            </div>
                            <div class="menu-data">
                                <p class="category-strain my-0">
                                    <span class="category">{{menu.category.price_type == 2 ? 'Flower | ' + menu.category.name : menu.category.name}}</span>
                                    <span class="strain" v-if="menu.strain"> | {{menu.strain.name}}</span>
                                </p>
                                <h4 class="item_name">{{menu.item_name}}</h4>
                                <p class="price-data mb-0">
                                    <span v-if="menu.price_each != null">{{menu.price_each}}<sup class="text-420">each</sup></span>
                                    <span v-if="menu.price_half_gram != null">{{menu.price_half_gram}}<sup class="text-420">1/2g</sup></span>
                                    <span v-if="menu.price_gram != null">{{menu.price_gram}}<sup class="text-420">g</sup></span>
                                    <span v-if="menu.price_eighth != null">{{menu.price_eighth}}<sup class="text-420">1/8</sup></span>
                                    <span v-if="menu.price_quarter != null">{{menu.price_quarter}}<sup class="text-420">1/4</sup></span>
                                    <span v-if="menu.price_half_oz != null">{{menu.price_half_oz}}<sup class="text-420">1/2</sup></span>
                                    <span v-if="menu.price_oz != null">{{menu.price_oz}}<sup class="text-420">oz</sup></span>
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="mt-3 text-center" v-if="loading">
                <div class="spinner-border text-primary"></div>
            </div>
        </div>
    </div>
</template>

<script>

import Multiselect from "vue-multiselect";
import { mapGetters } from "vuex";

export default {
    name: "PortalMenu",
    components : {
        Multiselect,
    },
    data() {
        return {
            auth_user_id : null,
            category : [],
            selected_category : {},
            strains : [],
            strain : {},
            portal_menus : [],
            menus: [],
            portal_categories : [],
            mediaType : '',
            mediaData : '',
            is_open : false,
            is_adding : false,
            is_editting : false,
            hover_image : '',
            hover_description : '',
            strainLoading : false,
            taggedStrains : [],

            // Form Data
            menu_id : null,
            category_id : null,
            strain_id : null,
            item_name : null,
            description : '',
            price_gram : null,
            price_half_gram : null,
            price_eighth : null,
            price_quarter : null,
            price_half_oz : null,
            price_oz : null,
            price_each : null,
            remove_media : null,
            loading: false,
            
            show_filter: false,
            menu_filter : {
                category_id : null,
                selected_category : {},
                price_type : '',
                price_max : '',
            },
        }
    },
    props : ['portal'],
    created() {

    },
    watch : {
        portal: function(newPortal, oldPortal){
            this.init();
        },
        menu_filter: {
            deep: true,
            handler() {
                this.searchMenus();
            }
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
    }),
    mounted() {        
        var _this = this;
        this.init();
        $(document).ready(function(){
            $("#description_add").emojioneArea({
                pickerPosition: "top",
                search: false,
                autocomplete: false,
                placeholder: " ",
                events: {
                    blur: function (editor, event) {
                        let description = $("#description_add").data('emojioneArea').getText();
                        _this.description = description;
                        if(description == '') {
                            $("#description_add").siblings('label').removeClass('focused');
                        }
                    },
                    focus: function (editor, event) {
                        $("#description_add").siblings('label').addClass('focused');
                    }
                }
            });
        });

        $(document).on('blur', "#form_menu .emojionearea-editor", function () {
            if($(this).text() != '') {
                $(this).parent().addClass('focused');
            } else {
                $(this).parent().removeClass('focused');
            }
        });
        $(document).on('click', "#form_menu .emojionearea-editor", function () {
            $(this).parent().addClass('focused');
        });

        $(document).on('mouseover', '.btn-image', function(){
            $(this).siblings('.image-panel').fadeIn();
        });

        $(document).on('mouseleave', '.btn-image', function(){
            if(!$(this).siblings('.image-panel').is(':hover')){
                $(this).siblings('.image-panel').fadeOut();
            }
        });
        $(document).on('mouseleave', '.image-panel', function(){
            $(this).fadeOut();
        });

    },
    updated() {

    },
    filters : {
        category_name : function(name) {
            let flowers = ['Indica', 'Sativa', 'Hybrid'];
            let filtered_name = '';
            if(flowers.indexOf(name) !== -1) {
                filtered_name = 'Flowers - ' + name;
            } else {
                filtered_name = name;
            }
            return filtered_name;
        }
    },
    methods: {
        asyncFind(query) {
            this.ajaxFindData(query).then(response => {
                for(let i = response.length-1 ; i >=0  ; i--) {
                    let el = response[i];
                    if(el.name.toLowerCase().indexOf(query.toLowerCase()) === 0) {
                        let index = response.findIndex(item => item.id === el.id);
                        response.splice(index, 1);
                        response.unshift(el);
                    }
                }
                this.taggedStrains = response;
            });
        },
        ajaxFindData(query) {
            return new Promise((resolve, reject) => {
                        let results = this.strains.filter(
                            (element, index, array) => {
                                return element.name
                                    .toLowerCase()
                                    .includes(query.toLowerCase());
                            }
                        ).sort(function(a, b){
                            let x = a.name.toLowerCase();
                            let y = b.name.toLowerCase();
                            if (x < y) {return -1;}
                            if (x > y) {return 1;}
                            return 0;
                        });
                        resolve(results);
                    });
        },
        init() {
            this.auth_user_id = this.auth_user ? this.auth_user.id : '';
            this.axios.post('/categories')
                .then(response => {
                    this.category = response.data;
                });
            this.getMenus();
        },
        getMenus(){
            this.loading = true;
            let params = {
                id: this.portal.id,
            };
            this.axios.post('/portal/get_all_menus', params)
                .then(response => {
                    this.portal_categories = response.data.data.portal_categories;
                    this.portal_menus = response.data.data.menus;
                    this.menus = response.data.data.menus;
                    this.loading = false;
                });
        },
        initForm() {
            this.strains = [],
            this.strain = {},
            this.item_name = null;
            this.menu_id = null;
            this.category_id = null;
            this.strain_id = null;
            this.description = '';
            this.price_gram = null;
            this.price_half_gram = null;
            this.price_eighth = null;
            this.price_quarter = null;
            this.price_half_oz = null;
            this.price_oz = null;
            this.price_each = null;
            this.mediaData = null;
            this.is_editting = false;
            this.is_adding = false;
            this.is_open = false;
            this.selected_category = {};
            $("#form_menu .emojionearea-editor").text('');
            this.remove_media = null;
        },
        closeForm() {
            this.is_open = false;
            this.initForm();
        },
        getStrain(event) {
            let id = event.target.value;
            this.selected_category = this.category.filter(cat => cat.id == id)[0];

            let url = '/category/strains';
            let data = {id : id};
            this.axios.post(url, data)
                .then(response => {
                    this.strains = response.data;
                    this.taggedStrains = this.strains;
                });
        },
        previewImage() {
            this.mediaData = "";
            this.mediaType = "";
            let input = event.target;

            if (input.files[0].type.match("image/*")) {
                this.mediaType = "image";
            } else if (input.files[0].type.match("video/*")) {
                this.mediaType = "video";
            }

            if (input.files && input.files[0]) {
                let reader = new FileReader();
                this.media = input.files[0];
                reader.onload = e => {
                this.mediaData = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        },
        formSubmit() {
            if(this.is_adding){
                this.saveMenu();
            } else if(this.is_editting) {
                this.updateMenu();
            }
        },
        addMenu() {
            this.initForm();
            this.is_adding = true;
            this.is_open = true;
        },
        saveMenu() {
            const url = '/menu/create';
            const form_data = new FormData($("#form_menu")[0]);
            if(this.strain.id) {
                form_data.set('strain', this.strain.id);
            } else {
                form_data.set('strain', 0);
            }
            const headers = {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                };
            this.axios.post(url, form_data, headers)
                .then(response => {
                    if(response.data.status == 200){
                        this.init();
                    } else {
                        console.log('Something went wrong!!!');
                    }
                });
            this.initForm();
        },
        categoryMenu : function(category_id) {
            return this.menus.filter(menu => menu.category_id === category_id);
        },
        deleteMenu(id) {
            if(!window.confirm('Are you sure?')) {
                return false;
            } else {
                this.axios.get('/menu/' + id + '/delete')
                    .then(response => {
                        if(response.data.status == 200){
                            this.init();
                        } else {
                            console.log('Something went wrong!!!');
                        }
                    });
            }
        },
        deactiveMenu(id, is_active) {
            let question = is_active ? 'Deactivate' : 'Activate';
            if(!window.confirm(question)) {
                return false;
            } else {
                this.axios.get('/menu/' + id + '/deactive')
                    .then(response => {
                        if(response.data.status == 200){
                            this.init();
                        } else {
                            console.log('Something went wrong!!!');
                        }
                    });
            }
        },
        editMenu(menu) {
            this.selected_category = this.category.filter(cat => cat.id == menu.category_id)[0];
            this.menu_id = menu.id;
            this.category_id = menu.category_id;
            this.item_name = menu.item_name;
            this.description = menu.description;
            this.price_gram = menu.price_gram;
            this.price_half_gram = menu.price_half_gram;
            this.price_eighth = menu.price_eighth;
            this.price_quarter = menu.price_quarter;
            this.price_half_oz = menu.price_half_oz;
            this.price_oz = menu.price_oz;
            this.price_each = menu.price_each;
            this.mediaData = menu.media ? this.serverUrl(menu.media.url) : null;
            this.mediaType = menu.media ? menu.media.type : 'image';
            this.remove_media = null;
            $("#form_menu .emojionearea-editor").text(this.description);
            if(this.description) {
                $("#form_menu div.emojionearea").addClass('focused');
            }
            this.loading = true;
            this.axios.post('/category/strains', {id : this.category_id})
                .then(response => {
                    this.strains = response.data;
                })
                .then(() => {
                    let strain = this.strains.filter(st => st.id === menu.strain_id)[0];
                    this.strain = strain ? strain : {};
                    // $('.multiselect.strain_add').siblings('label').css({'top' : '-15px', 'color' : 'gray'});
                    this.is_editting = true;
                    this.is_open = true;
                    $("#menuForm").parent().scrollTop(0);
                    this.loading = false;
                });

        },
        updateMenu() {
            const url = '/menu/update';
            const form_data = new FormData($("#form_menu")[0]);
            if(this.strain.id) {
                form_data.set('strain', this.strain.id);
            } else {
                form_data.set('strain', 0);
            }
            const headers = {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                };
            this.axios.post(url, form_data, headers)
                .then(response => {
                    if(response.data.status == 200){
                        this.init();
                    } else {
                        console.log('Something went wrong!!!');
                    }
                });
            this.initForm();    
        },
        selectStrain(option) {
            // if(option) {
            //     $('.multiselect.strain_add').siblings('label').css({'top' : '-15px', 'color' : 'gray'});
            // } else {
            //     $('.multiselect.strain_add').siblings('label').css({'top' : '30%', 'color' : 'white'});
            // }
        },
        removeMedia(){
            this.mediaData = null;
            this.mediaType = null;
            this.remove_media = 1;
        },
        setNewStrain(){
            // this.strain = { id : 0, name : 'Not Listed Below'};
            this.strain = {};
            this.$refs.strain_multiselect.deactivate();
        },
        closeMenu(){
            if(this.$parent.$parent){
                this.$parent.$parent.openPortalMenu = false;
            }
        },
        resetSearchOptions() {
            this.menu_filter.category_id = null;
            this.menu_filter.selected_category = {};
            this.menu_filter.price_type = '';
            this.menu_filter.price_max = '';
        },
        getCategoryResults(item) {
            return this.portal_menus.filter(m => m.category_id == item.id).length;
        },
        searchMenus() {
            this.menus = this.portal_menus;
            if(this.menu_filter.category_id) {
                this.menus = this.menus.filter(item => item.category_id == this.menu_filter.category_id);
            }
            if(this.menu_filter.price_type) {
                let filter_price_type = this.menu_filter.price_type;
                this.menus = this.menus.filter(item => item[filter_price_type] != null);
                if(this.menu_filter.price_max) {                    
                    this.menus = this.menus.filter(item => item[filter_price_type] <= this.menu_filter.price_max);
                }
            }
        },
        showDescriptionClick(item) {
            if(this.$device.isMobile) {
                item.show_description = true;
            }
        },
        showDescriptionHover(item) {
            if(this.$device.isDesktop) {
                item.show_description = true;
            }
        },
        serverUrl(item) {
            if(item.charAt(0) != '/'){item = '/' + item;}
            try {
                return process.env.serverUrl + item;
            } catch (error) {
                return process.env.serverUrl + '/imgs/default.png';
            }
        }
    },
}
</script>

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
    .floating-select ~ label.focused {
        top: -15px;
        color: gray;
        font-size: 14px;
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

    .menu-container {
        display: flex;
        padding: 10px 0;
        border-bottom: solid 3px gray;
        .menu-media {
            position: relative;
            width: 125px;
            .media {
                img {
                    object-fit: cover;
                }

                .video__tag__mobile {
                    position: absolute;
                    height: 25px;
                    width: auto;
                    top: -2px;
                    right: 0;
                }
            }
            .btn-group {
                display: flex;
                justify-content: center;
                margin-top: 5px;
                .btn-action {
                    font-size: 15px;
                    margin-left: 5px;
                    color: #EFA720;
                }
            }
            .menu-description {
                position: absolute;
                z-index: 1;
                background-color: #000;
                border: solid 1px #FFF;
                border-radius: 4px;
                padding: 3px 5px;
                width: 528px;
                top: 80px;
                min-height: 80px;
            }
        }
        .menu-data {
            flex-grow: 1;
            padding-left: 20px;
            display: flex;
            flex-direction: column;
            @media (max-width: 600px) {
                padding-left: 10px;
            }
            .category-strain {
                color: gray;
                font-size: 14px;
            }
            .item_name {
                color: #EFA720;
                margin-bottom: 2px;
            }
            .price-data {
                margin-top: auto;
                color: #FFF;
                font-size: 16px;
                span {
                    padding-right: 10px;
                    sup {
                        font-size: 12px;
                        padding-left: 3px;
                    }
                }
            }
        }
    }

    @media (max-width: 600px) {
        .menu-container {
            padding: 8px 0;
            border-bottom: solid 2px gray;
            .menu-media {
                width: 80px;
                .menu-description {
                    width: 585px;
                    top: unset;
                    bottom: -5px;
                    min-height: 60px;
                    width: 320px;
                }
                .media {
                    img, video {
                        width: 80px;
                        height: 80px;
                    }
                    
                    .video__tag__mobile {
                        position: absolute;
                        height: 25px;
                        width: auto;
                        top: -2px;
                        right: 0;
                    }
                }
            }
            .menu-data {
                padding-left: 10px;
                .category-strain {
                    font-size: 14px;
                }
                .item_name {
                    font-size: 18px;
                }
            }
            .price-data {
                font-size: 14px !important;
                span {
                    padding-right: 0px !important;
                }
            }
        }
    }

    .btn-form-close {
        color: #EFA720;
        position: absolute;
        right: 17px;
        font-size: 23px;
        cursor: pointer;
    }

    .btn-remove-media {
        cursor: pointer;
    }

    .emojionearea .emojionearea-editor {
        padding-left: 0px;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal !important;
    }

    .custom-checkbox .custom-control-label::before {
        border-radius: 0;
        border-color: #d2d1d0;
        background-color: #000;
        color: #000;
    }

    .custom-checkbox .custom-control-label::after {
        left: -1.59rem;
        background: no-repeat 80% / 80% 80%;
    }

    .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OENBRjk0MUE2NjBBMTFFQTlEOTVGMzFBMEE3NjhGMzciIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OENBRjk0MUI2NjBBMTFFQTlEOTVGMzFBMEE3NjhGMzciPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo4Q0FGOTQxODY2MEExMUVBOUQ5NUYzMUEwQTc2OEYzNyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo4Q0FGOTQxOTY2MEExMUVBOUQ5NUYzMUEwQTc2OEYzNyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Piya4HIAAA0RSURBVHjazFlpdFXV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');
    }

    .page-data {
        overflow-y: scroll;
        min-height: 500px;
        @media (max-width: 600px) {
            padding-top: 55px;
            min-height: 600px;
        }
    }

    .section-search {
        position: relative;
        .search-header {
            position: relative;
            .results-count {
                position: absolute;
                left: 0px;
                top: 10px;
                color: #EFA720;
            }
        }
        .search-container {
            position: absolute;
            z-index: 1;
            width: 100%;
            .search-body {
                width: 100%;
                max-width: 400px;
                margin: auto;
                border: solid 1px gray;
                background-color: #000;
                margin: 6px auto;
                padding-top: 10px;
                padding-bottom: 10px;
                .filter-input {
                    max-width: 170px;
                    border-bottom: solid 1px #666;
                    padding-bottom: 4px;
                    cursor: pointer;
                }
                @media (max-width: 600px) {
                    border: none;
                    padding-left: 0;
                    padding-right: 0;
                }
            }   
        }
    }
    .menu-panel {
        display: flex;
        .desktop-search {
            width: 185px;
            padding-right: 20px;
            padding-left: 3px;
            .search-type {
                color: #EFA720;
                margin-bottom: 0;
                margin-top: 10px;
            }
        }
        .menu-list {
            flex-grow: 1;
        }
    }
    .custom-radio {
        .custom-control-label {
            font-size: 14.4px;
            cursor: pointer;
            &::before {
                border-color: #d2d1d0;
                background-color: #000;
                color: #000;
            }
            &::after {
                left: -1.56rem;
                background: no-repeat 80% / 74% 76%;
            }
        }
        .custom-control-input:checked ~ .custom-control-label::after {
            background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAADuSURBVHja7NrBDcMwCAVQPuoAXaFbdcRs1RW6AT1UvSWOcZqYj8gAmBfASiRgZpLhuYmIvJfHP2KNvhEcPfj+fH0hE5LfioFDFZkEaMXF2ZCrBsoN0oCIoTM1MMJ1tgZHdOegBIiuXJQEsZuTEiGauSkZYjNHlSSPElZjNdeUFWH8nrf0M0IPYf5NtGqtghSkIAUpSEEKUpCCuCAgNqBaKzoErG2VurXAWI30ww62arQqAibEXmuBBdEzI2BA9A47oiM8txYiI7zXL6IiRPybD7/AFgUwCjkbdPlSzdrB09acRESQZfHsAwAA//8DAKaFJ2siR2N6AAAAAElFTkSuQmCC');
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
    #form_menu .emojionearea-editor {
        font-size: 16px;
        white-space: normal !important;
    }

    #form_menu {
        .emojionearea {
            &.floating-textarea {
                border: none;
                border-bottom: solid 1px white;
                border-radius: 0;
                padding-right: 30px !important;
                overflow-x: unset;
                overflow-y: unset;
            } 
        }
        .emojionearea-button {
            top: 7px;
            div {
                background-image: url(https://i.imgur.com/xljqgrH.png) !important;
            }
        } 
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

    .portal_menu.fullscreen{
         .vs-popup{
            max-height: 100% !important;
            max-width: 100% !important;
            margin: 0;
            .btn-close-popup {
                width: 100% !important;
                .page-title {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    flex-grow: 1;
                    color: #EFA720;
                    font-size: 22px;
                    margin-bottom: 0;
                    padding: 10px 0;
                }
            }
        }
    }
    .portal_menu.strains__popup {
        .vs-popup {
            width: 750px !important;
            max-width: calc(100% - 30px);
            max-height: calc(100% - 30px);
        }
    }
    
</style>
