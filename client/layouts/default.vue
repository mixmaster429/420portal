<template>
    <div class="layout" id="app" v-touch:swipe="toggleSidebar">
        <left-sidebar :active="leftsidebarflag" class="left-sidebar"></left-sidebar>
        <flatchat v-if="auth_user"></flatchat>
        <top-nav />

        <div class="container-fluid">
            <nuxt />
        </div>

        <bottom-nav />

        <!-- Login Modal -->
        <div class="modal fade" id="loginmodal" style="z-index: 40001">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <img src="~assets/imgs/logo.png" alt="">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <login />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopNav from '~/components/TopNav'
import BottomNav from '~/components/BottomNav'
import Login from '~/components/Login'
import Flatchat from '~/components/Flatchat'
import LeftSidebar from '~/components/mobile/LeftSidebar'
import { mapGetters } from "vuex";
import firebase from '../Firebase';
export default {
    components: {
        TopNav, BottomNav, Login, LeftSidebar, Flatchat,
    },
    data(){
        return {
            leftsidebarflag: false,
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
    }),
    mounted() {
        let _this = this;
        if(this.auth_user) {
            ////// Messenger
            firebase.database().ref('chatrooms/' + this.auth_user.id + '/chats/').remove();
            let starCountRef = firebase.database().ref('chatrooms/' + this.auth_user.id + '/chats/').limitToLast(1);
            starCountRef.on('value', function(snapshot) {
                snapshot.forEach((doc) => {
                    _this.$store.dispatch('auth/getUnreadMessage');
                    let audio = new Audio('/sound.mp3');
                    audio.play();
                });
            });
            ////// Notification
            this.$store.dispatch('auth/getUnreadNotification');
            firebase.database().ref('notifications/' + this.auth_user.id).remove();
            let notiCountRef = firebase.database().ref('notifications/' + this.auth_user.id).limitToLast(1);
            notiCountRef.on('value', function(snapshot) {
                snapshot.forEach((doc) => {
                    _this.$store.dispatch('auth/getUnreadNotification');
                });
            });   
        }
    },
    methods: {
        toggleSidebar(direction) {
            if (direction == 'right') {
                this.leftsidebarflag = true;
            } else if (direction == 'left') {
                this.leftsidebarflag = false;
            }
        },
    },
}
</script>
