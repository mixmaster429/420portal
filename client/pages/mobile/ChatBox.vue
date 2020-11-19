<template>
    <div class="row">
        <div class="mchat_container" @click="readmessage()">
            <div class="chat_header">
                <fa icon="power-off" style="color:red; font-size: 20px; margin-left: 5px" @click="block()"></fa>
                <p class="username">
                    <a :href="'/' + username">&nbsp;{{name}}</a>
                    <br v-if="type != 'user'" class="my-0" />
                    <span class="store_type pl-1" v-if="type != 'user'">
                        <img src="/imgs/dispensary.png" v-if="store_type == 1 || store_type == 3" />
                        <img src="/imgs/delivery.png" v-if="store_type == 2 || store_type == 3" />
                        <img src="/imgs/brand.png" v-if="type == 'brand'" />
                    </span>
                </p>
                <p class="tools">
                    <fa icon="trash-alt" @click="deleteusermessage()"></fa>
                    <router-link :to="{ name: 'mobile_chatlist', params: {userdetail: this.$route.params.userdetail}}">
                        <fa icon="times" fixed-width style="color: #EFA720;font-size: 24px; margin-top: 5px;"></fa>
                    </router-link>
                </p>
            </div>
            <div class="chat_body" ref="chatbody" v-chat-scroll="{always: false, smooth: false, image: true}" style="overflow-y: auto" @scroll="scrollReached()">
                <div v-for="(message, index) of messages" :key="index" class="chatmessage" :class="message.type ? 'sent': ''">
                    <img
                        class="chat-profile-img"
                        :src="serverUrl(logo_url)"
                        v-if="!message.read && message.first_unread && !last_message_read && message.type"
                    />
                    <div style="clear: both; height:3px;" ></div>
                    <p v-if="message.file == 0">
                        <span class="message-text" v-html="linkify(message.message)"></span>
                        <span>{{changetimezone(message.created_at)}}</span>
                    </p>
                    <p class="msg-image" v-else>
                        <img :src="serverUrl(message.message)" alt="">
                        <span style="color:#d2d1d0;padding-top: 5px;">{{changetimezone(message.created_at)}}</span>
                    </p>
                    <div style="clear: both; height:3px;" ></div>
                    <img
                        class="chat-profile-img"
                        :src="serverUrl(logo_url)"
                        ref="last_message_read"
                        v-if="last_message_read && index == messages.length - 1"
                    />
                </div>
            </div>
            <div class="action" v-if="receiver_is_typing">
                <img src="/imgs/typing_indicator.gif" width="50" />
            </div>
            <span class="btn-scroll-bottom" v-if="show_scroll_btn" @click="scrollToBottom()"><img src="/imgs/down_arrow.png" width="40" alt=""></span>
            <chat-form v-on:messagesent="sendmessage" :receiver="this.id"></chat-form>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
    import firebase from '../../Firebase';
    import ChatForm from "~/components/mobile/ChatForm";

    export default {
        name: "ChatBox",
        data() {
            return {
                sender : null,
                receiver : this.$route.params.userdetail.id,
                id: this.$route.params.userdetail.id,
                name: this.$route.params.userdetail.name,
                username: this.$route.params.userdetail.username,
                logo_url: this.$route.params.userdetail.url,
                store_type: this.$route.params.userdetail.store_type,
                type: this.$route.params.userdetail.type,
                messages: [],
                ref: firebase.database().ref('chatrooms/'),
                loaded: false,
                last_message_read: false,
                receiver_is_typing : false,
                show_scroll_btn: false,
            }
        },
        computed: mapGetters({
            auth_user: 'auth/user',
        }),
        methods: {
            block(){
                let uri = "/user/block";
                let params = {
                    blockuserid: this.id
                };
                if(window.confirm('Block this user')) {
                    this.axios.post(uri, params).then((response) => {
                        // let r = this.$router.resolve({ name: 'mobile_chatlist', params: {userdetail: this.$route.params.userdetail}});
                        // window.location.assign(r.href)
                        window.location.href = "/mobile/chatlist";
                    });
                }
            },
            readmessage() {
                let uri = "/usermessages/readall";
                let params = {
                    receiver: this.id,
                    sender: this.auth_user.id
                };
                let _this = this;
                this.axios.post(uri, params).then((response) => {
                    if (response.status) {
                        _this.$emit('messagesent');
                        let readFs = firebase.database().ref('chatrooms/' + this.sender + '/read/' + this.receiver);
                        readFs.set({
                            read: true,
                        });
                        _this.$store.dispatch('auth/getUnreadMessage');
                    }
                });
            },
            sendmessage(chatmessage) {
                let joinData = firebase.database().ref('chatrooms/' + this.id + '/chats/').push();
                let url = "/usermessages/send";

                if(chatmessage.file){
                    joinData.set({
                        user_id: this.auth_user.id,
                        message: chatmessage.message,
                        file: 1
                    });
                } else {
                    let params = {
                        receiver: this.id,
                        message: chatmessage.message
                    };

                    this.axios.post(url, params).then((response) => {
                        if (response.status) {
                            joinData.set({
                                user_id: this.auth_user.id,
                                message: chatmessage.message,
                                file: 0
                            });
                            firebase.database().ref('chatrooms/' + this.receiver + '/read/' + this.sender).update({read : false});
                        }
                    });
                }
            },
            deleteusermessage(){
                let uri = '/user/deletemessage';
                let params = {
                    sender: this.auth_user.id,
                    receiver: this.id
                };
                if(window.confirm("Delete Message?")) {
                    this.axios.post(uri, params).then((response) => {
                        this.messages = [];
                        // let r = this.$router.resolve({ name: 'mobile_chatlist', params: {userdetail: this.$route.params.userdetail}});
                        // window.location.assign(r.href)
                        window.location.href = "/mobile/chatlist";
                    });
                }
            },
            fetchMessages() {
                let url = "/usermessages/fetch";
                let params = {
                    sender: this.auth_user.id,
                    receiver: this.id
                };
                this.axios.post(url, params).then((response) => {
                    this.messages = response.data;
                    this.last_message_read =  this.messages.filter(msg => msg.type && msg.first_unread)[0] ? 0 : 1;
                    this.scrollToBottom();
                });
            },
            changetimezone: function (servertime) {
                let localTime = this.$moment.utc(servertime).local().format('M-D-Y, hh:mm a');
                return localTime;
            },
            scrollToBottom(){
                let scrollingElement = this.$refs.chatbody;
                if(scrollingElement) {
                    this.$nextTick(function () {
                        $(scrollingElement).animate({
                            scrollTop: scrollingElement.scrollHeight + 2000
                        }, 400);
                    });
                }
            },
            scrollReached(){
                let scrollingElement = this.$refs.chatbody;
                if(scrollingElement) {
                    let scrollBottom = scrollingElement.scrollHeight - scrollingElement.scrollTop;
                    if(scrollBottom > 800) {
                        this.show_scroll_btn = true;
                    } else {
                        this.show_scroll_btn = false;
                    } 
                }
            },
            linkify(text) {
                var urlRegex =/(\b(https?|http):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
                let url = text.replace(urlRegex, function(url) {
                    if(!url.includes('http') && !url.includes('Http')) {
                        url = 'https://' + url;
                    }
                    return '<a href="' + url + '">' + url + '</a>';
                });
                return url;
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
            this.sender = this.auth_user.id;
            if(!this.$route.params.userdetail.url) {
                this.logo_url = this.$route.params.userdetail.profile_pic ? this.$route.params.userdetail.profile_pic.url : '/imgs/default_sm.png';
            }
            this.fetchMessages();
            let _this = this;
            const loged_user = this.auth_user.id;
            firebase.database().ref('chatrooms/' + loged_user + '/chats/').remove();
            // Sent
            let starCountRef = firebase.database().ref('chatrooms/' + _this.id + '/chats/').limitToLast(1);
            starCountRef.on('value', function (snapshot) {
                snapshot.forEach((doc) => {
                    let item = doc.val();
                    if (item.user_id == loged_user && _this.loaded) {
                        let first_unread = _this.messages.filter(msg => msg.first_unread == 1)[0];
                        let message = {
                            message: item.message,
                            type: 1,
                            file: item.file,
                            first_unread: first_unread ? 0 : 1,
                        };
                        _this.messages.push(message);
                        _this.scrollToBottom();
                    }
                });
                _this.loaded = true;
            });
            // Received
            let starCountRef1 = firebase.database().ref('chatrooms/' + _this.sender + '/chats/').limitToLast(1);
            starCountRef1.on('value', function (snapshot) {
                snapshot.forEach((doc) => {
                    let item = doc.val();
                    if (item.user_id == _this.id) {
                        let message = {
                            message: item.message,
                            type: 0,
                            file: item.file
                        };
                        _this.messages.push(message);
                        if(_this.$route.name == 'mobile_chatbox') _this.readmessage();
                        _this.scrollToBottom();
                    }
                });
            });

            this.readmessage();

            let readRef1 = firebase.database().ref('chatrooms/' + _this.receiver + '/read/' + _this.sender);
            readRef1.on('value', function (snapshot) {
                let item = snapshot.val();
                if (item && item.read == true) {
                    _this.last_message_read = item.read;
                    _this.messages.map((message) => {
                        if(!message.read && message.type) {
                            message.read = 1;
                            message.first_unread = 0;
                        }
                    });
                    let scrollingElement = _this.$refs.chatbody;
                    if(scrollingElement) {
                        $(scrollingElement).animate({
                            scrollTop: scrollingElement.scrollHeight + 2000
                        }, 100);
                    }
                }
            });

            let typing = firebase.database().ref('chatrooms/' + _this.receiver + '/is_typing/' + _this.sender);
            typing.on('value', function (snapshot) {
                _this.receiver_is_typing = snapshot.val() ? snapshot.val().is_typing : false;
            });            
            this.$store.dispatch('auth/getUnreadMessage');
        },
        watch: {
            '$route.params.userdetail': function (id) {
                this.messages = [];
                this.id = this.$route.params.userdetail.id;
                this.name = this.$route.params.userdetail.name;
                this.username = this.$route.params.userdetail.username;
                this.logo_url = this.$route.params.userdetail.url;
                this.type = this.$route.params.userdetail.type;
                this.store_type = this.$route.params.userdetail.store_type;
                this.fetchMessages();
                this.readmessage();
            }
        },
        components: {
            ChatForm
        }
    }
</script>

<style lang="scss">
    .mchat_container {
        width: 100%;
        height: calc(100vh - 158px);
        position: relative;

        p {
            margin: 0;
        }

        .chat_header {
            padding: 5px;
            display: flex;
            align-items: center;
            position: relative;
            border-bottom: 1px solid white;
            font-size: 20px;

            p {
                &.username {
                    color: #EFA720;
                    width: calc(100% - 70px);
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    line-height: 18px;
                    &.portal {
                        line-height: 17px;
                    }
                    .store_type {
                        img {
                            width: 15px;
                            height: 15px;
                        }
                    }
                }
            }

            .tools {
                position: absolute;
                right: 5px;
                color: #EFA720;
                display: flex;
                align-items: center;
            }

            i {
                margin-left: 10px;
            }
            p.username {
                a {
                    color: #EFA720;
                }
            }
        }

        .chat_body {
            height: calc(100vh - 258px);
            background-color:#000;
            padding-top: 5px;
            padding-bottom: 5px;

            .chatmessage {
                display: block;
                clear: both;

                .chat-profile-img {
                    border-radius: 100px;
                    width: 23px;
                    height: 23px;
                    float: right;
                    object-fit: cover;
                }

                &.sent {
                    p {
                        float: right;
                        margin: 0 5px 3px 0;
                        background-color: #96c83c;
                        font-size: 16px;

                        &:after {
                            right: -5px;
                            left: unset;
                            border-top: 10px solid #96c83c;
                        }
                    }
                }

                p {
                    position: relative;
                    margin: 0 0 3px 5px;
                    float: left;
                    max-width: 70%;
                    width: fit-content;
                    padding: 5px 10px;
                    background-color: #EFA720;
                    border-radius: 5px;
                    word-break: break-word;
                    color: #000;
                    line-height: 16px;
                    font-size: 16px;
                    // pre {
                    //     word-break: break-word;
                    //     color: black;
                    //     line-height: 16px;
                    //     font-size: 16px;
                    //     margin-bottom: 0;
                    //     font-weight: bold;
                    // }
                    span.message-text {
                        font-size: 17px;
                        text-align: left;
                        a {
                            color: blue;
                        }
                    }
                    span {
                        display: block;
                        font-size: 14px;
                        text-align: right;
                        font-weight: bold;
                    }

                    img{
                        width: 100%;
                        height: 100%;
                    }

                    &:after {
                        content: "";
                        border: 10px solid transparent;
                        border-top: 10px solid #EFA720;
                        position: absolute;
                        visibility: visible;
                        top: 0;
                        left: -5px;
                    }
                }
                p.msg-image {
                    background-color: unset;
                    border: solid 2px #96c83c;
                    &:after {
                        display: none;
                    }
                }
            }
        }
        .btn-scroll-bottom {
            position: absolute;
            right: 3px;
            bottom: 60px;
            cursor: pointer;
        }

        .action {
            height: 25px;
            background-color: #000;
            position: absolute;
            bottom: 53px;
            left: 0;
            width: 30%;
        }
    }

    .keyboard-open {
        .chat_body {
            height: calc(100vh - 149px);
        }
    }
</style>
