<template>
    <div class="row">
        <div class="strains__sticky">
            <ul class="nav strains__nav">
                <p style="margin: auto 5px; font-size: 22px;color:#EFA720;" class="p-2">
                    <fa icon="arrow-left" @click="$router.go(-1)" class="mr-2" /> Comments                    
                </p>
            </ul>
        </div>
        <div class="comment_body pt-2">
            <div class="comment_header" v-if="media.description">
                <div class="media_user my-0 px-2 clearfix" v-if="media.portal">
                    <img :src="serverUrl(media.portal.media.url)" v-if="media.portal.media" alt @click="goProfile(media.portal.name)" />
                    <img v-else src="~assets/imgs/default.png" @click="goProfile(media.portal.name)" />
                    <div class="username">
                        <span @click="goProfile(media.portal.name)">{{media.portal.username}}</span>
                        <span class="description text-white pl-2" style="white-space:pre-line;">{{media.description}}</span>
                        <div class="user_icons">
                            <img src="~assets/imgs/delivery.png"  v-if="media.portal.store_type === 2 || media.portal.store_type === 3" alt />
                            <img src="~assets/imgs/dispensary.png" v-if="media.portal.store_type === 1 || media.portal.store_type === 3" alt />
                        </div>
                    </div>
                </div>
                <div class="media_user my-0 px-2 clearfix" v-else>
                    <img :src="serverUrl(media.user.profile_pic.url)" v-if="media.user.profile_pic" alt @click="goProfile(media.user.name)" />
                    <img v-else src="~assets/imgs/default.png" @click="goProfile(media.user.name)" />
                    <div class="username" style="padding-top: 12px;">
                        <span @click="goProfile(media.user.name)">{{media.user.name}}</span>
                        <span class="description text-white pl-2" style="white-space:pre-line;">{{media.description}}</span>
                    </div>
                </div>
            </div>
            <div class="comment_container" v-for="(item, index) in comments" v-if="!loading" :key="index">
                <div class="comment">
                    <div class="userlogo">
                        <a v-bind:href="item.user.name">
                            <img v-if="item.user.profile_pic" v-bind:src="serverUrl(item.user.profile_pic.url)" alt="">
                            <img v-else src="~assets/imgs/default.png" />
                        </a>
                    </div>
                    <div class="description">
                        <a v-bind:href="item.user.name"><span class="username">{{item.user.name}}</span></a>
                        <span v-html="linkify(item.text)"></span>
                        <p class="status">
                            <span class="likes" v-if="item.likes > 0 && item.likes == 1">{{item.likes}} Like.</span>
                            <span class="likes" v-if="item.likes > 0 && item.likes > 1">{{item.likes}} Likes.</span>
                            <span class="reply" @click="replyto(item.id, item.user.name)">Reply</span>
                        </p>
                    </div>
                    <div class="actions">
                        <fa icon="heart" @click="unlikecomment(item.id, index)" v-if="item.userliked"></fa>
                        <fa :icon="['far', 'heart']" @click="likecomment(item.id, index)" v-else></fa>
                        <fa icon="edit" v-if="item.owned" @click="editcomment(item.id, index)" />
                        <fa icon="trash-alt" v-if="item.owned" @click="deletecomment(item.id)" />
                    </div>
                </div>

                <!-- Sub Comment -->
                <div class="sub_comment_container" v-for="(sub_item, subindex) of item.sub_comment" :key="subindex">
                    <div class="sub_comment">
                        <div class="userlogo">
                            <a v-bind:href="sub_item.user.name">
                                <img v-if="sub_item.user.profile_pic" v-bind:src="serverUrl(sub_item.user.profile_pic.url)" alt />
                                <img v-else src="~assets/imgs/default.png" />
                            </a>
                        </div>
                        <div class="description">
                            <a v-bind:href="sub_item.user.name">
                                <span class="username">{{sub_item.user.name}}</span>
                            </a>
                            <a v-bind:href="sub_item.parent.user.name">
                                <span class="text-primary">@{{sub_item.parent.user.name}} </span>
                            </a>
                            <span v-html="linkify(sub_item.text)"></span>
                            <p class="status">
                                <span class="likes" v-if="sub_item.likes > 0 && sub_item.likes == 1">{{sub_item.likes}} Like.</span>
                                <span class="likes" v-if="sub_item.likes > 0 && sub_item.likes > 1">{{sub_item.likes}} Likes.</span>
                                <span class="reply" @click="replyto(sub_item.id, sub_item.user.name)">Reply</span>
                            </p>
                        </div>
                        <div class="actions">
                            <fa icon="heart" @click="unlikecomment(sub_item.id, subindex, index)" v-if="sub_item.userliked"></fa>                        
                            <fa :icon="['far', 'heart']" @click="likecomment(sub_item.id, subindex, index)" v-else></fa>
                            <fa icon="edit" v-if="sub_item.owned" @click="editcomment(sub_item.id, index, subindex)" />
                            <fa icon="trash-alt" v-if="sub_item.owned" @click="deletecomment(sub_item.id)" />
                        </div>
                    </div>
                    <!-- Sub 2 Comments -->
                    <div class="sub2_comment_container" v-for="(sub2_item, sub2index) of sub_item.sub2_comments" :key="sub2index">
                        <div class="sub2_comment">
                            <div class="userlogo">
                                <a v-bind:href="sub2_item.user.name">
                                    <img v-if="sub2_item.user.profile_pic" v-bind:src="serverUrl(sub2_item.user.profile_pic.url)" alt />
                                    <img v-else src="~assets/imgs/default_sm.png" />
                                </a>
                            </div>
                            <div class="description">
                                <a v-bind:href="sub2_item.user.name">
                                    <span class="username">{{sub2_item.user.name}}</span>
                                </a>
                                <a v-bind:href="sub2_item.parent.user.name">
                                    <span class="text-primary">@{{sub2_item.parent.user.name}} </span>
                                </a>
                                <span v-html="linkify(sub2_item.text)"></span>
                                <p class="status">
                                    <span class="likes" v-if="sub2_item.likes > 0 && sub2_item.likes == 1">{{sub2_item.likes}} Like.</span>
                                    <span class="likes" v-if="sub2_item.likes > 0 && sub2_item.likes > 1">{{sub2_item.likes}} Likes.</span>
                                    <!-- <span class="reply" @click="replyto(sub_item.id, sub_item.user.name)">Reply</span> -->
                                </p>
                            </div>
                            <div class="actions">
                                <fa icon="heart" @click="unlikecomment(sub2_item.id, sub2index, subindex, index)" v-if="sub2_item.userliked"></fa>                        
                                <fa :icon="['far', 'heart']" @click="likecomment(sub2_item.id, sub2index, subindex, index)" v-else></fa>
                                <fa icon="edit" v-if="sub2_item.owned" @click="editcomment(sub2_item.id, index, subindex, sub2index)" />
                                <fa icon="trash-alt" v-if="sub2_item.owned" @click="deletecomment(sub2_item.id)" />
                            </div>
                        </div>
                    </div>
                    <!-- End Sub2_comment -->
                </div>
            </div>

            <div class="d-flex justify-content-center" v-if="loading">
                <div class="spinner-border text-light" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>

        <div class="mobile_comment_box">
            <p class="link_reply mt-2" v-if="status != null">
                Reply to <a class="reply_to" :href="replyusername">{{replyname}}</a>
                <span @click="reset" class="reset_reply"><fa icon="times"></fa></span>
            </p>
            <textarea class="comment_text_box" id="mobile_comment" v-model="commenttext" onfocus="this.value = this.value;"></textarea>
            <a href="javascript:;" id="btn_post" @click="postcomment" v-if="!editComment"><fa :icon="['fab', 'telegram-plane']"></fa></a>
            <a href="javascript:;" id="btn_edit" @click="savecomment" v-else><fa :icon="['fab', 'telegram-plane']"></fa></a>
        </div>
    </div>
