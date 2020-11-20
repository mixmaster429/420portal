<template>
    <div class="desktop-chat">
        <vs-sidebar position-right parent="body" default-index="1" color="primary" class="userlist-right" spacer :hidden-background="true"
                    v-model="flatchat">
            <div class="input-group mb-3 usersearch_bar">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <fa icon="power-off" :class="auth_user.messenger ? 'active': 'disactive'" @click="toggleactive"></fa>
                    </span>
                </div>
                <input type="text" placeholder="Search Users/Companies" class="form-control search_user" @keypress="searchusers($event)"
                       v-model="userkeyword" @focus="searching = true" v-on:blur="searchunfocus" :disabled="disabledinput == 1">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <fa icon="power-off" class="block_list" @click="viewblockuserlist()" v-show="!blockview"></fa>
                        <fa icon="arrow-left" class="block_list" @click="disableblockpage()" v-show="blockview"></fa>
                    </span>
                </div>
            </div>

            <div v-for="(user, index) of userlists" class="userlist" :key="`user-${index}`" @click="openchat(user)" v-show="!searching && !blockview">
                <img :src="serverUrl(user.url)" alt="">
                <div>
                    <p>
                        <img class="store-type" src="/imgs/dispensary.png" alt v-if="user.store_type == 1 || user.store_type == 3"/>
                        <img class="store-type" src="/imgs/delivery.png" alt v-if="user.store_type == 2 || user.store_type == 3"/>
                        <img class="store-type" src="/imgs/brand.png" alt v-if="user.type == 'brand'"/>
                        {{user.name}} <span class="badge badge-pill badge-success" v-show="user.unread_count > 0">{{user.unread_count}}</span>
                    </p>
                    <p class="lastmessage" v-if="user.lastmessage">{{user.lastmessage.message}}</p>
                </div>
            </div>

            <div v-for="(searchuser, index) of searchresult" class="userlist" :key="`searchuser-${index}`" @click="openchat(searchuser)"
                 v-show="searching && !blockview">
                <img :src="serverUrl(searchuser.url)" alt="">
                <div>
                    <p>{{searchuser.name}}</p>
                    <img class="store-type" src="/imgs/dispensary.png" alt v-if="searchuser.store_type == 1 || searchuser.store_type == 3"/>
                    <img class="store-type" src="/imgs/delivery.png" alt v-if="searchuser.store_type == 2 || searchuser.store_type == 3"/>
                    <!-- <p class="lastmessage" v-if="searchuser.lastmessage">{{searchuser.lastmessage.message}}</p> -->
                </div>
                <!-- <span class="badge badge-pill badge-success" v-show="searchuser.unread_count > 0">{{searchuser.unread_count}}</span> -->
            </div>

            <div v-for="(blockuser, index) of blockusers" class="userlist" :key="`blockuser-${index}`" v-show="blockview">
                <fa icon="power-off" style="color:red !important;" @click="enableblockuser(blockuser.id, blockuser.store_type)"></fa>&nbsp;&nbsp;&nbsp;
                <img :src="serverUrl(blockuser.url)" alt="" @click="gotouserprofile(blockuser)">
                <div @click="gotouserprofile(blockuser)">
                    <p>{{blockuser.name}}</p>
                    <img class="store-type" src="/imgs/dispensary.png" alt v-if="blockuser.store_type == 1 || blockuser.store_type == 3"/>
                    <img class="store-type" src="/imgs/delivery.png" alt v-if="blockuser.store_type == 2 || blockuser.store_type == 3"/>
                    <p class="lastmessage" v-if="blockuser.lastmessage">{{blockuser.lastmessage.message}}</p>
                </div>
            </div>

        </vs-sidebar>

        <div class="chatwindows" :class="flatchat ? 'rightmargin' : ''" v-if="auth_user.messenger">
            <chat-box :user="chatuser" v-for="(chatuser, index) of chat_users" :key="index" v-on:messagesent="getuserlist"></chat-box>
        </div>
    </div>

</template>

