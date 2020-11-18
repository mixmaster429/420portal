<template>
    <div class="chatwindow" @click="readmessage()">
        <div class="chat_header">
            <fa icon="power-off" style="color:red; font-size: 16px; margin-left: 5px" @click="block()"></fa>
            <p @click="gotouserprofile(username)" class="username" :class="{portal : user.type != 'user'}">
                {{user.name}}
                <br v-if="user.type != 'user'" />
                <span class="store_type" v-if="user.type != 'user'">
                    <img src="/imgs/dispensary.png" v-if="store_type == 1 || store_type == 3" />
                    <img src="/imgs/delivery.png" v-if="store_type == 2 || store_type == 3" />
                    <img src="/imgs/brand.png" v-if="user.type == 'brand'" />
                </span>
            </p>
            <p class="tools">
                <fa icon="trash-alt" @click="deleteMessage()" style="font-size: 16px;"></fa>
                <fa icon="times" style="font-size: 21px; margin-left: 5px" @click="closewindow"></fa>    
            </p>
        </div>
        <div class="chatbody" ref="chatbody" v-chat-scroll="{always: false, smooth: false, image: true}" style="overflow-y: auto;" @scroll="scrollReached()">
            <div v-for="(message, index) of messages" :key="index" class="chatmessage" :class="message.type ? 'sent': ''">
                <img
                    class="chat-profile-img"
                    :src="serverUrl(logo_url)"
                    v-if="!message.read && message.first_unread && !last_message_read && message.type"
                />
                <div style="clear: both; height:3px;" ></div>
                <p v-if="message.file == 0">
                    <span class="message-text" v-html="linkify(message.message)"></span>
                    <span class="message-time">{{changetimezone(message.created_at)}}</span>
                </p>
                <p class="msg-image" v-else>
                    <img :src="serverUrl(message.message)" alt="">
                    <span>{{changetimezone(message.created_at)}}</span>
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
        <div class="action">
            <img src="/imgs/typing_indicator.gif" width="50" v-if="receiver_is_typing" />
        </div>
        <span class="btn-scroll-bottom" v-if="show_scroll_btn" @click="scrollToBottom()"><img src="/imgs/down_arrow.png" width="30" alt=""></span>
        <chat-form v-on:messagesent="sendmessage" :receiver="this.receiver" :sender="this.sender" :chat_type="this.chat_type"></chat-form>
    </div>
</template>

