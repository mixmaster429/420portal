<template>
    <div class="right_comment_profile" id="page_comment" v-if="selected">
        <div class="comments profile-comments">
            <div class="comment_body">
                <div class="d-flex mt-3 justify-content-center" v-if="loading">
                    <div class="spinner-border text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>

                <div class="comment_container" :key="index" v-for="(item, index) in comments" v-if="!loading">
                    <div class="comment">
                        <div class="userlogo">
                            <a :href="item.user.username"><img :src="serverUrl(item.user.profile_pic ? item.user.profile_pic.url : '/imgs/default_sm.png')" alt /></a>
                        </div>

                        <div class="description">
                            <a :href="item.user.username">
                                <span class="username">{{item.user.type == 'user' ? item.user.username : item.user.name}}</span>
                            </a>
                            <span>{{item.text}}</span>
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
                                    <img :src="serverUrl(sub_item.user.profile_pic ? sub_item.user.profile_pic.url : '/imgs/default_sm.png')" alt />
                                </a>
                            </div>
                            <div class="description">
                                <a :href="sub_item.user.username">
                                    <span class="username">{{sub_item.user.type == 'user' ? sub_item.user.username : sub_item.user.name}}</span>
                                </a>
                                <a :href="sub_item.parent.user.username">
                                    <span class="text-primary">@{{sub_item.parent.user.type == 'user' ? sub_item.parent.user.username : sub_item.parent.user.name}} </span>
                                </a>
                                <span>{{sub_item.text}}</span>
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
                                        <img :src="serverUrl(sub2_item.user.profile_pic ? sub2_item.user.profile_pic.url : '/imgs/default_sm.png')" alt />
                                    </a>
                                </div>
                                <div class="description">
                                    <a :href="sub2_item.user.username">
                                        <span class="username">{{sub2_item.user.type == 'user' ? sub2_item.user.username : sub2_item.user.name}}</span>
                                    </a>
                                    <a :href="sub2_item.parent.user.username">
                                        <span class="text-primary">@{{sub2_item.parent.user.name}} </span>
                                    </a>
                                    <span>{{sub2_item.text}}</span>
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
                <div class="comment_area main_comment" id="page_main_comment">
                    <textarea id="main_comment_profile" name="comment" class="comment_text"></textarea>
                    <p class v-if="status != null">
                        Reply to <a class="reply_to" :href="replyname">{{replyname}}</a>
                        <span @click="reset" class="ml-1">
                            <fa icon="times" fixed-width></fa>
                        </span>
                    </p>
                    <button @click="postcomment" v-if="!editComment" id="btn_post">Post</button>
                    <button @click="savecomment" v-else id="btn_edit">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
	name: "PageComment",
	props: ["page", 'model'],
	data() {
		return {
			editMedia: false,
			sendata: {},
			selected: this.page,
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
            default_logo: '/imgs/default.png',
		};
	},
    mounted() {
        var self = this;
        $("#main_comment_profile").emojioneArea({
            pickerPosition: "top",
            search: false,
            autocomplete: false,
            placeholder: "Write a comment...",
            events: {
                // blur: function (editor, event) {
                //     if(window.is_mobile){
                //         $(".right_comment_profile").css('height', "calc(100vh - 135px)");
                //         $("#footer_bar").show();
                //     }
                // },
                // focus: function (editor, event) {
                //     if(window.is_mobile){
                //         $("#footer_bar").show();
                //         $(".right_comment_profile").css('height', "calc(100vh - 50px)");
                //     }
                // },
                blur: function (editor, event) {
                    if(event.relatedTarget && event.relatedTarget.id == 'btn_edit') {
                        self.savecomment();
                    }
                    if(event.relatedTarget && event.relatedTarget.id == 'btn_post') {

                    } else {
                        if(self.$device.isMobile){
                            $("#app").removeClass('focus_comment');
                        }
                    }
                },
                focus: function (editor, event) {
                    if(self.$device.isMobile){
                        $("#app").addClass('focus_comment');
                    }
                },
                keypress: function(editor, event) {
                    if(event.keyCode === 13) {
                        if(self.editComment) {
                            self.savecomment();
                        } else {
                            self.postcomment();
                        }
                    }
                }
            }
        });
		this.getComments();
	},
    computed: mapGetters({
        user: 'auth/user',        
    }),
	methods: {
		showEditModal(id) {
			this.editMedia = true;
			this.sendata = id;
		},
		deletecomment(id) {
			if (window.confirm("Are you sure?")) {
				let uri = "/comment/deletecomment";
				let params = {
					comment_id: id,
                    target_id: this.selected.id,
                    target_model: this.model,
				};
				this.axios.post(uri, params).then(response => {
                    this.comments = response.data;
                    this.getCountOfComments();
				});
			}
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
            $("#main_comment_profile").data("emojioneArea").setText('');
		},
		likecomment(id, index, type) {
			if (this.user) {
				let uri = "/comment/addlike";
				let params = {
					target_id: id
				};
				this.axios.post(uri, params).then(response => {
					if (type == "parent") {
						this.comments[index].userliked = response.data.userliked;
						this.comments[index].likes = response.data.likes;
					} else {
						this.comments[type].sub_comment[index].userliked =
							response.data.userliked;
						this.comments[type].sub_comment[index].likes = response.data.likes;
					}
				});
			} else {
				$("#loginmodal").modal("show");
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
                    // if(this.user != action_comment.user_id) {                            
                    //     let noti_fb = firebase.database().ref('notifications/' + action_comment.user_id).push();
                    //     noti_fb.set({
                    //         notifier_id: this.user,
                    //         type: 'like_comment',
                    //     });
                    // }
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
            $("#main_comment_profile").data("emojioneArea").editor.focus();
            $("#main_comment_profile").data("emojioneArea").setText("@" + name + ' ');
            this.setCursor();
		},
		getComments() {
			let uri = "/comment/getall";
			let params = {
				target_id: this.selected.id,
                target_model: this.model,
			};
			this.loading = true;
			this.axios.post(uri, params).then(response => {
                this.comments = response.data;
                this.getCountOfComments();
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
			if (this.user) {
				if ($(".profile-comments .emojionearea-editor").html() == "") {
					return true;
				} else {
					const params = {
						text: $("#main_comment_profile").data('emojioneArea').getText(),
						target_id: this.selected.id,
						target_model: this.model,
						reply: this.replyid
                    };

					let uri = "/comment/add";

					this.axios.post(uri, params).then(response => {
                        this.comments = response.data;
                        this.getCountOfComments();
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
            $("#main_comment_profile").data("emojioneArea").setText(comment.text);
        },
        savecomment() {
            let id = this.editCommentId;
            let index = this.editCommentIndex;
            let subindex = this.editSubCommentIndex;
            let sub2index = this.editSub2CommentIndex;
            let text = $("#main_comment_profile").data("emojioneArea").getText();
            const params = {
                id: id,
                text: text,
            };
            console.log(params);
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
		likepost() {
			if (this.user) {
				const params = {
					target_id: this.selected.id,
					target_model: this.model
				};
				let uri = "/like/addlike";
				this.axios.post(uri, params).then(response => {
					this.selected.likes = response.data;
					this.selected.user_liked = true;
				});
			} else {
				$("#loginmodal").modal("show");
			}
		},
		unlikepost() {
			if (this.user) {
				const params = {
					target_id: this.selected.id,
					target_model: this.model
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
            // var tag = document.getElementsByClassName('emojionearea-editor')[1];
            var tag = document.querySelector('#page_main_comment > .comment_text > .emojionearea-editor');
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
        getCountOfComments(){
            let data = {
                target_id : this.selected.id,
                target_model : this.model,
            };
            this.axios.post('/comment/count_comments', data)
                .then(response => {
                    $("#count_comments").text(response.data);
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
	}
};
</script>

<style lang="scss" scoped>
    .right_comment_profile {
        max-width: 500px;
        height: calc(100vh - 380px);
        @media only screen and (max-width:600px) {
            height: calc(100vh - 110px);
        }
        .comments {
            // border: 1px solid white;
            // border-radius: 10px;
            // margin-top: 10px;
            // height: 80%;
            font-size: 0.9rem;
            @media only screen and (max-width:600px) {
                font-size: 16px;
            }            
            font-weight: 600;
            height: 100%;
            background-color: black;
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
                                color: #efa720;
                                cursor: pointer;
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
                        color: #efa720;
                        font-size: 20px;
                    }
                    .dropdown-menu {
                        background-color: black;
                        border: 1px solid grey;
                        a {
                            color: #efa720;
                        }
                    }
                }
            }
            .footer {
                // border-top: 1px solid grey;
                // padding: 2px 10px;
                i {
                    font-size: 22px;
                    color: #efa720;
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
                    // margin-top: 50px;
                    position: relative;
                    height: 50px;
                    .emojionearea, .emojionearea.form-control {
                        border: none !important;
                        border-top: 1px solid #fff !important;
                        border-radius: 0 !important;
                        .emojionearea-button {
                            top: 30% !important;
                        }
                    }
                    .comment_text {
                        background-color: transparent;
                        color: white;
                    }
                    button {
                        font-size: 16px;
                        background-color: #efa720;
                        padding: 2px 6px;
                        border: none;
                        position: absolute;
                        top: 22% !important;
                        right: 30px;
                    }
                    
                }
            }
        }
    }
    .comment_body {
        position: relative;
        height: calc(100% - 70px);
        @media only screen and (max-width:600px) {
            height: calc(100% - 50px);
            padding-bottom: 0;
        }
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
    .reply {
        cursor: pointer;
    }
</style>
<style lang="scss">
    #page_comment .emojionearea .emojionearea-editor {
        padding-left: 5px !important;
        white-space: normal !important;
    }
    #page_main_comment .emojionearea .emojionearea-button {
        top: 10px;
    }
    
    #app.focus_comment .right_comment_profile {
        @media only screen and (max-width:600px) {
            height: calc(100vh - 106px);
        }
    }

    #page_comment {
        .emojionearea-button {
            top: 10px;
            div {
                background-image: url(https://i.imgur.com/xljqgrH.png) !important;
            }
        } 
    }
</style>
