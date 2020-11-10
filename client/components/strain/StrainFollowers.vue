<template>
    <div class="tabs-content pt-2" id="strain_followers">
        <div class="bg-420 text-center py-1" style="font-size:18px;">Followers</div>
        <div class="followers mt-1">
            <ul>
                <li class="mb-0" v-for="(follower, index) of followers" :key="index">
                    <div class="follower-logo" @click="goToUserProfile(follower)">
                        <img :src="serverUrl(follower.url)"/>
                    </div>
                    <div class="username">
                        <span @click="goToUserProfile(follower)">{{follower.name}}</span>
                    </div>
                    <div class="action text-center">
                        <img src="~/assets/imgs/follow-icon.png" v-if="follower.is_follower == 0" @click="follower.is_private ? followRequest(follower) : follow(follower)"/>
                        <img src="~/assets/imgs/unfollow.png" v-if="follower.is_follower == 1" @click="unfollow(follower)"/>
                        <button class="btn btn-sm btn-requested" v-if="follower.is_follower == 2">Requested</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import firebase from "../../Firebase";
    import { mapGetters } from "vuex";
    export default {
        name: 'StrainFollowers',
        props: ['strain'],
        data() {
            return {
                followers: [],
            }
        },        
        computed: mapGetters({
            user: 'auth/user',
        }),
        mounted() {
            this.getAllFollows();
        },
        methods: {
            getAllFollows(){
                let uri = '/marijuana-strains/get_all_follows';
                let params = {id: this.strain.id};
                this.axios.post(uri, params).then(response => {
                    this.followers = response.data.followers;
                });
            },
            follow(item){
                if(this.user){
                    if(this.user.type != 'user' || this.user.id == item.id)
                        return false;
                    let uri = '/user/follow';
                    let params = {
                        user_id: this.user.id,
                        follower_id: item.id
                    };
                    this.axios.post(uri, params).then(response => {
                        item.is_follower = 1;
                    });
                }else{
                    $("#loginmodal").modal('show');
                }
            },
            unfollow(item){
                if(this.user){
                    if(this.user.type != 'user' || this.user.id == item.id)
                        return false;
                    let uri = '/user/unfollow';
                    let params = {
                        user_id: this.user.id,
                        follower_id: item.id
                    };
                    this.axios.post(uri, params).then(response => {
                        item.is_follower = 0;
                    });
                }else{
                    $("#loginmodal").modal('show');
                }
            },
            followRequest(item) {
                if(this.user) {
                    if (this.user.type != 'user' || this.user.id == item.id)
                        return false;
                    let uri = '/user/follow_request';
                    let params = {
                        user_id: this.user.id,
                        follower_id: item.id,
                    };
                    this.axios.post(uri, params).then(response => {
                        if(response.data.status == 2) {
                            let noti_fb = firebase.database().ref('notifications/' + item.id).push();
                            noti_fb.set({
                                notifier_id: this.user.id,
                                type: 'follow_request',
                            });
                            item.is_follower = 2;
                        }
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },
            goToUserProfile(item) {
                window.location.href = "/" + item.name;
            },
            serverUrl(item) {
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + 'imgs/default.png';
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .btn-requested {
        border: solid 1px #FFF;
        color: #FFF;
    }
</style>