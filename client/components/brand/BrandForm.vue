<template>
    <div :class="{'mt-2': !is_edit}">
        <div class="pa_addform border-0" :class="{pa_mobile : $device.isMobile}">
            <div class="sticky-top strains__sticky" style="margin-right: -12px; margin-left: -12px; background: #fff;top:0;border-radius:5px 5px 0 0;" v-if="$device.isMobile && is_edit">
                <ul class="nav strains__nav">
                    <p style="margin: auto 5px; padding: 8px; font-size: 20px;color:black;">
                        <fa @click="closePopUp()" icon="times" class="mr-2"></fa> Edit Profile
                    </p>
                </ul>
            </div>
            <div class="pa-logo">
                <h4 class="pa-logotitle" style="cursor: pointer;">Brand Logo*</h4>
                <label for="pa_imageselect" class="d-block">
                    <div class="logo_preview" style="cursor: pointer;">
                        <img :src="profileMedia" alt=" " id="pa_logo_image" name="pa_logo_image" v-show="profileMedia">
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
                    :data="{media_type: 'user', username : brand_form.username}"
                    :post-action="upload_url"
                    @input-file="inputFile"
                    @input-filter="inputFilter"
                >Select</file-upload>
                <a @click.prevent="logout()" href="javascript:;" class="btn-logout" v-if="is_edit">
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
                <div class="form-label-group portal_description floating-portal floating-label mt-4" style="height: 100px;margin-bottom:12px;">
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
                profileMedia: null,
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
                loading : false,
                openActivatePopup: false,
                
                upload_url: process.env.serverUrl + '/api/media/upload',
                files: [],
                uploadProgress: 5,
                uploading : false,
            }
        },
        watch : {
            from : function(new_brand, old_brand) {
                if(this.is_edit) {
                    this.brand_form = new_brand;
                    this.brand_form.image_url = new_brand.profile_pic ? new_brand.profile_pic.url : '';
                    this.profileMedia = new_brand.profile_pic ? this.serverUrl(new_brand.profile_pic.url) : '';
                    if($("#brand-description")){
                        $("#brand-description").data('emojioneArea').setText(new_brand.description);
                    }   
                }           
            },
        },
        mounted() {
            if(process.client) {
                this.init();
            }
            var self = this;
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
            if(self.is_edit) {
                self.loadBrand();
            }
        },
        methods: {
            init() {
                console.log(document.getElementById('website'));
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
                    image_url: this.from.profile_pic ? this.from.profile_pic.url : '',
                    media_id: this.from.media_id,
                    name: this.from.name,
                    username: this.from.username,
                    is_active: this.from.is_active,
                    website_url: this.from.website_url,
                    description: this.from.description,
                };
                $("#brand-description").data('emojioneArea').setText(this.from.description);
                this.profileMedia = this.from.profile_pic ? this.serverUrl(this.from.profile_pic.url) : '';
            },
            async save(event) {
                event.preventDefault();
                this.brand_form.description = $("#brand-description").data('emojioneArea').getText();
                if(this.brand_form.image_url == '') {
                    return false;
                }
                let url = `/brand/save`;
                this.loading = true;
                const {data} = await this.axios.post(url, this.brand_form);
                this.loading = false;
                if(data.status == 200) {
                    // Log in the user.
                    const { data: { token } } = await this.axios.post('/login', {username: this.brand_form.username, password: this.brand_form.password})
                    // Save the token.
                    this.$store.dispatch('auth/saveToken', { token })
                    // Update the user.
                    await this.$store.dispatch('auth/updateUser', { user: data })
                    // Redirect home.
                    window.location.href = "/" + this.brand_form.username;
                }
            },
            update(event) {
                event.preventDefault();
                this.brand_form.description = $("#brand-description").data('emojioneArea').getText();
                let url = `/brand/update`;
                this.loading = true;
                this.axios.post(url, this.brand_form).then(response => {
                    this.loading = false;
                    if(response.data.status == 200) {
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
                this.axios.post(uri, params)
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
                this.axios.delete(uri, params)
                    .then(response => {
                        // this.openActivatePopup = false;
                        window.location.href = '/';
                    });
            },
            async logout () {
                await this.$store.dispatch('auth/logout')
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
                            _this.brand_form.image_url = newFile.response.fileurl;
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