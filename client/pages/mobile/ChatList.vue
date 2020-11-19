<template>
    <div>
        <div class="blockuserlist" v-show="blockview">
            <fa icon="arrow-left" @click="disableblockpage()"></fa>
            <h3>Blocked Users/Companies</h3>
        </div>
        <h3 style="text-align:center; color:#efa720; padding: 8px 0 0px 0;" v-show="!blockview">Messages</h3>
        <hr style="background-color: #fff; margin-left: -10px; margin-right: -10px;margin-top:0;" v-show="!blockview">
        <div class="input-group mb-3 usersearch_bar" v-show="!blockview">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <fa icon="power-off" :class="active? 'active': 'disactive'" @click="toggleactive"></fa>
                </span>
            </div>
            <input type="text" placeholder="Search Users/Companies" class="form-control search_user" style="background-color: #fff !important; color: #000 !important;text-align: center;"
                @input='evt=>userkeyword=evt.target.value' @focus="searching = true" v-on:blur="searchunfocus" :disabled="disabledinput == 1"/>
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <fa icon="power-off" class="block_list" @click="viewblockuserlist()"></fa>
                </span>
            </div>
        </div>
        <div v-for="(user, index) of userlists" class="userlist" :key="index" v-show="!searching && !blockview">
            <router-link :to="{ name: 'mobile_chatbox', params: {userdetail: user}}">
                <img :src="serverUrl(user.url)" alt="">
                <div>
                    <p>
                        <img class="store-type" src="/imgs/dispensary.png" alt v-if="user.store_type == 1 || user.store_type == 3"/>
                        <img class="store-type" src="/imgs/delivery.png" alt v-if="user.store_type == 2 || user.store_type == 3"/>
                        <img class="store-type" src="/imgs/brand.png" alt v-if="user.type == 'brand'"/>
                        {{user.name}}<span class="badge badge-pill badge-success" v-show="user.unread_count > 0">{{user.unread_count}}</span>
                    </p>
                    <p class="lastmessage" v-if="user.lastmessage">{{user.lastmessage.message}}</p>
                </div>
            </router-link>
        </div>
        <div v-for="(user, index) of searchresult" class="userlist" v-show="searching && !blockview" :key="index">
            <router-link :to="{ name: 'mobile_chatbox', params: {userdetail: user}}">
                <img :src="(user.url)" alt="">
                <div>
                    <p>{{user.name}}</p>
                    <img class="store-type" src="/imgs/dispensary.png" alt v-if="user.store_type == 1 || user.store_type == 3"/>
                    <img class="store-type" src="/imgs/delivery.png" alt v-if="user.store_type == 2 || user.store_type == 3"/>
                    <img class="store-type" src="/imgs/brand.png" alt v-if="user.type == 'brand'"/>
                    <p class="lastmessage" v-if="user.lastmessage">{{user.lastmessage.message}}</p>
                </div>
            </router-link>
        </div>
        <div v-for="(user, index) of blockusers" class="userlist" v-show="blockview" :key="index">
            <fa icon="power-off" style="color:red !important;" @click="enableblockuser(user)"></fa>&nbsp;&nbsp;&nbsp;
            <img :src="serverUrl(user.url)" alt="" @click="gotouserprofile(user.name)">
            <div @click="gotouserprofile(user.username)">
                <p><span class="username">{{user.name}}</span></p>
                <img class="store-type" src="/imgs/dispensary.png" alt v-if="user.store_type == 1 || user.store_type == 3"/>
                <img class="store-type" src="/imgs/delivery.png" alt v-if="user.store_type == 2 || user.store_type == 3"/>
                <img class="store-type" src="/imgs/brand.png" alt v-if="user.type == 'brand'"/>
                <p class="lastmessage" v-if="user.lastmessage">{{user.lastmessage.message}}</p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ChatList",
        data() {
            return {
                userlists: null,
                chatuserlist: [],
                searchresult: [],
                userkeyword: '',
                searching: false,
                active: 0,
                disabledinput: 0,
                blockusers: [],
                blockview: false,
            }
        },
        methods: {
            disableblockpage(){
                this.blockview = false;
                this.disabledinput = 0;
                this.userkeyword = '';
            },
            viewblockuserlist(){
                this.blockview = true;
                let uri = '/user/blockuserlistonmobile';
                this.axios.get(uri).then((response) => {
                    this.blockusers = response.data;
                    this.userkeyword = 'Blocked Users';
                    this.disabledinput = 1;
                });
            },
            enableblockuser(user){
                let uri = "/user/enableblock";
                let params = {
                    blockuserid: user.id
                };
                if(window.confirm("Enable this user")) {
                    this.axios.post(uri, params).then((response) => {
                        this.getuserlist();
                        this.blockview =false;
                        this.disabledinput = 0;
                        this.userkeyword = '';
                    });
                }
            },
            gotouserprofile(username){
                let url = "/" + username;
                window.location.href = url;
            },
            getuserlist() {
                let uri = '/user/chatlist';
                this.axios.get(uri).then((response) => {
                    this.userlists = response.data;
                });
            },
            searchunfocus() {
                let _this = this;
                setTimeout(function () {
                    _this.searching = false;
                    _this.userkeyword = '';
                    _this.searchresult = [];
                }, 300);
            },
            searchusers() {
                let uri = '/user/searchuser';
                let params = {
                    keyword: this.userkeyword
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
                if (this.active) {
                    if(window.confirm("Deactive messenger?")) {
                        this.axios.get(uri).then((response) => {
                            this.active = response.data.status;
                        });
                    }
                } else {
                    if(window.confirm("Active messenger?")) {
                        this.axios.get(uri).then((response) => {
                            this.active = response.data.status;
                        });
                    }
                }
            },
            getMessengerStatus(){
                let uri = '/user/getmessengerstatus';
                this.axios.get(uri).then((response) => {
                    this.active = response.data;
                });
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
        mounted() {
            this.getuserlist();
            this.getMessengerStatus();
        },
        watch: {
            $route : function(to, from) {
                if(from.name == 'mobile_chatbox') {
                    this.getuserlist();
                }
            },
            userkeyword : function(keyword) {
                this.searchusers();
            }
        }
    }
</script>

<style lang="scss">
    .blockuserlist {
        color: black;
        background-color: white;
        margin-left: -15px;
        margin-right: -15px;
        padding: 15px;
        display: flex;
        align-items: center;

        h3 {
            font-size: 18px;
            margin-bottom: 0;
        }
        svg {
            float:left;
            font-size: 20px;
            margin-right: 10px;
        }
    }
    .usersearch_bar {
        padding: 0;
        .input-group-prepend {
            margin: 0 -4px 0 -3px !important;
            .input-group-text {
                background-color: white !important;
                border: none !important;
                font-size: 18px;
                border-radius: 0.25rem !important;

                svg {
                    color: red;
                    &.active{
                        color: #67e212;
                    }
                }
            }
        }

        .search_user {
            text-align: center;
            background-color: white !important;
            color: black !important;
        }
    }
    .userlist {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: solid 1px #fff;

        a {
            display: flex;
            align-items: center;
            width: 100%;
        }

        img {
            width: 35px;
            height: 35px;
            border-radius: 100%;
            margin-right: 10px;
            object-fit: cover;
            cursor: pointer;
        }
        div {
            float: right;
            cursor: pointer;
            p {
                color: #efa720 !important;
                font-size: 16px;

                .badge {
                    margin-left: 10px;
                    font-size: 12px;
                }
            }
            p.lastmessage {
                color: white !important;
            }
        }
    }
</style>