<script>
    import ChatBox from "./ChatBox";
    import firebase from '../Firebase';
    import { mapGetters } from "vuex";

    export default {
        name: "Flatchat",
        components: {
            ChatBox
        },
        data() {
            return {
                userlists: [],
                searchresult: [],
                portal_id: this.chatlistflag,
                popupActivo: false,
                userkeyword: '',
                searching: false,
                blockusers: [],
                blockview: false,
                disabledinput: 0,
                ref: firebase.database().ref('chatrooms/'),
            }
        },
        computed: {
             ...mapGetters({
                auth_user: 'auth/user',
                chat_users: 'chat/chat_users',
            }),
            flatchat: {
                get() {
                    return this.$store.state.chat.flatchat;
                },
                set() {
                    this.$store.commit('chat/TOGGLE_FLATCHAT');
                }
            }
        },
        mounted() {
            this.getuserlist();
            // let loged_user = this.auth_user.id;
            // if(this.portal_id != 0)
            //     loged_user = this.portal_id;
            // let _this = this;
            // let starCountRef = firebase.database().ref('chatrooms/' + loged_user + '/chats/');
            // starCountRef.on('value', function (snapshot) {
            //     //_this.getuserlist();
            // });

            // let starCountRef1 = firebase.database().ref('chatrooms/' + _this.id + '/chats/');
        },
        methods: {
            disableblockpage(){
                this.blockview = false;
                this.disabledinput = 0;
                this.userkeyword = '';
            },
            viewblockuserlist(){
                this.blockview = true;
                let uri = '/user/blockuserlist';
                this.axios.get(uri).then((response) => {
                    this.blockusers = response.data;
                    this.userkeyword = 'Blocked Users';
                    this.disabledinput = 1;
                });
            },
            enableblockuser(blockuserid, type){
                let uri = "/user/enableblock";
                let params = {
                    blockuserid: blockuserid
                };
                if (window.confirm("Unblock user?")) {
                    this.axios.post(uri, params).then((response) => {
                        this.getuserlist();
                        this.blockview =false;
                        this.disabledinput = 0;
                        this.userkeyword = '';
                    });
                }
            },
            gotouserprofile(user){
                let url = "/" + user.name;
                if(user.store_type > 0) {
                    url = "/" + user.username;
                }
                window.location.href = url;
            },
            searchunfocus() {
                let _this = this;
                setTimeout(function () {
                    _this.searching = false;
                    _this.userkeyword = '';
                    _this.searchresult = [];
                }, 300);
            },
            searchusers(e) {
                let keyword = e.target.value;
                let uri = '/user/searchuser';
                let params = {
                    keyword: keyword,
                };
                if(this.userkeyword == '' || this.userkeyword.length == 1){
                    this.searchresult = [];
                } else {
                    this.axios.post(uri, params).then((response) => {
                        this.searchresult = response.data;
                    });
                }
            },
            toggleactive() {
                let uri = '/user/togglemessenger';
                if (this.auth_user.messenger) {
                    if (window.confirm("Deactivate messenger?")) {
                        this.axios.get(uri).then((response) => {
                            // this.active = response.data.status;
                            this.$store.dispatch('auth/fetchUser');
                        });
                    }
                } else {
                    if (window.confirm("Activate messenger?")) {
                        this.axios.get(uri).then((response) => {
                            // this.active = response.data.status;
                            this.$store.dispatch('auth/fetchUser');
                        });
                    }
                }

            },
            getuserlist() {
                let uri = '/user/chatlist';
                this.axios.get(uri).then((response) => {
                    this.userlists = response.data;
                });
            },
            openchat(user) {
                let sender = this.auth_user.id
                let sender_type = 'user';
                let chat_user = {
                    from: sender,
                    to: user.id,
                    name: user.name,
                    username: user.username,
                    url: user.url,
                    type: user.type,
                    store_type: user.store_type,
                    sender_type: sender_type,
                };
                for (let i = 0; i < this.chat_users.length; i++) {
                    if (this.chat_users[i]['to'] === user.id && this.chat_users[i]['from'] === sender) return false;
                }
                this.$store.dispatch('chat/openChatBox', chat_user);
            },
            serverUrl(item) {
                try {
                    if(item.charAt(0) != '/'){item = '/' + item;}
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + '/imgs/default.png';
                }
            }
        },
    }
</script>

<style lang="scss">
    .userlist {
        cursor: pointer;
        .store-type {
            width: 15px !important;
            height: 15px !important;
            margin: 0px !important;
        }
        p {
            margin: 0;
            line-height: 20px;

            &.lastmessage {
                text-overflow: ellipsis;
                max-width: 200px;
                white-space: nowrap;
                overflow: hidden;
                display: inline-block;
                color: white;
            }
        }

        .badge {
            margin-left: 10px;
            font-size: 12px;


            /*color: white !important;*/
            /*background-color: #EFA720 !important;*/
        }
    }


    .chatwindows {
        display: flex;
        position: fixed;
        bottom: 72px;
        right: 0;
        z-index: 100;
    }

    .rightmargin {
        margin-right: 350px;
    }
</style>
