<template>
    <div class="row justy-content-center" :class="{menu_media :  media.menu && media.menu.is_active}" id="media-page">
        <div class="col-md-6">
            <div class="comment_box_mobile" v-if="$device.isMobile">
                <div class="header">
                    <div class="row">
                        <div class="col-9">
                            <div class="user_detail">
                                <div class="profile_image">
                                    <a :href="media.user.username">
                                        <img :src="media.user.profile_pic ? media.user.profile_pic.url : default_logo" alt />
                                    </a>
                                </div>
                                <div class="username">
                                    <p>
                                        <a :href="media.user.username">{{media.user.name}}</a>
                                        <span style="padding-left: 5px;" v-show="logged_user_id != media.user_id">•</span>
                                        <span class="followuser" @click="follow()" v-show="logged_user_id != media.user_id" v-if="!isfollower">Follow</span>
                                        <img src="/imgs/unfollow.png" alt class="pf-unfollow" @click="unfollow()" v-show="logged_user_id != media.user_id" v-if="isfollower" />
                                    </p>
                                    <p v-if="media.user.type != 'user'">
                                        <img src="/imgs/dispensary.png" alt v-if="media.user.store_type === 1 || media.user.store_type === 3" />
                                        <img src="/imgs/delivery.png" alt v-if="media.user.store_type === 2 || media.user.store_type === 3" />
                                        <img src="/imgs/brand.png" alt v-if="media.user.type =='brand'" />
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-3 action">
                            <a href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-if="media.loged_user || media.user_role === 1">
                                <fa icon="ellipsis-h"></fa>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <a v-if="media.model === 'post'" class="dropdown-item" href="#" @click.prevent="showEditModal()">Edit</a>
                                <!-- <EditMedia :mainData="user" :editData="media.id" mode="edit"></EditMedia> -->
                                <a class="dropdown-item" href="#" @click.prevent="deleteMedia()">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="media">
                <img :src="media.url" alt v-if="media.type == 'image'" />
                <video :src="media.url" alt v-if="media.type == 'video'" disablePictureInPicture controlsList="nodownload" onclick="this.paused ? this.play() : this.pause();"></video>
                <img class="video__tag__mobile" style="width:35px;" v-if="media.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
            </div>
            <div class="icon-panel clearfix media_toolbar" v-if="$device.isMobile">
                <div class="heart_icon" @click="likepost" v-if="!media.user_liked">
                    <fa :icon="['far', 'heart']" fixed-width></fa>
                    <p>{{media.likes}}</p>
                </div>
                <div class="heart_icon" @click="unlikepost" v-else>
                    <fa icon="heart" fixed-width></fa>
                    <p>{{media.likes}}</p>
                </div>
                <div class="comment_icon" @click="focuscomment">
                    <fa :icon="['far', 'comment']" fixed-width></fa>
                </div>
                <div class="bookmark_icon" @click="addbookmark" v-if="!media.user_saved">
                    <fa :icon="['far', 'bookmark']" fixed-width></fa>
                </div>
                <div class="bookmark_icon" @click="removebookmark" v-else>
                    <fa icon="bookmark" fixed-width></fa>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="commentbox">
                <div class="header" v-if="!$device.isMobile">
                    <div class="row">
                        <div class="col-9">
                            <div class="user_detail">
                                <div class="profile_image">
                                    <a :href="media.user.username">
                                        <img :src="media.user.profile_pic ? media.user.profile_pic.url : default_logo" alt />
                                    </a>
                                </div>
                                <div class="username">
                                    <p>
                                        <a :href="media.user.username">{{media.user.name}}</a>
                                        <span style="padding-left: 5px;" v-show="logged_user_id != media.user_id">•</span>
                                        <span class="followuser" @click="follow()" v-show="logged_user_id != media.user_id" v-if="!isfollower">Follow</span>
                                        <img src="/imgs/unfollow.png" alt class="pf-unfollow" @click="unfollow()" v-show="logged_user_id != media.user.id" v-if="isfollower" />
                                    </p>
                                    <p v-if="media.user.type != 'user'">
                                        <img src="/imgs/dispensary.png" alt v-if="media.user.store_type === 1 || media.user.store_type === 3" />
                                        <img src="/imgs/delivery.png" alt v-if="media.user.store_type === 2 || media.user.store_type === 3" />
                                        <img src="/imgs/brand.png" alt v-if="media.user.type == 'brand'" />
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-3 action">
                            <a href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-if="media.loged_user || media.user_role === 1">
                                <fa icon="ellipsis-h"></fa>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <a v-if="media.model === 'post'" class="dropdown-item" href="#" @click.prevent="showEditModal()">Edit</a>
                                <!-- <EditMedia :mainData="user" :editData="media.id" mode="edit"></EditMedia> -->
                                <a class="dropdown-item" href="#" @click.prevent="deleteMedia()">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="comment_body">
                    <div class="image_description" v-if="media.description">
                        <div class="userlogo">
                            <a :href="media.user.username">
                                <img :src="media.user.profile_pic ? media.user.profile_pic.url : default_logo" alt />
                            </a>
                        </div>
                        <div class="description">
                            <a :href="media.user.username">
                                <span class="username">{{media.user.name}}</span>
                            </a>
                            <span>{{media.description}}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center" v-if="loading">
                        <div class="spinner-border text-light" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <div class="comment_container" :key="index" v-for="(item, index) in comments" v-if="!loading">
                        <div class="comment">
                            <div class="userlogo">
                                <a :href="item.user.username">
                                    <img :src="item.user.profile_pic ? item.user.profile_pic.url : default_logo" />
                                </a>
                            </div>

                            <div class="description">
                                <a :href="item.user.username">
                                    <span class="username">{{item.user.name}}</span>
                                </a>
                                <span style="white-space:pre-line">{{item.text}}</span>
                                <p class="status">
                                    <span class="likes" v-if="item.likes > 0 && item.likes == 1">{{item.likes}} Like.</span>
                                    <span class="likes" v-if="item.likes > 0 && item.likes > 1">{{item.likes}} Likes.</span>
                                    <span class="reply" @click="replyto(item.id, item.user)">Reply</span>
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
                                    <a :href="sub_item.user.username">
                                        <img :src="sub_item.user.profile_pic ? sub_item.user.profile_pic.url : default_logo" alt />
                                    </a>
                                </div>
                                <div class="description">
                                    <a :href="sub_item.user.username">
                                        <span class="username">{{sub_item.user.name}}</span>
                                    </a>
                                    <a :href="sub_item.parent.user.username">
                                        <span class="text-primary">@{{sub_item.parent.user.name}} </span>
                                    </a>
                                    <span style="white-space:pre-line">{{sub_item.text}}</span>
                                    <p class="status">
                                        <span class="likes" v-if="sub_item.likes > 0 && sub_item.likes == 1">{{sub_item.likes}} Like.</span>
                                        <span class="likes" v-if="sub_item.likes > 0 && sub_item.likes > 1">{{sub_item.likes}} Likes.</span>
                                        <span class="reply" @click="replyto(sub_item.id, sub_item.user)">Reply</span>
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
                                        <a :href="sub2_item.user.username">
                                            <img :src="sub2_item.user.profile_pic ? sub2_item.user.profile_pic.url : default_logo" alt />
                                        </a>
                                    </div>
                                    <div class="description">
                                        <a :href="sub2_item.user.username">
                                            <span class="username">{{sub2_item.user.name}}</span>
                                        </a>
                                        <a :href="sub2_item.parent.user.username">
                                            <span class="text-primary">@{{sub2_item.parent.user.name}} </span>
                                        </a>
                                        <span style="white-space:pre-line">{{sub2_item.text}}</span>
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
                        <!-- End Subomments -->
                    </div>
                </div>
                <div class="footer">
                    <div class="menu-panel" v-if="media.menu && media.menu.is_active" :class="{'brand-menu': media.user.type == 'brand'}">
                        <p class="menu-category">
                            <span>{{media.menu.category.price_type == 2 ? 'Flower | ' + media.menu.category.name : media.menu.category.name}}</span>
                            <span>{{media.menu.strain ? ' | ' + media.menu.strain.name : ''}}</span>
                        </p>
                        <p class="item_name">{{media.menu.item_name}}</p>
                        <p class="price_data mb-0" v-if="media.menu.portal && media.user.type == 'company'">
                            <span class="text-white pr-2" v-if="media.menu.price_each != null">{{media.menu.price_each}}<sup class="text-420">each</sup></span>
                            <span class="text-white pr-2" v-if="media.menu.price_half_gram != null">{{media.menu.price_half_gram}}<sup class="text-420">1/2g</sup></span>
                            <span class="text-white pr-2" v-if="media.menu.price_gram != null">{{media.menu.price_gram}}<sup class="text-420">g</sup></span>
                            <span class="text-white pr-2" v-if="media.menu.price_eighth != null">{{media.menu.price_eighth}}<sup class="text-420">1/8</sup></span>
                            <span class="text-white pr-2" v-if="media.menu.price_quarter != null">{{media.menu.price_quarter}}<sup class="text-420">1/4</sup></span>
                            <span class="text-white pr-2" v-if="media.menu.price_half_oz != null">{{media.menu.price_half_oz}}<sup class="text-420">1/2</sup></span>
                            <span class="text-white pr-2" v-if="media.menu.price_oz != null">{{media.menu.price_oz}}<sup class="text-420">oz</sup></span>
                        </p>
                    </div>
                    <div class="icon-panel clearfix" v-if="!$device.isMobile">
                        <div class="heart_icon" @click="likepost" v-if="!media.user_liked">
                            <fa :icon="['far', 'heart']" fixed-width></fa>
                            <p>{{media.likes}}</p>
                        </div>
                        <div class="heart_icon" @click="unlikepost" v-else>
                            <fa icon="heart" fixed-width></fa>
                            <p>{{media.likes}}</p>
                        </div>
                        <div class="comment_icon" @click="focuscomment">
                            <fa :icon="['far', 'comment']" fixed-width></fa>
                        </div>
                        <div class="bookmark_icon" @click="addbookmark" v-if="!media.user_saved">
                            <fa :icon="['far', 'bookmark']" fixed-width></fa>
                        </div>
                        <div class="bookmark_icon" @click="removebookmark" v-else>
                            <fa icon="bookmark" fixed-width></fa>
                        </div>
                    </div>

                    <div class="comment_area main_comment">
                        <textarea id="main_comment" name="comment" class="comment_text"></textarea>
                        <p class="link_reply mt-3" v-if="status != null">
                            Reply to <a class="reply_to" :href="replyusername">{{replyname}}</a>
                            <span @click="reset" class="reset_reply">
                                <fa icon="times" fixed-width></fa>
                            </span>
                        </p>
                        <button @click="postcomment" v-if="!editComment">Post</button>
                        <button @click="savecomment" v-else>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <vs-popup class="strains__popup media__add" type="border" title="Edit Media" :active.sync="editMedia">
            <add-form :mainData="media" :editData="media.id" mode="edit"></add-form>
        </vs-popup>
    </div>
