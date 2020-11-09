<template>
    <div class="card">
        <div class="card-header"><h4 class="mb-0">All Posts</h4></div>
        <div class="card-body">
            <table class="table table-borderless text-white">
                <tbody>
                    <tr v-for="(post, index) of posts" :key="index">
                        <td>{{post.title}}</td>
                        <td width="110">
                            <router-link :to="{name: 'admin.post_form', params: {mode: 'edit', post: post}}">Edit</router-link>
                            <a href="javascript:;" @click="deletePost(post)" class="ml-2">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
    name: 'admin.post',
    data(){
        return {
            posts: [],
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
    }),
    mounted() {
        this.getPosts();
    },
    methods: {
        getPosts() {
            let url = `/marijuana-news`;
            let params = {category_id: ''};
            this.axios.post(url, params).then(response => {
                this.posts = response.data.posts;
            });
        },
        deletePost(item) {
            if(!window.confirm('Are you sure?')) return false;
            let uri = `/admin/post/${item.id}/delete`
            this.axios.get(uri).then(response => {
                if(response.data.status == 200) {
                    this.getPosts();
                }
            });
        }
    }
}
</script>