<template>
    <div :class="{'mt-2': !is_edit}">
        <div class="pa_addform border-0" :class="{pa_mobile : is_mobile}">
            <div class="sticky-top strains__sticky" style="margin-right: -12px; margin-left: -12px; background: #fff;top:0;border-radius:5px 5px 0 0;" v-if="is_mobile && is_edit">
                <ul class="nav strains__nav">
                    <p style="margin: auto 5px; padding: 8px; font-size: 20px;color:black;">
                        <i @click="closePopUp()" class="fas fa-times mr-2"></i> Edit Profile
                    </p>
                    <!-- <div class="ml-auto" style="padding: 8px; font-size: 20px;">
                        <i @click="update()" class="fas fa-check mr-3" style="color: #efa720;"></i>
                    </div> -->
                </ul>
            </div>
            <div class="pa-logo">
                <h4 class="pa-logotitle" style="cursor: pointer;">Brand Logo*</h4>
                <input type="file" hidden id="pa_imageselect" name="postfile" accept="image/*">
                <label for="pa_imageselect">
                    <div class="logo_preview" style="cursor: pointer;">
                        <img :src="brand_form.image_url ? brand_form.image_url : ''" alt=" " id="pa_logo_image" name="pa_logo_image" v-show="brand_form.image_url">
                    </div>
                </label>
                <div>
                    <div class="pa_progress progress d-block" v-if="media_progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" :style="{width: media_progress + '%'}"></div>
                    </div>
                </div>
                <label class="btn btn-sm btn-primary mt-2" for="pa_imageselect">Select</label>
                <a @click="logout($event)" href="javascript:;" class="btn-logout" v-if="is_edit">
                    <img src="/imgs/logout.png" alt />
                </a>                
                <img :src="brand_form.is_active ? '/imgs/active.png' : '/imgs/inactive.png'" alt=" " class="btn-power" v-if="is_edit && brand_form" @click="openActivatePopup = true">
            </div>

            <form action="" class="pt-3" id="pa_addform" method="post" ref="pa_addform" @submit="is_edit ? update($event) : save($event)">
                <div class="floating-label mt-4">
                    <input type="text" id="portal_name" class="floating-input" name="name" required autocomplete="off" v-model="brand_form.name" placeholder=" ">
                    <label for="portal_name">Brand Name *</label>
                </div>
                <div class="floating-label mt-4">
                    <input type="text" id="portal_username" class="floating-input" name="username" required autocomplete="off" v-model="brand_form.username" placeholder=" ">
                    <label for="portal_username">Brand Userame *</label>
                </div>
                <div class="floating-label mt-4" v-if="!is_edit">
                    <input type="password" id="portal_password" class="floating-input" name="password" required autocomplete="off" v-model="brand_form.password" placeholder=" ">
                    <label for="portal_password">Password *</label>
                </div>
                <div class="floating-label mt-2">
                    <input type="text" class="floating-input" name="website_url" id="website" data-value="http://" v-model="brand_form.website_url" placeholder=" ">
                    <label for="website">Brand Website</label>
                </div>
                <div class="form-label-group portal_description floating-portal mt-4" style="height: 100px;margin-bottom:12px;">
                    <textarea class="form-control comment_text" name="description" id="brand-description" placeholder=" " v-model="brand_form.description"></textarea>
                    <label class="pt-0" :class="{floating : brand_form.description}">Brand Bio</label>
                </div>
                <div class="custom-check d-flex" v-if="!is_edit">
                    <div class="round mr-1">
                        <input type="checkbox" value="1" checked name="old_condition" id="old_condition" />
                        <label for="old_condition"></label>
                    </div>
                    <label class="ml-2 text-white" for="old_condition">Are you 21+ Years Old or Older?</label>
                </div>
                <div class="custom-check d-flex" v-if="!is_edit">
                    <div class="round mr-1">
                        <input type="checkbox" value="1" checked name="terms_condition" id="terms_condition" />
                        <label for="terms_condition"></label>
                    </div>
                    <label class="ml-2 text-white" for="terms_condition">Accept</label><a href="javascript:;" style="color: #EFA720;font-size:16px;margin-left:4px;" data-toggle="modal" data-target="#user_agreement_modal">User Agreement</a>
                </div>
                <div class="submit text-center">                        
                    <button type="button" class="btn btn-primary btn-block mb-3 mt-4" disabled v-if="loading">
                        <div class="spinner spinner-border text-white" role="status"></div>
                    </button>
                    <button type="submit" class="btn btn-primary btn-block mb-3 mt-4" v-else>Post</button>
                </div>
            </form>
        </div>
        <vs-popup class="active_popup" type="border" title :active.sync="openActivatePopup" v-if="is_edit && brand_form">
            <div class="text-center py-3">
                <button class="btn btn-primary mr-3" @click="activeBrand()">{{brand_form.is_active ? 'Deactivate' : 'Activate'}}</button>
                <button class="btn btn-420" @click="deleteBrand()">Delete</button>
            </div>
        </vs-popup>
    </div>
