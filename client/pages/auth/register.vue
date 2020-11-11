<template>
    <div class="row justify-content-center" id="signup_page">
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="card mt-4 register_card">
                <div class="card-body">
                    <form @submit.prevent="register" @keydown="form.onKeydown($event)">
                        <!-- Name -->
                        <div class="profile_picture">
                            <h5 style="font-size: 22px;color:white;">Profile Photo</h5>
                            <label for="postfile" class="logo">
                                <div @click="openFileDialog()">
                                    <img :src="profile_pic" class="signup_logo" alt="" v-show="profile_pic">
                                </div>
                            </label>
                            <div class="progress" id="progress_logo" style="height: 10px; width: 150px; margin: 10px auto; display: none;" v-show="uploading">
                                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" :style="{width: uploadProgress + '%'}"></div>
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
                            >Select</file-upload>
                            <input type="hidden" id="logo_url" name="logo" v-model="form.logo">
                        </div>
                        <div class="form-group floating-label">
                            <input v-model="form.username" :class="{ 'is-invalid': form.errors.has('username') }" type="text" name="username" class="floating-input" placeholder=" " />
                            <label class="">Username</label>
                            <has-error :form="form" field="username" />              
                        </div>

                        <!-- Email -->
                        <div class="form-group floating-label">
                            <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email" name="email" class="floating-input" placeholder=" " />
                            <label class="">Email</label>
                            <has-error :form="form" field="email" />
                        </div>

                        <!-- Password -->
                        <div class="form-group floating-label">
                            <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" type="password" name="password" class="floating-input" placeholder=" " />
                            <label class="">Password</label>
                            <has-error :form="form" field="password" />
                        </div>

                        <div class="terms-condition mt-5">
                            <div class="form-group mb-0 d-flex align-item-center">
                                <input type="checkbox" required name="age_term" id="age_term" checked>
                                <label for="age_term" class="ml-1 mb-0" style="color: white;font-size:16px;">Are you 21+ Years Old or Older?</label>
                            </div>

                            <div class="form-group mb-0 d-flex align-item-center">
                                <input type="checkbox" required name="terms" id="user_term" checked>
                                <label for="user_term" class="ml-1 mb-0" style="font-size: 16px;">Accept</label> <a href="javascript:;" style="color: #EFA720;font-size:16px;margin-left:4px;" data-toggle="modal" data-target="#user_agreement_modal">User Agreement</a>
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <div class="col-md-12">
                                <v-button :loading="form.busy" :block="true">Register</v-button>
                            </div>
                        </div>

                        <p style="color: white;font-size:18px;text-align:center">
                            Already have an account? <a href="#" style="color: #EFA720;" data-toggle="modal" data-target="#loginmodal">Sign in</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <page-footer></page-footer>
        <!-- User Agreement Modal -->
        <div class="modal fade" id="user_agreement_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="text-white">User Agreement</h2>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"><terms></terms></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Form from 'vform'
import PageFooter from "~/components/PageFooter";
import Terms from "./terms"

export default {
    components: {
        PageFooter, Terms
    },
    head () {
        return { title: '420Portal - Register' }
    },
    watch: {
        'form.username': function (newName, oldName) {
            this.form.name = newName;
        }
    },
    data: () => ({
        form: new Form({
            logo: '',
            name: '',
            username: '',
            email: '',
            password: '',
        }),
        profile_pic: '',
        upload_url: process.env.serverUrl + '/api/media/upload',
        files: [],
        uploadProgress: 5,
        uploading: false,
    }),
    methods: {
        async register () {
            // Register the user
            const { data } = await this.form.post('/register')
            if (data) {
                // Log in the user.
                const { data: { token } } = await this.form.post('/login')

                // Save the token.
                this.$store.dispatch('auth/saveToken', { token })

                // Update the user.
                await this.$store.dispatch('auth/updateUser', { user: data })

                // Redirect home.
                window.location.href = "/";
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
                        // $(".progress").hide()
                        _this.uploading = false;
                        // _this.$emit('messagesent', {
                        //     message: newFile.response.url,
                        //     file: true,
                        //     created_at: newFile.response.created_at
                        // });
                        _this.form.logo = newFile.response.fileurl;
                        _this.profile_pic = process.env.serverUrl + newFile.response.fileurl;
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
        openFileDialog() {
            // this.$refs.upload.open();
        }
    }
}
</script>

<style scoped lang="scss">
    #signup_page {
        .profile_picture {
            text-align: center;
            label.logo {
                display: block;
                height: 150px;
                width: 150px;
                margin: auto;
                div{
                    background: url(/imgs/profile-bg.png) center;
                    background-size: cover;
                    padding: 10px;
                    border-radius: 100%;
                    width: 100%;
                    height: 100%;
                    img{
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        border-radius: 100%;
                    }
                }
            }
            
            .logo:hover {
                cursor: pointer;
            }

            label{
                color: #EFA720;
                cursor: pointer;
                font-size: 20px;
            }
        }
    }

</style>
