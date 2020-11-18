<template>
    <footer id="footer_bar">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">
                    <ul class="footer_tollbar">
                        <li>
                            <a href="/">
                                <img src="~assets/imgs/home.png" alt="">
                                <p>Home</p>
                            </a>
                        </li>                        
                        <li v-if="$device.isMobile">
                            <router-link to="/mobile/userlist">
                                <a href="/mobile/userlist">
                                    <img src="~assets/imgs/messenger-mobile.png" alt="">                                    
                                    <span v-if="user" class="badge badge-pill badge-success messenger-unread" style="position:absolute;top: 5px;">{{user.unread_message_user_count == 0 ? '' : user.unread_message_user_count}}</span>
                                    <p>Messenger</p>
                                </a>
                            </router-link>
                        </li>
                        <li v-else>
                            <a href="javascript:;" @click="openMessenger()">
                                <img src="~assets/imgs/messenger-mobile.png" alt="">                                   
                                <span v-if="user" class="badge badge-pill badge-success messenger-unread" style="position:absolute;top: 5px;">{{user.unread_message_user_count == 0 ? '' : user.unread_message_user_count}}</span>
                                <p>Messenger</p>
                            </a>
                        </li>
                        <li>
                            <template v-if="user">
                                <!-- <router-link v-if="$device.isMobile" to="/mobile/media/add"> -->
                                    <a href="/mobile/media/add" v-if="$device.isMobile">
                                        <img src="/imgs/add.png" alt="">
                                        <p>Add Media</p>
                                    </a>
                                <!-- </router-link> -->
                                <add-media v-else></add-media>
                            </template>
                            
                            <a v-else href="javascript:;" @click="openLoginModal()" class="myprofile">
                                <img src="/imgs/add.png" alt="">
                                <p>Add Media</p>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" @click="goNotification()">
                                <img src="~assets/imgs/heart.png" alt="">
                                <span v-if="user && notification_count" class="badge badge-pill badge-success notification-unread" style="position:absolute;top: 5px;">{{notification_count}}</span>
                                <p>Notifications</p>
                            </a>
                        </li>
                        <li class="myaccount">                            
                            <a v-if="user" href="javascript:;" @click="goProfile()">                                
                                <img :src="serverUrl(user.profile_pic ? user.profile_pic.url : '/imgs/default_sm.png')" style="padding:5px;border-radius:100px;object-fit:cover;" alt="">
                                <p>Account</p>
                            </a>                        
                            <a v-else href="javascript:;" @click="goProfile()" class="myprofile">
                                <img src="~assets/imgs/account.png" alt="">
                                <p>Account</p>
                            </a>                            
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
import { mapGetters } from 'vuex'
import AddMedia from "./media/AddMedia";

export default {
    components: {
        AddMedia,
    },

    data: () => ({
        appName: process.env.appName
    }),

    computed: mapGetters({
        user: 'auth/user',
        notification_count: 'auth/notification_count',
    }),

    methods: {
        openLoginModal() {
            $("#loginmodal").modal();
        },
        serverUrl(item) {
            if(item.charAt(0) != '/'){item = '/' + item;}
            try {
                return process.env.serverUrl + item;
            } catch (error) {
                return 'imgs/default.png';
            }
        },
        openMessenger() {
            if(this.user) {
                this.$store.dispatch('chat/toggleFlatchat');
            } else {
                this.openLoginModal();
            }
        },
        goProfile() {
            if(this.user) {
                window.location.href = "/" + this.user.username;
            } else {
                this.openLoginModal();
            }
        },
        goNotification() {
            if(this.user) {
                window.location.href = "/notification";
            } else {
                this.openLoginModal();
            }
        }
    }
}
</script>

<style scoped>

</style>
