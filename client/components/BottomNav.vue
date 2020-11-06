<template>
    <footer id="footer_bar">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">
                    <ul class="footer_tollbar">
                        <li>
                            <a href="#">
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
                            <a>
                                <img src="~assets/imgs/messenger-mobile.png" alt="">                                   
                                <span v-if="user" class="badge badge-pill badge-success messenger-unread" style="position:absolute;top: 5px;">{{user.unread_message_user_count == 0 ? '' : user.unread_message_user_count}}</span>
                                <p>Messenger</p>
                            </a>
                        </li>
                        <li>
                            <template v-if="user">
                                <router-link v-if="$device.isMobile" to="/mobile/addmedia">
                                    <a href="/mobile/addmedia">
                                        <img src="~assets/imgs/add.png" alt="">
                                        <p>Add Media</p>
                                    </a>
                                </router-link>                                
                                <li v-else>
                                    <img src="~assets/imgs/add.png" alt="">
                                    <p>Add Media</p>
                                </li>
                                
                            </template>
                            
                            <a v-else href="#" class="myprofile">
                                <img src="~assets/imgs/add.png" alt="">
                                <p>Add Media</p>
                            </a>
                            
                        </li>
                        <li>
                            <a href="#">
                                <img src="~assets/imgs/heart.png" alt="">
                                    <span v-if="user" class="badge badge-pill badge-success notification-unread" style="position:absolute;top: 5px;">0</span>
                                <p>Notifications</p>
                            </a>
                        </li>
                        <li class="myaccount">                            
                            <a v-if="user" href="#">                                
                                <img :src="serverUrl(user.profile_pic ? user.profile_pic.url : '/imgs/default_sm.png')" style="padding:5px;border-radius:100px;object-fit:cover;" alt="">
                                <p>Account</p>
                            </a>                        
                            <a v-else href="#" class="myprofile">
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

export default {

    data: () => ({
        appName: process.env.appName
    }),

    computed: mapGetters({
        user: 'auth/user',
    }),

    methods: {
        serverUrl(item) {
            if(item.charAt(0) != '/'){item = '/' + item;}
            try {
                return process.env.serverUrl + item;
            } catch (error) {
                return 'imgs/default.png';
            }
        }
    }
}
</script>

<style scoped>

</style>
