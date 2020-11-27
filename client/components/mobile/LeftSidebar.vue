<template>
    <div v-if="$device.isMobile">
        <vs-sidebar parent="body" default-index="1" color="primary" class="sidebarx" spacer v-model="flag">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 leftside-title">
                        <a href="/"><img src="~assets/imgs/logo.png" alt="" height="50"></a>
                    </div>                
                </div>
                <div class="row" v-if="auth_user">
                    <div class="col-12 text-center pb-2 pt-3">
                        <a :href="'/' + auth_user.username" style="text-decoration: none">
                            <img class="logo mx-auto" :src="serverUrl(auth_user.profile_pic ? auth_user.profile_pic.url : '/imgs/default_sm.png')" alt=" ">
                            <h5 class="username">{{auth_user.type == 'user' ? auth_user.username : auth_user.name}}</h5>
                        </a>
                        <a href="/dashboard" class="btn-edit" v-if="auth_user.name == '420portal'"><img src="~assets/imgs/edit.png" alt=""></a>
                    </div>
                    <div class="col-12 text-center pb-2 pt-0">
                        <hr class="sidebar-hr" />
                        <button type="button" class="btn btn-primary" @click="logout">Logout</button>
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col-12 text-center">
                        <hr class="sidebar-hr" />  
                        <button type="button" class="btn btn-primary" @click="openLoginModal">Log In / Sign Up</button>
                    </div>
                </div>
            </div>
        </vs-sidebar>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
    name : 'LeftSidebar',
    data() {
        return {
            flag : this.active,
        }
    },
    props : ['active'],
    watch: {
        active : function(newActive) {
            this.flag = newActive;
        },
        flag : function(newFlag) {
            if(!newFlag){
                this.$parent.leftsidebarflag = false;
            }
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
    }),
    methods: {
        openLoginModal() {
            $("#loginmodal").modal('show');
        },
        async logout () {
            await this.$store.dispatch('auth/logout')
            window.location.href = "/";
        },
        serverUrl(item) {
            if(item.charAt(0) != '/'){item = '/' + item;}
            try {
                return process.env.serverUrl + item;
            } catch (error) {
                return process.env.serverUrl + 'imgs/default.png';
            }
        }
    },
    updated() {
        
    },
}
</script>

<style lang="scss" scoped>
    .vs-sidebar--items {
        .leftside-title {
            height: 61px;
            text-align: center;
            background: white;
            padding-top: 5px;
        }
        .sidebar-hr {
            margin: 0 auto;
            margin-bottom: 14px;
            background-color: white;
            width: 90%;
        }
        .logo {
            width: 85px;
            height: 85px;
            border-radius: 100%;
            object-fit: cover;
        }
        .username {
            color: #EFA720;
            margin-top: 8px;
            margin-bottom: 0;
            font-size: 24px;
        }
        .btn-edit {
            position: absolute;
            top: 45px;
            right: 20px;
            img {
                width: 30px;
            }
        }
    }
</style>