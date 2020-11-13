<template>
    <div class="mini-comment">
        <p class="comment-count" @click="openCommentModal()">View all <span id="count_comments">{{comment_count}}</span> comments</p>
        <div class="v_comment_box">
            <div class="profile-img">
                <img class="userlogo" :src="serverUrl(auth_user.profile_pic.url)" v-if="auth_user != null && auth_user.profile_pic != null" alt />
                <img class="userlogo" v-else src="/imgs/default_sm.png" />
            </div>
            <div class="commentarea">
                <textarea 
                    rows="1" 
                    class="comment_text"                                    
                    v-model="comment_text"
                    placeholder="Write a comment..."
                    @focus="hideFooter()"
                    @blur="showFooter()"
                ></textarea>
            </div>
            <div class="button-group">
                <button class="btn" @click="postComment()">Post</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
    name : 'MiniComment',
    props: ["page", "model", "comments_count"],
    data() {
        return {
            comment_text: '',
            comment_count: this.comments_count,
            target_id: this.page.id,
            target_model : this.model,
        };
    },
    computed: mapGetters({        
        auth_user: 'auth/user',
    }),
    mounted() {
        // $("#mini_comment").emojioneArea({
        //     pickerPosition: "top",
        //     autocomplete: false,
        //     search: false,
        //     placeholder : 'Write a comment...',
        // });
    },
    methods : {
        postComment() {
            // var text = $(".commentarea .comment_text").data('emojioneArea').getText();
            if (this.auth_user) {
				if (this.comment_text == "") {
					return false;
				} else {                    
					const params = {
						text: this.comment_text,
						target_id: this.target_id,
						target_model: this.target_model,
						reply: null,
                    };

					let uri = "/comment/add";

					this.axios.post(uri, params).then(response => {
                        this.comment_text = '';
                        // $(".commentarea .comment_text").data('emojioneArea').setText('');
                        this.comment_count++;
					});
				}
			} else {
				$("#loginmodal").modal("show");
			}
        },
        openCommentModal(){
            $("#commentModal").modal();
        },        
        hideFooter(){
            $("#app").addClass('focus_comment');
        },
        showFooter(){
            $("#app").removeClass('focus_comment');
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
        
    }
}
</script>

<style lang="scss" scoped>
    .comment-count{
        margin: 5px 0;
        color: white;
        span {
            color: #3490dc !important;
        }
    }
    .v_comment_box {
        display: flex;
        border: solid 1px white;
        padding: 5px;
        position: relative;
        .profile-img {
            .userlogo {
                width: 30px;
                height: 30px;
                border-radius: 100px;
                object-fit: cover;
            }
        }
        .commentarea {
            width: 100%;
            height: 31px;
            textarea {
                border: none;
                width: 100%;
                height: 36px;
                font-size: 16px;
                padding: 5px !important;
                resize: none;
                border: none !important;
            }
        }
        .button-group {
            // position: absolute;
            // right: 0;
            button {
                background-color: #EFA720;
                background-color: #EFA720;
                border-radius: 0;
                padding: 3px 5px;
            }
        }
    }
</style>
<style lang="scss">
    .mini-comment { 
        .emojionearea {
            border: none;
            width: 100%;
            height: 31px;
            padding: 5px;
            padding-right: 0px !important;
            max-width: calc(100% - 28px);
            .emojionearea-editor {
                padding: 0 80px 0 5px;
            }
        }   
    }
</style>