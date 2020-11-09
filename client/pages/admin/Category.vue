<template>
    <div class="card">
        <div class="card-header"><h4 class="mb-0">All Categories</h4></div>
        <div class="card-body">
            <table class="table table-borderless text-white">
                <tbody>
                    <tr v-for="(item, index) of categories" :key="index">
                        <td width="40">
                            <a class="up" href="javascript:;" v-if="item.order != 1" @click.prevent="orderUp(item)">⇑</a>
                            <a href="javascript:;" v-else>⇓</a>
                        </td>
                        <td width="40">
                            <a class="down" href="javascript:;" v-if="item.order != max" @click.prevent="orderDown(item)">⇓</a>
                            <a href="javascript:;" v-else>⇓</a>
                        </td>
                        <td>{{item.name}}</td>
                        <td width="150">
                            <a href="javascript:;" @click="editCategory(item)">Edit</a>
                            <a href="javascript:;" @click="deleteCategory(item)" class="ml-2">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="row">
                <div class="col-12 admin-blog-title text-center">
                    <form id="admin-post-form" class="form-horizontal" method="post" @submit.prevent="saveCategory">
                        <div class="form-group row">
                            <label for="name" class="col-md-3 text-right mb-0 pt-2"><h6 class="mb-0">Name : </h6></label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="name" name="name" v-model="form.name"/>
                            </div>
                            <div class="col-md-4text-left">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
    name: 'admin.post',
    data(){
        return {
            form: {
                id: '',
                name: ''
            },
            categories: [],
            max: 1,
            mode: '',
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
    }),
    mounted() {
        this.getCategories();
    },
    methods: {
        getCategories() {
            let url = `/admin/category`;
            this.axios.get(url).then(response => {
                this.categories = response.data.categories;
                this.max = response.data.max;
            });
        },
        deleteCategory(item) {
            if(!window.confirm('Are you sure?')) return false;
            let uri = `/admin/cateogry/${item.id}/delete`
            this.axios.get(uri).then(response => {
                if(response.data.status == 200) {
                    this.getCategories();
                }
            });
        },
        orderUp(item) {
            let uri = `/admin/category/order/${item.id}/up`;
            this.axios.get(uri).then(response => {
                if(response.data.status == 200) {
                    this.getCategories();
                }
            });
        },
        orderDown(item) {
            let uri = `/admin/category/order/${item.id}/down`;
            this.axios.get(uri).then(response => {
                if(response.data.status == 200) {
                    this.getCategories();
                }
            });
        },
        editCategory(item) {
            this.form = item;
            this.mode = 'edit';
        },
        saveCategory(){
            let uri = this.mode == 'edit' ? `/admin/category/${this.form.id}/update` : `/admin/category/add`;
            this.axios.post(uri, this.form).then(response => {
                this.getCategories();
            });
        }
    }
}
</script>