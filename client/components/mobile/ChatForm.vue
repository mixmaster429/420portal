<template>
    <div class="mobilechatfooter" @blur="lostfocus()">
        <div class="progress progress-sm mb-0" v-show="uploading">
            <div class="progress-bar progress-bar-success" role="progressbar" :aria-valuenow="uploadProgress" aria-valuemin="0" aria-valuemax="100" :style="{width: uploadProgress + '%'}">
                <span class="sr-only">{{uploadProgress}}% Complete</span>
            </div>
        </div>
        <textarea :id="'d_chatbox'+this.receiver"></textarea>
        <file-upload
            ref="upload"
            v-model="files"
            :post-action="this.action + this.sender + '/' + this.receiver"
            @input-file="inputFile"
            @input-filter="inputFilter"
        >
            <fa icon="paperclip" fixed-width></fa>
        </file-upload>
        <a href="javascript:;" id="btn-send" @click="sendmessage()"><fa :icon="['fab', 'telegram-plane']"></fa></a>
    </div>
</template>

<script>
    import firebase from '../../Firebase';
    import { mapGetters } from "vuex";
    export default {
        name: "mobilechatform",
        props: ['receiver'],
        data: function () {
            return {
                sender: null,
                files: [],
                uploadProgress: 5,
                uploading: false,
                is_typing : false,
                tTimeout : null,
                action: process.env.serverUrl + '/api/usermessages/imageupload/',
            }
        },
        computed: mapGetters({
            auth_user: 'auth/user',
        }),
        watch: {
            is_typing: function (new_val, old_val) {
                let typingFs = firebase.database().ref('chatrooms/' + this.sender + '/is_typing').child(this.receiver);
                if(new_val) {
                    typingFs.set({
                        is_typing: true,
                    });
                } else {
                    // if (this.tTimeout) clearTimeout(this.tTimeout);
                    // this.tTimeout = setTimeout(function(){
                        typingFs.update({
                            is_typing: false,
                        });
                    // }, 2000);
                }
            }
        },
        methods: {
            sendmessage() {
                let message = $("#d_chatbox"+this.receiver).val();
                if (message == '') {
                    return false;
                } else {
                    this.$emit('messagesent', {
                        message: message,
                        file: false
                    });
                    $("#d_chatbox").val('');
                    $(".mobilechatfooter .emojionearea-editor").text('');
                }
                $("#d_chatbox" + this.receiver).data('emojioneArea').setFocus();
                this.is_typing = false;
            },

            inputFile(newFile, oldFile){
                let _this = this;
                this.$refs.upload.active = true;
                if (newFile && oldFile) {
                    if (newFile.active !== oldFile.active) {
                        this.uploading = true
                    }
                    if (newFile.progress !== oldFile.progress) {
                        // console.log('progress', newFile.progress)
                        this.uploadProgress = newFile.progress
                    }
                    // Uploaded error
                    if (newFile.error !== oldFile.error) {
                        alert('Sorry, upload is failed. Please try again');
                    }
                    // Uploaded successfully
                    if (newFile.success !== oldFile.success) {
                        setTimeout(function(){
                            // $(".progress").hide()
                            _this.uploading = false;
                            _this.$emit('messagesent', {
                                message: newFile.response.url,
                                file: true,
                                created_at: newFile.response.created_at
                            });
                        }, 1000);
                    }
                }
            },

            inputFilter: function (newFile, oldFile, prevent) {
                if (newFile && !oldFile) {
                    // Filter non-image file
                    if (!/\.(jpeg|jpe|jpg|gif|png|webp)$/i.test(newFile.name)) {
                        return prevent()
                    }
                }
            },
            lostfocus(){
                console.log('adsfasd');
            }
        },
        mounted() {
            this.sender = this.auth_user.id;
            let _this = this;
            firebase.database().ref('chatrooms/' + this.sender + '/is_typing').child(this.receiver).set({is_typing : false});

            $("#d_chatbox"+_this.receiver).emojioneArea({
                pickerPosition: "top",
                search: false,
                autocomplete: false,
                placeholder: "Write a message...",
                attributes: {
                    spellcheck : true,
                    autocomplete   : "on",
                },
                events: {
                    keypress: function (editor, event) {
                        // _this.is_typing = true;
                        // if (typingTimeout != undefined) clearTimeout(typingTimeout);
                        // var typingTimeout = setTimeout(function(){
                        //     _this.is_typing = false;
                        // }, 2500);
                    },
                    blur: function (editor, event) {
                        if(event.relatedTarget && event.relatedTarget.id == 'btn-send') {

                        } else {
                            $("#app").removeClass('focus_comment');
                            _this.$parent.scrollToBottom();
                        }
                    },
                    focus: function (editor, event) {
                        $("#app").addClass('focus_comment');
                        _this.$parent.scrollToBottom();
                    }
                }
            });

            $(".mobilechatfooter .emojionearea-editor").text('').keyup(function (event) {
                let message = $("#d_chatbox"+_this.receiver).data('emojioneArea').getText();
                if(message.length > 1) {
                    _this.is_typing = true;
                } else {
                    _this.is_typing = false;
                }
                // if (event.keyCode == 13) {
                //     if (event.shiftKey) {
                //     } else {
                //         $(".mobilechatfooter .emojionearea-editor").blur().focus();
                //         _this.sendmessage();
                //     }
                // }
            });

            // window.addEventListener("hashchange", function(e) {
            //     // if(e.oldURL.length > e.newURL.length)
            //         alert("back")
            // });
        },
    }
</script>

<style lang="scss">
    .mobilechatfooter {
        height: 50px;
        width: 100%;
        position: relative;
        // bottom: 45px;

        .emojionearea {
            padding-left: 30px !important;
            padding-right: 30px !important;

            .emojionearea-editor {
                padding: 6px 30px 6px 12px;
                white-space: normal !important;
            }

            .emojionearea-button {
                left: 3px !important;
                div {
                    background-image: url(https://i.imgur.com/xljqgrH.png) !important;
                    background-position: 0 -30px;
                    background-size: cover;
                    width: 28px;
                    height: 28px;
                }
            }
        }

        .file-uploads {
            position: absolute;
            right: 6px;
            top: 4px;
            font-size: 20px;
            color: #EFA720;

            label{
                cursor: pointer;
            }
            svg {
                font-size: 24px;
            }
        }

        .progress{
            position: absolute;
            width: 100%;
            height: 3px;
            top: -1px;
            z-index: 1;

            .progress-bar{
                background-color: red;
            }
        }
        #btn-send {
            position: absolute;
            right: 35px;
            top: -3px;
            font-size: 28px;
            color: #EFA720;
        }
    }
</style>
