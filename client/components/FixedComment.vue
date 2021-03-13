<template>
    <div class="right_side" :class="{comment_fixed: comment_fixed}" v-show="selected">
        <div class="comments" id="fixedcommentbox" :class="{ comments_menu: selected && selected.menu && selected.menu.is_active }">
            <div class="header" v-if="selected">
                <div class="row">
                    <div class="col-10 pr-0">
                        <div class="user_detail">
                            <div class="profile_image">
                                <a :href="'/'+selected.user.username" v-if="selected.user">
                                    <img :src="serverUrl(selected.user.profile_pic ? selected.user.profile_pic.url : default_logo)" alt />
                                </a>
                            </div>
                            <div class="username">
                                <p v-if="selected.user">
                                    <a :href="'/'+selected.user.username">{{selected.user.name}}</a>
                                    <span style="padding-left: 5px;" v-show="logged_user_id != selected.user_id">•</span>
                                    <span class="followuser" @click="follow()" v-show="logged_user_id != selected.user_id" v-if="!isfollower">Follow</span>
                                    <img src="~assets/imgs/unfollow.png" alt class="pf-unfollow" @click="unfollow()" v-show="logged_user_id != selected.user_id" v-if="isfollower" />
                                </p>
                                <p v-if="selected.user.type != 'user'">
                                    <img src="/imgs/dispensary.png" alt v-if="selected.user.store_type === 1 || selected.user.store_type === 3" />
                                    <img src="/imgs/delivery.png" alt v-if="selected.user.store_type === 2 || selected.user.store_type === 3" />
                                    <img src="/imgs/brand.png" alt v-if="selected.user.type == 'brand'" />
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-2 action">
                        <a href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <fa icon="ellipsis-h" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" @click.prevent="showEditModal(selected.id)" v-if="selected.model === 'post' && (selected.loged_user || media.user_role === 1)">Edit</a>
                            <a class="dropdown-item" href="#" @click.prevent="deleteMedia(selected.id)" v-if="selected.loged_user || media.user_role === 1">Delete</a>
                            <a class="dropdown-item" href="javascript:;" @click.prevent="flagMedia(selected.id)" v-if="logged_user_id">Flag Media</a>
                            <a class="dropdown-item" :href="'/media/' + selected.id">Media Page</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="comment_body" >
                <div class="image_description" v-if="selected && selected.description">
                    <div class="userlogo">
                        <a :href="'/'+selected.user.username" v-if="selected.user">
                            <img :src="serverUrl(selected.user.profile_pic ? selected.user.profile_pic.url : default_logo)" alt />
                        </a>
                    </div>
                    <div class="description">
                        <a :href="'/'+selected.user.username">
                            <span class="username">{{selected.user.type == 'user' ? selected.user.username : selected.user.name}}</span>
                        </a>
                        <span>{{selected.description}}</span>
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
                            <a :href="'/'+item.user.username">
                                <img :src="item.user.profile_pic ? serverUrl(item.user.profile_pic.url) : default_logo" alt />
                            </a>
                        </div>

                        <div class="description">
                            <a :href="'/'+item.user.username">
                                <span class="username">{{item.user.type == 'user' ? item.user.username : item.user.name}}</span>
                            </a>
                            <span style="white-space:pre-line" v-html="linkify(item.text)"></span>
                            <p class="status">
                                <span class="likes" v-if="item.likes > 0 && item.likes == 1">{{item.likes}} Like.</span>
                                <span class="likes" v-if="item.likes > 0 && item.likes > 1">{{item.likes}} Likes.</span>
                                <span class="reply" @click="replyto(item.id, item.user)">Reply</span>
                            </p>
                        </div>
                        <div class="actions">
                            <fa icon="heart" fixed-width @click="unlikecomment(item.id, index)" v-if="item.userliked" />
                            <fa :icon="['far', 'heart']" fixed-width @click="likecomment(item.id, index)" v-else />
                            <fa icon="edit" fixed-width v-if="item.owned" @click="editcomment(item.id, index)" />
                            <fa icon="trash-alt" fixed-width v-if="item.owned" @click="deletecomment(item.id)" />
                        </div>
                    </div>
                    <!-- Sub Comment -->
                    <div class="sub_comment_container" v-for="(sub_item, subindex) of item.sub_comment" :key="subindex">
                        <div class="sub_comment">
                            <div class="userlogo">
                                <a :href="'/'+sub_item.user.username">
                                    <img :src="sub_item.user.profile_pic ? serverUrl(sub_item.user.profile_pic.url) : default_logo" alt />
                                </a>
                            </div>
                            <div class="description">
                                <a :href="'/'+sub_item.user.username">
                                    <span class="username">{{sub_item.user.type == 'user' ? sub_item.user.username : sub_item.user.name}}</span>
                                </a>
                                <a :href="'/'+sub_item.parent.user.username">
                                    <span class="text-primary">@{{sub_item.parent.user.type == 'user' ? sub_item.parent.user.username : sub_item.parent.user.name}} </span>
                                </a>
                                <span style="white-space:pre-line" v-html="linkify(sub_item.text)"></span>
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
                                    <a :href="'/'+sub2_item.user.username">
                                        <img :src="sub2_item.user.profile_pic ? serverUrl(sub2_item.user.profile_pic.url) : default_logo" alt />                                        
                                    </a>
                                </div>
                                <div class="description">
                                    <a :href="'/'+sub2_item.user.username">
                                        <span class="username">{{sub2_item.user.type == 'user' ? sub2_item.user.username : sub2_item.user.name}}</span>
                                    </a>
                                    <a :href="'/'+sub2_item.parent.user.username">
                                        <span class="text-primary">@{{sub2_item.parent.user.type == 'user' ? sub2_item.parent.user.username : sub2_item.parent.user.name}} </span>
                                    </a>
                                    <span style="white-space:pre-line" v-html="linkify(sub2_item.text)"></span>
                                    <p class="status">
                                        <span class="likes" v-if="sub2_item.likes > 0 && sub2_item.likes == 1">{{sub2_item.likes}} Like.</span>
                                        <span class="likes" v-if="sub2_item.likes > 0 && sub2_item.likes > 1">{{sub2_item.likes}} Likes.</span>
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
                <div class="menu-panel" v-if="selected && selected.menu && selected.menu.is_active" :class="{'py-2': selected.user.type == 'brand'}">
                    <p class="menu-category">
                        <span>{{selected.menu.category.price_type == 2 ? 'Flower | ' + selected.menu.category.name : selected.menu.category.name}}</span>
                        <span>{{selected.menu.strain ? ' | ' + selected.menu.strain.name : ''}}</span>
                    </p>
                    <p class="item_name">{{selected.menu.item_name}}</p>
                    <p class="price_data mb-0">
                        <span class="text-white pr-2" v-if="selected.menu.price_each != null">{{selected.menu.price_each}}<sup class="text-420">each</sup></span>
                        <span class="text-white pr-2" v-if="selected.menu.price_half_gram != null">{{selected.menu.price_half_gram}}<sup class="text-420">1/2g</sup></span>
                        <span class="text-white pr-2" v-if="selected.menu.price_gram != null">{{selected.menu.price_gram}}<sup class="text-420">g</sup></span>
                        <span class="text-white pr-2" v-if="selected.menu.price_eighth != null">{{selected.menu.price_eighth}}<sup class="text-420">1/8</sup></span>
                        <span class="text-white pr-2" v-if="selected.menu.price_quarter != null">{{selected.menu.price_quarter}}<sup class="text-420">1/4</sup></span>
                        <span class="text-white pr-2" v-if="selected.menu.price_half_oz != null">{{selected.menu.price_half_oz}}<sup class="text-420">1/2</sup></span>
                        <span class="text-white pr-2" v-if="selected.menu.price_oz != null">{{selected.menu.price_oz}}<sup class="text-420">oz</sup></span>
                    </p>
                </div>
                <div class="icon-panel clearfix" v-if="selected">
                    <div class="heart_icon" @click="likepost" v-if="!selected.user_liked">
                        <fa :icon="['far', 'heart']" fixed-width />
                        <p>{{selected.likes}}</p>
                    </div>
                    <div class="heart_icon" @click="unlikepost" v-else>
                        <fa icon="heart" fixed-width />
                        <p>{{selected.likes}}</p>
                    </div>
                    <div class="comment_icon" @click="focuscomment">
                        <fa :icon="['far', 'comment']" fixed-width />
                    </div>
                    <div class="bookmark_icon" @click="addbookmark" v-if="!selected.user_saved">
                        <fa :icon="['far', 'bookmark']" fixed-width />
                    </div>
                    <div class="bookmark_icon" @click="removebookmark" v-else>
                        <fa icon="bookmark" fixed-width />
                    </div>
                    <div class="taged_icon" @click="open_tag_dialog" v-if="hasmediatags">
                      <img src="/imgs/taged.png" alt="">
                    </div>
                </div>
                <div class="comment_area main_comment">
                    <textarea id="main_comment" name="comment" class="comment_text"></textarea>
                    <p class="link_reply mt-2" v-if="status != null">
                        Reply to <a class="reply_to" :href="replyusername">{{replyname}}</a>
                        <span @click="reset" class="reset_reply">
                            <fa icon="times" fixed-width />
                        </span>
                    </p>
                    <button @click="postcomment" v-if="!editComment">Post</button>
                    <button @click="savecomment" v-else>Save</button>
                </div>
            </div>
        </div>
        <vs-popup class="strains__popup media__add" type="border" title="Edit Media" :active.sync="editMedia" v-if="selected">
            <add-form :mainData="selected" :editData="sendata" mode="edit" :from="from"></add-form>
        </vs-popup>

        <vue-bottom-dialog v-model="dialog" v-if="this.selected" >
            <div>
                <media-tags :media="selected" :logged_user_id="logged_user_id"></media-tags>
            </div>
        </vue-bottom-dialog>
    </div>