</template>
<script>
    import firebase from "../../Firebase";
    import AddForm from "./AddForm";
    import { mapGetters } from "vuex";
    export default {
        name: 'ShowMedia',
        props: ['selected'],
        components: {
            AddForm,
        },
        data(){
            return {
                media : this.selected,
                editMedia: false,
                comments: [],
                loading: true,
                status: null,
                replyid: null,
                replyname: "",
                replyusername: null,

                editComment: false,
                editCommentId: null,
                editCommentIndex: null,
                editSubCommentIndex: null,
                editSub2CommentIndex: null,

                editText: '',
                default_logo: '/imgs/default_sm.png',
                isfollower: 0,
                logged_user_id: this.auth_user ? this.auth_user.id : '',
            };
        },
        computed: mapGetters({
            auth_user: 'auth/user',
        }),
        mounted() {
            this.init();
            var self = this;
            $(document).ready(function () {
                $("#main_comment").emojioneArea({
                    pickerPosition: "top",
                    search: false,
                    autocomplete: false,
                    placeholder: "Write a comment...",
                    events: {
                        keypress: function (editor, event) {
                            if(event.which == 13 && !event.shiftKey){
                                self.postcomment();
                            }
                        },
                    }
                });
            });
        },
        methods: {
            init() {
                this.getcomment();
            },
            getcomment() {
                let uri = "/comment/getall";
                let params = {
                    target_id: this.media.id,
                    target_model: 'media',
                };
                this.loading = true;
                this.axios.post(uri, params).then(response => {
                    this.comments = response.data;
                    this.loading = false;
                });
            },
            deleteMedia() {
                if (!window.confirm('Are you sure?')) {
                    return false;
                }
                let uri = `/media/${this.media.id}`;
                this.axios.delete(uri).then(res => {                    
                    toastr["success"]("Media has been deleted.", "");
                    window.history.go(-1);
                });
            },
            deletecomment(id) {
                if (window.confirm("Are you sure?")) {
                    let uri = "/comment/deletecomment";
                    let params = {
                        comment_id: id,
                        target_id: this.media.id
                    };
                    this.axios.post(uri, params).then(response => {
                        this.comments = response.data;
                    });
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
                $("#main_comment").data("emojioneArea").setText(comment.text);
            },
            savecomment() {
                let id = this.editCommentId;
                let index = this.editCommentIndex;
                let subindex = this.editSubCommentIndex;
                let sub2index = this.editSub2CommentIndex;
                let text = $("#main_comment").data("emojioneArea").getText();
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
                    })
                    .catch(error => {
                        console.log(error);
                    });
                this.reset();
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
                $("#main_comment").data("emojioneArea").setText('');
            },
            likecomment(id, index, parent1 = null, parent2 = null) {
                if (this.auth_user) {
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
                        if(this.auth_user.id != action_comment.user_id) {                            
                            let noti_fb = firebase.database().ref('notifications/' + action_comment.user_id).push();
                            noti_fb.set({
                                notifier_id: this.auth_user.id,
                                type: 'like_comment',
                            });
                        }
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            unlikecomment(id, index, parent1 = null, parent2 = null) {
                if (this.auth_user) {
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
            replyto(id, user) {
                let name = user.type == 'user' ? user.username : user.name;
                this.status = "reply";
                this.replyid = id;
                this.replyname = "@" + name;
                this.replyusername = user.username;
                $("#main_comment").data("emojioneArea").editor.focus();
                $("#main_comment").data("emojioneArea").setText("@" + name + ' ');
                this.setCursor();
            },
            addbookmark() {
                if (this.auth_user) {
                    let uri = "/bookmark/store";
                    let params = {
                        target_id: this.media.id
                    };
                    this.axios.post(uri, params).then(response => {
                        this.media.user_saved = response.data;
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            removebookmark() {
                let uri = "/bookmark/delete";
                let params = {
                    target_id: this.media.id
                };
                this.axios.post(uri, params).then(response => {
                    this.media.user_saved = response.data;
                });
            },
            focuscomment() {
                if (this.auth_user) {
                    $(".main_comment .emojionearea-editor").focus();
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            postcomment() {

                let comment = $("#main_comment ").data('emojioneArea').getText();

                if (this.auth_user) {
                    if (comment == "") {
                        return true;
                    } else {
                        const params = {
                            text: comment,
                            target_id: this.media.id,
                            target_model: "media",
                            reply: this.replyid
                        };

                        let uri = "/comment/add";

                        this.axios.post(uri, params).then(response => {
                            this.comments = response.data;
                            if(this.auth_user.id != this.media.user_id) {                                
                                let noti_fb = firebase.database().ref('notifications/' + this.media.user.id).push();
                                if(this.replyid) {
                                    noti_fb.set({
                                        notifier_id: this.auth_user.id,
                                        type: 'reply',
                                    });
                                } else {
                                    noti_fb.set({
                                        notifier_id: this.auth_user.id,
                                        type: 'comment',
                                    });
                                }
                            }

                            this.reset();
                        });
                    }
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            likepost() {
                if (this.auth_user) {
                    const params = {
                        target_id: this.media.id,
                        target_model: "post"
                    };
                    let uri = "/like/addlike";
                    this.axios.post(uri, params).then(response => {
                        this.media.likes = response.data;
                        this.media.user_liked = true;
                        if(this.auth_user.id != this.media.user_id) {                           
                            let noti_fb = firebase.database().ref('notifications/' + this.media.user_id).push();
                            noti_fb.set({
                                notifier_id: this.auth_user.id,
                                type: 'like',
                            }); 
                        }
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            unlikepost() {
                if (this.auth_user) {
                    const params = {
                        target_id: this.media.id,
                        target_model: "post"
                    };
                    let uri = "/like/unlike";
                    this.axios.post(uri, params).then(response => {
                        this.media.likes = response.data;
                        this.media.user_liked = false;
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            setCursor() {
                var tag = document.querySelector('.main_comment .comment_text .emojionearea-editor');
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
            getIsFollower(post) {
                if (this.auth_user) {
                    let uri = "/profile/getisfollower";
                    let params = {
                        user_id: this.auth_user.id,
                        follower_user_id: post.user.id
                    }
                    if (post.portal_id){
                        uri = "/profilecompany/getisfollower";
                        params.follower_company_id = post.portal_id;
                    }

                    this.axios.post(uri, params).then(response => {
                        if (response.data.status == 1)
                            this.isfollower = 1;
                        else
                            this.isfollower = 0;
                    });
                }
            },
            follow() {
                if (this.auth_user) {
                    let uri = '/user/follow';
                    let params = {
                        user_id: this.auth_user.id,
                        follower_id: this.media.user_id
                    };
                    this.axios.post(uri, params).then(response => {
                        this.isfollower = 1;
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },
            unfollow() {
                if (this.auth_user) {
                    let uri = '/user/unfollow';
                    let params = {
                        user_id: this.auth_user.id,
                        follower_id: this.media.user_id
                    };
                    this.axios.post(uri, params).then(response => {
                        this.isfollower = 0;
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },            
            showEditModal() {
                this.editMedia = true;
            },
        },
    }
</script>

<style lang="scss" scoped>
    #media-page {
        @media only screen and (min-width: 600px) {
            margin-top: 20px;    
            border: solid 1px white;
        }
        .media {           
            height: 500px;
            margin: auto;
            padding: 15px 0;
            position: relative;
            align-items: center;
            justify-content: center;
            img {
                width: 100%;
                max-height: 100%;
                object-fit: cover;
            }
            video {
                height: 100%;
                max-width: 100%;
            }
            .video__tag__mobile {
                position: absolute;
                height: unset;
                top: 5px;
                right: 10px;
            }
        }
        @media only screen and (max-width: 600px) {
            .media {
                height: 400px;
            }
        }
        .commentbox {
            height: 500px;            
            position: relative;
            @media only screen and (max-width: 600px) {
                height: 350px;
            }
            .header {
                border-bottom: 1px solid grey;
                .user_detail {
                    padding: 5px;
                    margin-left: 10px;
                    display: flex;
                    align-items: center;
                    .profile_image {
                        background-image: url("/imgs/profile-bg.png");
                        background-size: cover;
                        border-radius: 50%;
                        width: 45px;
                        height: 45px;
                        padding: 5px;
                        img {
                            width: 35px;
                            height: 35px;
                            border-radius: 50%;
                            object-fit: cover;
                        }
                    }
                    .username {
                        margin-left: 5px;
                        p {
                            margin: 0;
                            color: white;
                            width: max-content;
                            a {
                                color: white;
                                font-size: 16px;
                                text-decoration: none;
                            }
                            .followuser {
                                margin-left: 5px;
                                color: #EFA720;
                                cursor: pointer;
                                font-size: 16px;
                            }
                            img {
                                width: 25px;
                                cursor: pointer;
                            }
                        }
                    }
                }
                .action {
                    display: flex;
                    justify-content: flex-end;
                    align-items: center;
                    padding-right: 30px;
                    i {
                        margin-right: 5px;
                        color: #EFA720;
                        font-size: 20px;
                    }
                    .dropdown-menu {
                        background-color: black;
                        border: 1px solid grey;
                        a {
                            color: #EFA720;
                        }
                    }
                }
            }
            .comment_body {
                height: calc(100% - 182px);
                @media only screen and (max-width: 600px) {
                    height: calc(100% - 70px);
                    padding-bottom: 10px;
                }
                .comment {
                    padding: 5px 10px;
                }
            }

            .footer {
                position: absolute;
                width: 100%;
                bottom: 10px;
                padding: 2px 10px;
                background-color: #000;
                i {
                    font-size: 26px;
                    color: #EFA720;
                }
                .heart_icon {
                    float: left;
                    margin-left: 10px;
                    p {
                        text-align: center;
                        margin: 0;
                        color: white;
                    }
                }
                .comment_icon {
                    float: left;
                    margin-left: 30px;
                }
                .bookmark_icon {
                    float: right;
                    margin-right: 10px;
                }
                .comment_area {
                    position: relative;
                    height: 50px;
                    .comment_text {
                        background-color: transparent;
                        color: white;
                    }
                    button {
                        background-color: #EFA720;
                        padding: 2px 4px;
                        border: none;
                        position: absolute;
                        top: 12px;
                        right: 30px;
                    }
                    .emojionearea-editor {
                        padding-right: 20px;
                    }                    
                }
            }
        }
        .comment_box_mobile {
            height: unset;
            position: relative;
            .header {
                border-bottom: 1px solid grey;
                .user_detail {
                    padding: 5px;
                    margin-left: 10px;
                    display: flex;
                    align-items: center;
                    .profile_image {
                        background-image: url("/imgs/profile-bg.png");
                        background-size: cover;
                        border-radius: 50%;
                        width: 45px;
                        height: 45px;
                        padding: 5px;
                        img {
                            width: 35px;
                            height: 35px;
                            border-radius: 50%;
                            object-fit: cover;
                        }
                    }
                    .username {
                        margin-left: 5px;
                        p {
                            margin: 0;
                            color: white;
                            width: max-content;
                            a {
                                color: white;
                                font-size: 16px;
                                text-decoration: none;
                            }
                            .followuser {
                                margin-left: 5px;
                                color: #EFA720;
                                cursor: pointer;
                                font-size: 16px;
                            }
                            img {
                                width: 25px;
                                cursor: pointer;
                            }
                        }
                    }
                }
                .action {
                    display: flex;
                    justify-content: flex-end;
                    align-items: center;
                    padding-right: 30px;
                    i {
                        margin-right: 5px;
                        color: #EFA720;
                        font-size: 20px;
                    }
                    .dropdown-menu {
                        background-color: black;
                        border: 1px solid grey;
                        a {
                            color: #EFA720;
                        }
                    }
                }
            }
        }
        .comment_body {
            position: relative;
        }

        .comment_status {
            position: absolute;
            bottom: 0;
            width: 100%;
            margin: 0;
            background-color: #656565;
            color: white;
            padding: 0 10px;
        }

        .comment_status span {
            float: right;
        }

        .link_reply {
            position: absolute;
            // bottom: -15px;
            left: 13px;
            .reset_reply i {
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

        .menu-panel {
            // position: absolute;
            // bottom: 0;
            width: 100%;
            border-top: solid 1px #FFF;
            padding: 0 8px;
            .menu-category {
                color: gray;
                margin-bottom: 0;
                font-size: 13px;
            }
            .item_name {
                color: #EFA720;
                margin-bottom: 0;
                font-size: 18px;
                line-height: 19px;
            }
            &.brand-menu {
                padding-bottom: 6px;
            }
            .price_data {
                span {
                    sup {
                        padding-left: 3px;
                    }
                }
            }
        }
        .icon-panel {
            padding-top: 3px;
            border-top: solid 1px grey;
            border-bottom: solid 1px grey;
        }
        &.menu_media .comment_body {
            height: calc(100% - 250px);
        }
    }
    .media_toolbar {
        i {
            font-size: 26px;
            color: #EFA720;
        }
        .heart_icon {
            float: left;
            margin-left: 10px;
            p {
                text-align: center;
                margin: 0;
                color: white;
            }
        }
        .comment_icon {
            float: left;
            margin-left: 30px;
        }
        .bookmark_icon {
            float: right;
            margin-right: 10px;
        }
    }
</style>
<style lang="scss">
    #media-page {
        .emojionearea .emojionearea-button {
            top: 10px;
        }
        .emojionearea .emojionearea-editor {
            white-space: normal !important;
        }
    }
</style>