<template>
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">{{mode == 'add' ? 'Add Post' : 'Edit Post'}}</h4>
        </div>
        <div class="card-body">
            <form @submit.prevent="savePost()" @keydown="form.onKeydown($event)">
                <div class="form-group row">
                    <label class="col-md-3">Title</label>
                    <div class="col-md-9">
                        <input v-model="form.title" :class="{ 'is-invalid': form.errors.has('title') }" type="text" name="title" class="form-control" autofocus />                    
                        <has-error :form="form" field="title" /> 
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-md-3">Description</label>
                    <div class="col-md-9">
                        <client-only>
                            <vue-trix v-model.lazy="form.description" id="darkTrix" class="mb-2" inputId="edit_description" placeholder="Enter your description..."/>
                        </client-only>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-md-3">Categories</label>
                    <div class="col-md-9">
                        <div class="form-check-inline mr-2" v-for="(item, index) of categories" :key="index">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="category" :value="item.id" v-model="form.categories" />{{item.name}}
                            </label>
                        </div>
                        <hr class="my-0 bg-white" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-md-3">Image</label>
                    <div class="col-md-9">
                        <div class="d-flex">
                            <file-upload
                                name="postfile"
                                class="btn-primary btn-sm"
                                extensions="gif,jpg,jpeg,png,webp"
                                accept="image/png,image/gif,image/jpeg,image/webp"
                                ref="upload"
                                v-model="files"
                                :post-action="upload_url"
                                @input-file="inputFile"
                                @input-filter="inputFilter"
                            >Select</file-upload>
                            <div class="progress" id="progress_logo" style="height: 10px; width: 70%; margin: 10px auto;" v-show="uploading">
                                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" :style="{width: uploadProgress + '%'}"></div>
                            </div>
                        </div>
                        <hr class="mb-0 bg-white" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Meta Title</label>
                    <div class="col-md-9">
                        <input v-model="form.meta_title" :class="{ 'is-invalid': form.errors.has('meta_title') }" type="text" name="meta_title" class="form-control" />                    
                        <has-error :form="form" field="meta_title" /> 
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Meta Keywords</label>
                    <div class="col-md-9">
                        <input v-model="form.meta_keywords" :class="{ 'is-invalid': form.errors.has('meta_keywords') }" type="text" name="meta_keywords" class="form-control" />                    
                        <has-error :form="form" field="meta_keywords" /> 
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Meta Description</label>
                    <div class="col-md-9">
                        <input v-model="form.meta_description" :class="{ 'is-invalid': form.errors.has('meta_description') }" type="text" name="meta_description" class="form-control" />                    
                        <has-error :form="form" field="meta_description" /> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9 offset-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>    
    import Form from 'vform'
    
    export default {
        data() {
            return {
                form: new Form({
                    image: '',
                    title: '',
                    description: '',
                    categories: [],
                    meta_title: '',
                    meta_keywords: '',
                    meta_description: '',
                }),
                post_image: '',
                upload_url: process.env.serverUrl + '/api/media/upload',
                files: [],
                uploadProgress: 5,
                uploading: false,
                categories: [],
                mode: this.$route.params.mode,
                post: this.$route.params.post,
            };
        },
        mounted() {
            this.getCategories();
            if(this.$route.params.mode == 'edit') {
                this.loadPost();
            }
        },
        methods: {
            getCategories() {
                let uri = '/admin/category';
                this.axios.get(uri).then(response => {
                    this.categories = response.data.categories;
                });
            },
            loadPost() {
                this.form.image = this.post.image
                this.form.title = this.post.title
                this.form.description = this.post.description
                this.form.meta_title = this.post.meta_title
                this.form.meta_keywords = this.post.meta_keywords
                this.form.meta_description = this.post.meta_description
                this.form.categories = this.post.category.split(',')
            },
            async savePost() {
                if(this.mode === 'add' && this.form.image === '') {
                    alert("Please select an image.")
                    return false;
                }
                let uri = this.mode == 'add' ? '/admin/post/add' : `/admin/post/${this.post.id}/update`
                const { data } = await this.form.post(uri);
                if(data.status == 200) {
                    this.$router.push({name: 'admin.post'});
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
                            _this.form.image = newFile.response.fileurl;
                            _this.post_image = process.env.serverUrl + newFile.response.fileurl;
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
    .form-group {
        label {
            margin-bottom: 0;
            text-align: right;
            line-height: 2.5;
        }
    }
    
    .form-check-label {
        cursor: pointer;
    }
</style>