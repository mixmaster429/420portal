<template>
    <div class="chatfooter">
        <div class="progress progress-sm mb-0" v-show="uploading">
            <div class="progress-bar progress-bar-success" role="progressbar" :aria-valuenow="uploadProgress" aria-valuemin="0" aria-valuemax="100" :style="{width: uploadProgress + '%'}">
                <span class="sr-only">{{uploadProgress}}% Complete</span>
            </div>
        </div>
        <textarea :id="'d_chatbox'+this.sender + '-' + this.receiver"></textarea>
        <file-upload
            ref="upload"
            :input-id="'uploader_' + sender + '-' + receiver"
            v-model="files"
            :post-action="this.action + this.sender + '/' + this.receiver"
            @input-file="inputFile"
            @input-filter="inputFilter"
        >
            <fa icon="paperclip"></fa>
        </file-upload>
    </div>
</template>

<script>
    import firebase from '../Firebase';
    import _ from "lodash";
    export default {
        name: "ChatForm",
        props: ['receiver', 'chat_type', 'sender'],
        data: function () {
            return {
                receiver_id: this.receiver,
                files: [],
                uploadProgress: 5,
                uploading: false,
                is_typing : false,
                tTimeout : null,
                action: process.env.serverUrl + '/api/usermessages/imageupload/',
            }
        },
        watch: {
            is_typing: function (new_val, old_val) {
                var typingFs = firebase.database().ref('chatrooms/' + this.sender + '/is_typing').child(this.receiver);
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
                // this.debouncedTyping()
            }
        },
        methods: {
            endTyping(){
                var typingFs = firebase.database().ref('chatrooms/' + this.sender + '/is_typing').child(this.receiver);
                typingFs.set({
                    is_typing: true,
                });
            },
            sendmessage() {
                let message = $("#d_chatbox"+this.sender+'-'+this.receiver_id).data('emojioneArea').getText();
                if (message == '') {
                    return false;
                } else {
                    this.$emit('messagesent', {
                        message: message,
                        file: false
                    });
                    // $("#d_chatbox"+this.sender+'-'+this.receiver_id).val('');
                    // $(".chatfooter .emojionearea-editor").text('');
                    $("#d_chatbox"+this.sender+'-'+this.receiver_id).data('emojioneArea').setText('');
                }
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
            }
        },
        created(){
            this.debouncedTyping = _.debounce(this.endTyping, 500)
        },
        mounted() {
            let _this = this;
            firebase.database().ref('chatrooms/' + this.sender + '/is_typing').child(this.receiver).set({is_typing : false});
            $("#d_chatbox"+this.sender+"-"+this.receiver).emojioneArea({
                pickerPosition: "top",
                search: false,
                autocomplete: false,
                placeholder: "Write a message...",
                events: {
                    keypress: function (editor, event) {
                        // _this.is_typing = true;
                        // if (typingTimeout != undefined) clearTimeout(typingTimeout);
                        // var typingTimeout = setTimeout(function(){
                        //     _this.is_typing = false;
                        // }, 2500);
                        // let message = $("#d_chatbox"+_this.sender+"-"+_this.receiver).data('emojioneArea').getText();
                        // console.log(message);
                        // if (message == '') {
                        //     _this.is_typing = false;
                        // } else {
                        //     _this.is_typing = true;
                        // }
                    },
                }
            });

            $(".chatfooter .emojionearea-editor").text('').keydown(function (event) {
                let message = $("#d_chatbox" + _this.sender + "-" + _this.receiver).data('emojioneArea').getText();
                if(message.length > 1) {
                    _this.is_typing = true;
                } else {
                    _this.is_typing = false;
                }
                if (event.keyCode == 13 && !event.shiftKey) {
                    _this.sendmessage();
                    _this.is_typing = false;
                    return false;
                }
            });
        },
    }
</script>

<style lang="scss">

    .file-uploads {
        display: none;
    }

    .chatfooter {
        height: 50px;
        width: 268px;
        position: absolute;
        bottom: 0;

        .file-uploads {
            position: absolute;
            right: 6px;
            bottom: -4px;
            font-size: 20px;
            color: #EFA720;

            label{
                cursor: pointer;
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

        i{
            cursor: pointer;
        }
    }
</style>