</template>

<script>
    import firebase from "../../Firebase";
    import { mapGetters } from 'vuex'

    export default {
        data() {
            return {
                emoji : null,
                target_id: null,
                comments: null,
                loading: true,
                commenttext: "",
                media: null,
                status: null,
                replyid: null,
                replyname: "",
                replyusername: null,
                editComment: false,
                editCommentId : null,
                editCommentIndex : null,
                editSubCommentIndex: null,
                editSub2CommentIndex: null,
                editText : '',
            }
        },
        watch: {
            "$route.params" : {
                handler : function(params) {
                    if(params.target) {
                        this.target_id = params.target;
                        this.getcomment(params.target);
                    }
                    if(params.media) {
                        this.media = params.media;
                    }
                },
                deep : true,
            }
        },
        computed: mapGetters({
            user: 'auth/user',
        }),
        methods: {
            getcomment(id) {
                let uri = '/comment/getall';
                let params = {
                    target_id: id,
                    target_model: 'media',
                };
                this.loading = true;
                this.axios.post(uri, params).then((response) => {
                    this.comments = response.data;
                    this.loading = false
                });
            },
            postcomment() {
                let comment_text = $("#mobile_comment").data("emojioneArea").getText();

                if (this.user) {
                    if (comment_text == "") {
                        return true;
                    } else {
                        const params = {
                            text: comment_text,
                            target_id: this.target_id,
                            target_model: "media",
                            reply: this.replyid
                        };

                        let uri = "/comment/add";

                        this.axios.post(uri, params).then(response => {
                            this.comments = response.data;
                            if(this.user.id != this.media.user_id) {
                                let noti_fb = firebase.database().ref('notifications/' + this.media.user_id).push();
                                if(this.replyid) {
                                    noti_fb.set({
                                        notifier_id: this.user.id,
                                        type: 'reply',
                                    });
                                } else {
                                    noti_fb.set({
                                        notifier_id: this.user.id,
                                        type: 'comment',
                                    });
                                }
                            }
                            this.reset();
                            // $("#mobile_comment").data("emojioneArea").setFocus();
                        });
                        this.reset();
                    }
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            editcomment(id, index, subindex = null, sub2index = null) {
                this.editComment = true;
                this.editCommentId = id;
                this.editCommentIndex = index;
                this.editSubCommentIndex = subindex;
                this.editSub2CommentIndex = sub2index;

                let comment = this.comments[index];
                if(subindex != null){
                    comment = this.comments[index].sub_comment[subindex];
                    if(sub2index != null) {
                        comment = this.comments[index].sub_comment[subindex].sub2_comments[sub2index];
                    }
                }
                $("#mobile_comment").data("emojioneArea").setText(comment.text);
            },
            savecomment() {
                let id = this.editCommentId;
                let index = this.editCommentIndex;
                let subindex = this.editSubCommentIndex;
                let sub2index = this.editSub2CommentIndex;

                let text = $("#mobile_comment").data("emojioneArea").getText();
                const params = {
                    id: id,
                    text: text,
                };

                let uri = "/comment/update";
                this.axios.post(uri, params).then(response => {
                        if(subindex != null && sub2index != null){
                            this.comments[index].sub_comment[subindex].sub2_comments[sub2index].text = text;
                        } else if(subindex != null && sub2index == null) {
                            this.comments[index].sub_comment[subindex].text = text;
                        } else {
                            this.comments[index].text = text;
                        }
                        this.reset();
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            reset() {
                this.status = null;
                this.replyid = null;
                this.replyname = "";
                this.editComment = false;
                this.editCommentId = null;
                this.editCommentIndex = null;
                this.editSubCommentIndex = null;
                this.editSub2CommentIndex = null;
                $("#mobile_comment").data("emojioneArea").setText('');
                $('#mobile_comment .emojionearea-editor').text('');
                $("#app").removeClass('focus_comment')
            },
            deletecomment(id) {
                if (window.confirm("Are you sure?")) {
                    let uri = "/comment/deletecomment";
                    let params = {
                        comment_id: id,
                        target_id: this.target_id,
                        target_model: 'media',
                    };
                    this.axios.post(uri, params).then(response => {
                        this.comments = response.data;
                    });
                }
            },
            likecomment(id, index, parent1 = null, parent2 = null) {
                if (this.user) {
                    let uri = "/comment/addlike";
                    let params = {
                        target_id: id
                    };
                    this.axios.post(uri, params).then(response => {
                        let action_comment = {};
                        if (parent1 != null && parent2 != null) {
                            // Sub2 Comment
                            action_comment = this.comments[parent2].sub_comment[parent1].sub2_comments[index];
                            this.comments[parent2].sub_comment[parent1].sub2_comments[index].userliked = response.data.userliked;
                            this.comments[parent2].sub_comment[parent1].sub2_comments[index].likes = response.data.likes
                        } else if (parent1 != null && parent2 == null) {
                            // Sub Comment
                            action_comment = this.comments[parent1].sub_comment[index];
                            this.comments[parent1].sub_comment[index].userliked = response.data.userliked;
                            this.comments[parent1].sub_comment[index].likes = response.data.likes;
                        } else {
                            // Comment
                            action_comment = this.comments[index];
                            this.comments[index].userliked = response.data.userliked;
                            this.comments[index].likes = response.data.likes;
                        }
                        if(this.user.id != action_comment.user_id) {                            
                            let noti_fb = firebase.database().ref('notifications/' + action_comment.user_id).push();
                            noti_fb.set({
                                notifier_id: this.user.id,
                                type: 'like_comment',
                            });
                        }
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            unlikecomment(id, index, parent1 = null, parent2 = null) {
                if (this.user) {
                    let uri = "/comment/unlike";
                    let params = {
                        target_id: id
                    };
                    this.axios.post(uri, params).then(response => {
                        if (parent1 != null && parent2 != null) {
                            // Sub2 Comment
                            this.comments[parent2].sub_comment[parent1].sub2_comments[index].userliked = response.data.userliked;
                            this.comments[parent2].sub_comment[parent1].sub2_comments[index].likes = response.data.likes
                        } else if (parent1 != null && parent2 == null) {
                            // Sub Comment
                            this.comments[parent1].sub_comment[index].userliked = response.data.userliked;
                            this.comments[parent1].sub_comment[index].likes = response.data.likes;
                        } else {
                            // Comment
                            this.comments[index].userliked = response.data.userliked;
                            this.comments[index].likes = response.data.likes;
                        }
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            replyto(id, name) {
                this.status = "reply";
                this.replyid = id;
                this.replyname = "@" + name;
                this.replyusername = name;
                // $("#mobile_comment").data("emojioneArea").setText("@" + name  + " ");
                if (!String.prototype.trim) {
                    String.prototype.trim = function () {
                        return this.replace(/^\s+|\s+$/gm, '');
                    };
                }
                $(".mobile_comment_box .emojionearea-editor").text("@" + name + ' ');
                this.setCursor();
            },
            setCursor() {
                var tag = document.querySelector(".mobile_comment_box .emojionearea-editor");
                // Creates range object
                var setpos = document.createRange();
                // Creates object for selection
                var set = window.getSelection();

                let len = tag.childNodes[0].length;
                setpos.setStart(tag.childNodes[0], len);
                // Collapse range within its boundary points
                // Returns boolean
                setpos.collapse(true);

                // Remove all ranges set
                set.removeAllRanges();

                // Add range with respect to range object.
                set.addRange(setpos);

                // Set cursor on focus
                tag.focus();
            },
            goProfile(username) {
                window.location.href = username;
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
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + 'imgs/default.png';
                }
            }
        },
        created() {
            this.target_id = this.$route.params.target;
            this.media = this.$route.params.media;
            this.getcomment(this.target_id);
        },
        mounted() {

            if(!this.media){
                let prev_page = localStorage.getItem("prev_page");
                if(prev_page == 'homepage') { window.location.href = '/';}
                else if(prev_page == 'company_page') {
                    window.location.href = localStorage.getItem('portal_username');
                } else if(prev_page == 'user_page') {
                    window.location.href = localStorage.getItem('username');
                } else {
                    window.location.href = '/';
                }
            }

            var self = this;
            this.emoji = $("#mobile_comment").emojioneArea({
                pickerPosition: "top",
                search: false,
                autocomplete: false,
                placeholder: "Write a comment...",
                events: {
                    blur: function (editor, event) {
                        if(event.relatedTarget && event.relatedTarget.id == 'btn_edit') {
                            self.savecomment();
                        }
                        if(event.relatedTarget && event.relatedTarget.id == 'btn_post') {

                        } else {
                            $("#app").removeClass('focus_comment');
                        }
                    },
                    focus: function (editor, event) {
                        $("#app").addClass('focus_comment');
                    }
                }
            });
        },
    }
</script>

<style lang="scss" scoped>
    .strains__sticky {
        position: fixed;
        top: 65px;
        width: 100%;
        background-color: #000;
    }
    .comment_body {
        margin-top: 50px;
        overflow-y: auto;
        height: calc(100vh - 200px);
    }
    .focus_comment {
        .comment_body {
            height: calc(100% - 172px);
            padding-bottom: 45px;
        }
    }
    #btn_post, #btn_edit {
        position: absolute;
        top: 1px;
        right: 35px;
        color: #EFA720;
    }
    .media_user {
        .username {
            color: #EFA720;
            float: left;
            margin-left: 10px;
        }

        img {
            margin-top: 7px;
            width: 35px;
            height: 35px;
            border-radius: 100px;
            border: solid 2px white;
            float: left;
        }
    }
    .user_icons {
        clear: both;
        img {
            margin-top: 0;
            width: 25px;
            height: 25px;
            border: none;
        }
    }
    .link_reply {
        position: absolute;
        left: 13px;
        bottom: 32px;
        color: #EFA720;
        z-index: 1;
        .reset_reply svg {
            color: #EFA720;
            font-size: 15px !important;
        }
    }
    .reply {
        cursor: pointer;
    }
    .reply_to {
        cursor: pointer;
        color: blueviolet;
    }
    .reply_to:hover {
        text-decoration: underline;
    }    
</style>

<style lang="scss">
    .mobile_comment_box {   
        .emojionearea-button {
            top: 7px;
            div {
                background-image: url(https://i.imgur.com/xljqgrH.png) !important;
            }
        } 
    }
</style>