<script>
    import firebase from '../Firebase';
    import ChatForm from "./ChatForm";
    export default {
        props: ['user'],
        name: "ChatBox",
        components: {
            ChatForm
        },
        data() {
            return {
                receiver: this.user.to,
                sender: this.user.from,
                username: this.user.username,
                logo_url: this.user.url,
                store_type: this.user.store_type,
                chat_type: this.user.type,
                message: '',
                messages: [],
                isblockuser: 0,
                ref: firebase.database().ref('chatrooms/'),
                last_message_read: false,
                receiver_is_typing : false,
                show_scroll_btn: false,
            }
        },
        methods: {
            block(){
                let uri = "/user/block";
                let params = {
                    blockuserid: this.receiver
                };
                if (window.confirm("Block this user?")) {
                    this.axios.post(uri, params).then((response) => {
                        this.$emit('closewindow', {
                            sender: this.sender,
                            receiver: this.receiver
                        });
                    });
                }
            },
            readmessage() {
                let url = "/usermessages/readall";
                let params = {
                    receiver: this.receiver,
                    sender: this.sender
                };
                let _this = this;

                this.axios.post(url, params).then((response) => {
                    if (response.status) {
                        let readFs = firebase.database().ref('chatrooms/' + this.sender + '/read/' + this.receiver);
                        readFs.set({
                            read: true,
                        });
                        // this.$root.getUnreads();
                        this.$parent.getuserlist();
                    }
                });
            },
            deleteMessage(){
                let uri = '/user/deletemessage';
                let params = {
                    sender: this.sender,
                    receiver: this.receiver
                };
                if (window.confirm("Delete Message?")) {
                    this.axios.post(uri, params).then((response) => {
                        this.messages = [];
                        let list_index = this.$parent.userlists.findIndex(user => user.id == this.receiver);
                        this.$parent.userlists.splice(list_index, 1);
                        this.closewindow();
                    });
                }
            },
            gotouserprofile(username){
                window.location.href = username;
            },
            closewindow() {
                firebase.database().ref('chatrooms/' + this.sender + '/is_typing').remove();
                // this.$store.dispatch('chat/closeChatBox', this.user);
                this.$store.commit('chat/REMOVE_CHAT_USER', this.user);
                // this.$emit('closewindow', {
                //     sender: this.sender,
                //     receiver: this.receiver
                // });
            },
            sendmessage(chatmessage) {
                this.checkblockuser()
                .then((returnVal) => {
                    if(!this.isblockuser){
                        let joinData = firebase.database().ref('chatrooms/' + this.receiver + '/chats/').push();

                        let url = "/usermessages/send";
                        let params = {
                            receiver: this.receiver,
                            message: chatmessage.message,
                        };
                        if (chatmessage.file) {
                            joinData.set({
                                user_id: this.sender,
                                message: chatmessage.message,
                                file: 1
                            });
                        } else {
                            this.axios.post(url, params).then((response) => {
                                if (response.status) {
                                    joinData.set({
                                        user_id: this.sender,
                                        message: chatmessage.message,
                                        file: 0,
                                    });
                                    firebase.database().ref('chatrooms/' + this.receiver + '/read/' + this.sender).update({read : false});
                                }
                            });
                        }
                        this.last_message_read = false;
                        this.$emit('messagesent');
                    }else{
                        alert('You are blocked by this user.');
                        return false;
                    }
                });
            },
            checkblockuser() {
                let url = "/user/isblockuser";

                let params = {
                    receiver: this.receiver,
                    sender: this.sender,
                    type: this.chat_type
                };

                return this.axios.post(url, params).then((response) => {
                    // 1: block, 0: unblock
                    this.isblockuser = response.data;
                });
            },
            fetchMessages() {
                let url = "/usermessages/fetch";

                let params = {
                    sender: this.sender,
                    receiver: this.receiver,
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
                this.$nextTick(function () {
                    $(scrollingElement).animate({
                        scrollTop: scrollingElement.scrollHeight
                    }, 400);
                });
            },
            scrollReached(){
                let scrollingElement = this.$refs.chatbody;
                let scrollBottom = scrollingElement.scrollHeight - scrollingElement.scrollTop;
                if(scrollBottom > 500) {
                    this.show_scroll_btn = true;
                } else {
                    this.show_scroll_btn = false;
                }
            },
            isClosed(){
                return this.$parent.chatuserlist.filter(chat => chat.to == this.receiver)[0] ? false : true;
            },
            linkify(text) {
                var urlRegex =/(\b(https?|http):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
                let url = text.replace(urlRegex, function(url) {
                    if(!url.includes('http')) {
                        url = 'https://' + url;
                    }
                    return '<a href="' + url + '">' + url + '</a>';
                });
                return url;
            },
            strip_tags (input, allowed) {
                allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
                var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
                    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
                return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
                    return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
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
            this.fetchMessages();
            let _this = this;
            ////// Received Message
            firebase.database().ref('chatrooms/' + _this.sender + '/chats/').remove();
            let starCountRef = firebase.database().ref('chatrooms/' + _this.sender + '/chats/').limitToLast(1);
            starCountRef.on('value', function (snapshot) {
                snapshot.forEach((doc) => {
                    let item = doc.val();
                    if (item.user_id == _this.receiver) {
                        let message = {
                            message: item.message,
                            type: 0,
                            file: item.file,
                        };
                        _this.messages.push(message);
                        if(!_this.isClosed()){
                            _this.readmessage();
                        }
                        // _this.$root.getUnreads();
                        _this.$parent.getuserlist();
                    }
                });
            });

            ////// Sent Message

            let starCountRef1 = firebase.database().ref('chatrooms/' + _this.receiver + '/chats/').limitToLast(1);
            starCountRef1.on('value', function (snapshot) {
                snapshot.forEach((doc) => {
                    let item = doc.val();
                    if (item.user_id == _this.sender && _this.loaded) {
                        let first_unread = _this.messages.filter(msg => msg.first_unread == 1)[0];
                        let message = {
                            message: item.message,
                            type: 1,
                            file: item.file,
                            first_unread: first_unread ? 0 : 1,
                        };
                        _this.messages.push(message);
                        _this.$parent.getuserlist();
                    }
                });
                _this.loaded = true;
            });

            this.readmessage();
            let readRef1 = firebase.database().ref('chatrooms/' + _this.receiver + '/read/' + _this.sender);
            readRef1.on('value', function (snapshot) {
                let item = snapshot.val();
                _this.last_message_read = item ? item.read : false;
                if (item && item.read == true) {
                    _this.last_message_read = true;
                    _this.messages.map((message) => {
                        if(!message.read && message.type) {
                            message.read = 1;
                            message.first_unread = 0;
                        }
                    });
                    let scrollingElement = _this.$refs.chatbody;
                    $(scrollingElement).animate({
                        scrollTop: scrollingElement.scrollHeight
                    }, 100);
                }
            });

            let typing = firebase.database().ref('chatrooms/' + _this.receiver + '/is_typing/' + _this.sender);
            typing.on('value', function (snapshot) {
                _this.receiver_is_typing = snapshot.val() ? snapshot.val().is_typing : false;
            });
            // this.$root.getUnreads();
        },
    }
</script>

<style lang="scss">
    .chatwindow {
        height: 400px;
        width: 270px;
        background-color: black;
        margin-right: 3px;
        border: 1px solid white;

        p {
            color: #EFA720;
            margin: 0;
        }

        .chat_header {
            padding: 5px;
            display: flex;
            align-items: center;
            position: relative;
            border-bottom: 1px solid white;
            .username {
                cursor: pointer;
                font-size: 16px;
                width: calc(100% - 70px);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 30px;
                &.portal {
                    line-height: 14px;
                }
            }
            .store_type {
                img {
                    width: 15px;
                    height: 15px;
                }
            }

            svg {
                margin-right: 5px;
            }

            .tools {
                position: absolute;
                right: 5px;
                color: #EFA720;
                font-size: 14px;
                display: flex;
                align-items: center;
            }
        }

        .chatbody {
            background-color: white;
            height: calc(100% - 106px);

            .chatmessage {
                display: block;
                clear: both;
                .chat-profile-img {
                    border-radius: 100px;
                    width: 25px;
                    height: 25px;
                    float: right;
                    object-fit: cover;
                }

                &.sent {
                    p {
                        float: right;
                        margin: 0 5px 3px 0;
                        background-color: #96c83c;

                        &:after {
                            right: -5px;
                            left: unset;
                            border-top: 10px solid #96c83c;
                        }
                    }
                }

                P {
                    position: relative;
                    margin: 0 0 3px 5px;
                    float: left;
                    max-width: 70%;
                    width: fit-content;
                    padding: 5px 10px;
                    background-color: #EFA720;
                    border-radius: 5px;
                    word-break: break-word;
                    color: black;
                    line-height: 16px;

                    .message-text {
                        color: black;
                        font-weight: 700;
                        font-size: 14px;
                        white-space: pre-wrap;
                        // margin-bottom: 3px;
                        // letter-spacing: -1px;
                        // word-spacing: -3px;
                        a {color: blue;}
                    }

                    span.message-time {
                        display: block;
                        font-size: 12px;
                        text-align: right;
                    }

                    img {
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
            right: 5px;
            bottom: 55px;
            cursor: pointer;
        }

        .action {
            height: 25px;
            background: white;
        }

        .chatfooter {
            .emojionearea {
                padding-right: 35px !important;
                .emojionearea-editor {
                    padding: 6px;
                    white-space: normal !important;
                }
            }
            .emojionearea-button {
                background-color: black !important;
                right: 2px !important;
                opacity: 1 !important;

                .emojionearea-button-open {
                    background-image: url('/imgs/emoji.png') !important;
                }
            }
        }
    }
</style>