</template>
<script>
    export default {
        name : "BrandForm",
        props: ['is_edit', 'from'],
        data : function(){
            return {
                brand_form : {
                    id: null,
                    image_url: '',
                    media_id: null,
                    name: '',
                    username: '',
                    password: '',
                    website_url: '',
                    is_active: null,
                    description: '',
                },
                is_mobile : window.is_mobile,
                loading : false,
                media_progress : 0,
                openActivatePopup: false,
            }
        },
        watch : {
            from : function(new_brand, old_brand) {
                if(this.is_edit) {
                    this.brand_form = new_brand;
                    this.brand_form.image_url = new_brand.media ? new_brand.media.url : '';
                    if($("#brand-description")){
                        $("#brand-description").data('emojioneArea').setText(new_brand.description);
                    }   
                }           
            },
        },
        mounted() {
            this.init();
            var self = this;
            $(document).ready(function(){
                $("#brand-description").emojioneArea({
                    pickerPosition: "top",
                    search: false,
                    autocomplete: false,
                    placeholder: " ",
                    events: {
                        blur: function (editor, event) {
                            const description = $("#brand-description").data('emojioneArea').getText();
                            self.brand_form.description = description;
                            if(description == '') {
                                $("#brand-description").siblings('label').removeClass('floating');
                            }
                        },
                        focus: function (editor, event) {
                            $("#brand-description").siblings('label').addClass('floating');
                        }
                    }
                });
                $('#pa_imageselect').fileupload({
                    dataType: 'json',
                    type: 'POST',
                    maxFileSize: 10000,
                    formData: {media_type: 'user', username : self.brand_form.name},
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        self.media_progress = progress;
                    },
                    done: function (e, data) {
                        self.media_progress = 0;
                        self.brand_form.image_url = data.result.fileurl;
                    },
                    fail: function (e, data) {
                        console.log('fail', e, data)
                    },
                    url: '/media/upload'
                });
                if(self.is_edit) {
                    self.loadBrand();
                }
            });
        },
        methods: {
            init() {
                this.makeInitialTextReadOnly(document.getElementById('website'));
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
            loadBrand() {
                this.brand_form = {
                    id: this.from.id,
                    image_url: this.from.media ? this.from.media.url : '',
                    media_id: this.from.media_id,
                    name: this.from.name,
                    username: this.from.username,
                    is_active: this.from.is_active,
                    website_url: this.from.website_url,
                    description: this.from.description,
                };
                $("#brand-description").data('emojioneArea').setText(this.from.description);
            },
            save(event) {
                event.preventDefault();
                if(this.brand_form.image_url == '') {
                    return false;
                }
                let url = `/brand/save`;
                this.loading = true;
                axios.post(url, this.brand_form).then(response => {
                    this.loading = false;
                    if(response.data.status == 200) {
                        window.location.href = response.data.brand.username;
                    }
                });
            },
            update(event) {
                event.preventDefault();
                this.brand_form.description = $("#brand-description").data('emojioneArea').getText();
                let url = `/brand/update`;
                this.loading = true;
                axios.post(url, this.brand_form).then(response => {
                    this.loading = false;
                    if(response.data.status == 200) {
                        // this.$parent.$parent.openEditPortal = false;
                        // this.$parent.$parent.selected = response.data.brand;
                        window.location.reload();
                    }
                });
            },
            closePopUp() {
                this.$parent.$parent.openEditPortal = false;
            },
            
            activeBrand(){
                let params = {
                    id: this.brand_form.id,
                };
                let uri = `/user/activate`;
                axios.post(uri, params)
                    .then(response => {
                        this.openActivatePopup = false;
                        this.brand_form.is_active = response.data;
                    });
            },
            deleteBrand(){
                if(!window.confirm('Are you sure?')) {
                    return false;
                }
                let params = { id: this.brand_form.id };
                let uri = `/portals/${this.brand_form.id}`;
                axios.delete(uri, params)
                    .then(response => {
                        // this.openActivatePopup = false;
                        window.location.href = '/';
                    });
            },
            logout(event) {
                event.preventDefault();
                if(window.confirm('Are you sure?')) {
                    document.getElementById('logout-form').submit();
                }                
            }
        }
    }
</script>
<style lang="scss" scoped>
    .portal_description {
        label {
            &.floating {
                top: -20px;
                color: gray;
            }
        }
    }
</style>

<style lang="scss">
    .active_popup .vs-popup {
        width: 420px;
    }
</style>