</template>

<script>
    import EditMedia from "./media/EditMedia";
    import MediaTags from "./media/MediaTags"
    import firebase from "../Firebase";
    import { mapGetters } from 'vuex';
    import AddForm from "./media/AddForm";

    export default {
        name: "FixedComment",
        props: ["media", "allposts"],
        components: {
            EditMedia,
            AddForm,
            MediaTags,
        },
        watch: {
            media: function(newVal, oldVal) {
                this.selected = newVal;
                this.getcomment(this.selected.id);
                this.getIsFollower(this.selected);

                if(newVal.tagged_portal || newVal.tagged_strain || newVal.tagged_users.length > 0) {
                  this.hasmediatags = true;
                } else {
                  this.hasmediatags = false;
                }
                // this.$nextTick(function () {
                //     $("#main_comment").data("emojioneArea").setFocus();
                // });
            }
        },
        computed: mapGetters({
            user: 'auth/user',
        }),
        data() {
            return {
                editMedia: false,
                sendata: {},
                selected: this.media,
                comments: [],
                loading: true,
                status: null,
                replyid: null,
                replyname: "",
                replyusername: null,
                from: 0,

                editComment: false,
                editCommentId: null,
                editCommentIndex: null,
                editSubCommentIndex: null,
                editSub2CommentIndex: null,

                editText: '',
                default_logo: '/imgs/default_sm.png',
                isfollower: 0,
                logged_user_id: 0,
                comment_fixed: false,
                
                dialog: false,
                hasmediatags : false
            };
        },
        methods: {
            open_tag_dialog() {
              this.dialog = true
            },
            showEditModal(id) {
                this.editMedia = true;
                this.sendata = id;
                if (this.selected.portal_id) {
                    this.from = this.selected.portal_id;
                } else {
                    this.from = 0;
                }
            },
            deleteMedia(id) {
                if (!window.confirm('Are you sure?')) {
                    return false;
                }
                let uri = `/media/${id}`;
                let selectedItem = this.allposts;
                this.axios.delete(uri).then(res => {
                    const data = item => item.id === res.data.id;
                    const index = selectedItem.findIndex(data);
                    selectedItem.splice(index, 1);
                    this.selected = selectedItem[0];
                    this.$toast.success("Media has been deleted", "");
                });
            },
            deletecomment(id) {
                if (window.confirm("Are you sure?")) {
                    let uri = "/comment/deletecomment";
                    let params = {
                        comment_id: id,
                        target_id: this.selected.id,
                        target_model: 'media',
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
            getcomment(id) {
                let uri = "/comment/getall";
                let params = {
                    target_id: id,
                    target_model: 'media',
                };
                this.loading = true;
                this.axios.post(uri, params).then(response => {
                    this.comments = response.data;
                    this.loading = false;
                });
            },
            addbookmark() {
                if (this.user) {
                    let uri = "/bookmark/store";
                    let params = {
                        target_id: this.selected.id
                    };
                    this.axios.post(uri, params).then(response => {
                        this.selected.user_saved = response.data;
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            removebookmark() {
                let uri = "/bookmark/delete";
                let params = {
                    target_id: this.selected.id
                };
                this.axios.post(uri, params).then(response => {
                    this.selected.user_saved = response.data;
                });
            },
            focuscomment() {
                if (this.user) {
                    $(".main_comment .emojionearea-editor").focus();
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            postcomment() {
                let comment = $("#main_comment ").data('emojioneArea').getText();
                if (this.user) {
                    if (comment == "") {
                        return true;
                    } else {
                        const params = {
                            text: comment,
                            target_id: this.selected.id,
                            target_model: "media",
                            reply: this.replyid
                        };

                        let uri = "/comment/add";

                        this.axios.post(uri, params).then(response => {
                            this.comments = response.data;
                            if(this.user.id != this.selected.user_id) {                                
                                let noti_fb = firebase.database().ref('notifications/' + this.selected.user.id).push();
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
                        });
                    }
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            likepost() {
                if (this.user) {
                    const params = {
                        target_id: this.selected.id,
                        target_model: "post"
                    };
                    let uri = "/like/addlike";
                    this.axios.post(uri, params).then(response => {
                        this.selected.likes = response.data;
                        this.selected.user_liked = true;
                        if(this.user.id != this.selected.user_id) {                           
                            let noti_fb = firebase.database().ref('notifications/' + this.selected.user_id).push();
                            noti_fb.set({
                                notifier_id: this.user.id,
                                type: 'like',
                            }); 
                        }
                    });
                } else {
                    $("#loginmodal").modal("show");
                }
            },
            unlikepost() {
                if (this.user) {
                    const params = {
                        target_id: this.selected.id,
                        target_model: "post"
                    };
                    let uri = "/like/unlike";
                    this.axios.post(uri, params).then(response => {
                        this.selected.likes = response.data;
                        this.selected.user_liked = false;
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
                if (this.user) {
                    let uri = "/profile/getisfollower";
                    let params = {
                        user_id: this.user.id,
                        follower_user_id: post.user_id
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
                if (this.user) {
                    let uri = '/user/follow';
                    let params = {
                        user_id: this.user.id,
                        follower_id: this.selected.user_id
                    };
                    this.axios.post(uri, params).then(response => {
                        this.isfollower = 1;
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },
            unfollow() {
                if (this.user) {
                    let uri = '/user/unfollow';
                    let params = {
                        user_id: this.user.id,
                        follower_id: this.selected.user_id
                    };
                    this.axios.post(uri, params).then(response => {
                        this.isfollower = 0;
                    });
                } else {
                    $("#loginmodal").modal('show');
                }
            },
            flagMedia(id) {
                if(this.user) {
                    if(this.selected.is_flag) {return false};
                    let url = '/flag';
                    let params = {
                        flaggable_id : id,
                        flaggable_type : "App\\Media",
                    }
                    this.axios.post(url, params).then(response => {
                        if(response.data.status == 'success'){
                            toastr['success']('Flagged Media');
                            this.selected.is_flag = true;
                        }
                    });
                } else {
                    $("#loginmodal").modal();
                }
                
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
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + 'imgs/default.png';
                }
            }
        },
        mounted() {
            if(this.media) {
                if(this.media.tagged_portal || this.media.tagged_strain || this.media.tagged_users.length > 0) {
                    this.hasmediatags = true;
                } else {
                    this.hasmediatags = false;
                }  
            }
            if (this.user) {
                this.logged_user_id = this.user.id
            }
            if (this.selected) {
                this.getcomment(this.selected.id);
            }

            var self = this;
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
        },
    };
</script>

<style lang="scss" scoped>    
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
        color: #EFA720;
        left: 13px;
        .reset_reply svg {
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
        // margin-top: -70px;
        border-top: solid 1px #FFF;
        .menu-category {
            color: gray;
            margin-bottom: 0;
            font-size: 12.5px;
        }
        .item_name {
            color: #EFA720;
            margin-bottom: 0;
            font-size: 18px;
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
        margin-left: -10px;
        margin-right: -10px;
        padding-top: 3px;
        border-top: solid 1px grey;
    }
</style>

<style lang="scss">
    #fixedcommentbox {
        .emojionearea {
            height: 100% !important;
            padding-right: 70px !important;
            background-color: black !important;
            .emojionearea-editor {
                white-space: normal !important;
                height: 100% !important;
                min-height: unset !important;
                width: 100%;
                color: white !important;
            }
            .emojionearea-button {
                top: 10px;
                div {
                    background-image: url(https://i.imgur.com/xljqgrH.png) !important;
                }
            }
        }
    }

    .postmedia .right_side {
      z-index: 100;
    }

    .vue-bottom-dialog {
      z-index: 100;

      * {
        z-index: 100;
      }
      
      .vue-bottom-dialog-ground {
        background-color: unset !important;
      }

      .vue-bottom-dialog-wrapper {
        background: black !important;
        color: white;
        padding: 0 30px;
        border-top: 1px solid white;

        .vue-bottom-dialog-wrapper-touch-indent {
          display: none;
        }

        @media (min-width: 600px) {
          left: 50%;
          transform: translate(-50%, 0);
          max-width: 600px;
          padding: 0 50px;
        }
      }

      &.vue-bottom-dialog-overlay {
        .vue-bottom-dialog-wrapper {
          height: auto !important;
        }
      }
    }

    .taged_icon {
      text-align: center;
      float: right;
      z-index: 1;
      background-color: black;
      margin-right: 10px;

      img {
        width: 25px;
        cursor: pointer;
      }
    }
</style>
