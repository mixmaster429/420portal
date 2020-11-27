<template>
    <form @submit.prevent="updateProfile" id="form_user_edit">
        <div class="form-group text-center">
            <h4 class="my-2">Edit Profile</h4>
            <div class="profileImg d-flex flex-column align-items-center">
                <label class="mt-2 upload__label" for="profileFile">
                    <div class="logo" for="profileFile" v-if="profileMediaType !== 'video'">
                        <img v-if="profileMediaType === 'image'" class="signup_logo" v-bind:src="profileMediaData" v-show="profileMediaData" />
                    </div>
                </label>
                <label class="btn btn-sm btn-primary mt-1 btn-select" for="profileFile">Select</label>
                <input type="file" hidden id="profileFile" ref="profileFile" name="profileFile" class="btn-file" @change="previewProfileImage" accept="image/*" />
                
                <a @click="logout($event)" href="javascript:;" class="btn-logout" v-if="mode == 'edit'">
                    <img src="/imgs/logout.png" alt />
                </a>                
                <img :src="is_active ? '/imgs/active.png' : '/imgs/inactive.png'" alt=" " class="btn-power" v-if="mode == 'edit'" @click="openActivatePopup = true">
            </div>
        </div>

        <div class="floating-label" @click="handleScroll">
            <input type="text" class="floating-input" name="username" required v-model="username" maxlength="25" placeholder="Username" />
            <label for="name" :class="{focused : username}">Username *</label>
        </div>

        <div class="floating-label" @click="handleScroll">
            <input type="text" class="floating-input" name="email" v-model="email" placeholder="Email" required />
            <label for="name" :class="{focused : email}">Email *</label>
        </div>

        <div class="floating-label" style="height: 50px;" @click="handleScroll">
            <textarea v-model="description" class="form-control media-description floating-textarea" rows="2" placeholder=" " id="user_signature" ref="signature"></textarea>
            <label for="user_signature" :class="{focused : description}">Bio</label>
        </div>

        <div class="custom-check d-flex mt-2">
            <div class="round mr-1">
                <input type="checkbox" name="is_private" id="is_private" v-model="is_private" />
                <label for="is_private"></label>
            </div>
            <label class="ml-2 text-white" for="is_private">Private Account</label>
        </div>

        <input type="hidden" name="model" :value="model = 'post'" />

        <button class="btn btn-primary mt-2 d-block px-3 mx-auto" style="font-size: 1.01rem">
            <span v-if="mode === 'add'">Post</span>
            <span v-else-if="mode === 'edit'">Post</span>
            <span v-if="loading" class="spinner-border spinner-border-sm"></span>
        </button>
        <vs-popup class="active_popup" type="border" title :active.sync="openActivatePopup" v-if="mode == 'edit'">
            <div class="text-center py-3">                
                <button class="btn btn-primary mr-3" @click="activeUser">{{is_active ? 'Deactive' : 'Activate'}}</button>
                <button class="btn btn-420" @click="deleteUser">Delete</button>
            </div>
        </vs-popup>
    </form>
</template>

<script>
    import Multiselect from "vue-multiselect";
    export default {
        name: "ProfileForm",
        components: {
            Multiselect
        },
        // watch: {
        //   editData: function(newVal, oldVal) {
        //     this.editData = newVal;
        //     // this.getcomment(this.selected.id);
        //   }
        // },
        props: {
            mode: {
                default: "add"
            },
            editData: 0,
            mainData: {}
        },
        data() {
            return {
                media: "",
                profileMediaType: "",
                profileMediaData: "",
                id: '',
                username: "",
                email: "",
                description: "",
                model: "",
                success: null,
                loading: false,
                is_active: false,
                is_private: false,
                openActivatePopup: false,
            };
        },
        mounted() {
            var _this = this;
            this.mode === "edit" && this.fetchData(this.editData);
            this.$root.$on("edit-profile", () => {
                this.updateProfile();
            });

            $("#user_signature").emojioneArea({
                pickerPosition: "top",
                search: false,
                autocomplete: false,
                placeholder: " ",
                events: {
                    ready: function() {
                        let description = _this.mainData.description ? _this.mainData.description : ' ';
                        this.setText(description);
                    },
                    blur: function (editor, event) {
                        _this.description = this.getText();
                        if(_this.description == '' || _this.description == ' ') {
                            $("#user_signature").siblings('label').removeClass('focused');
                        }
                        if(_this.$device.isMobile) {
                            _this.showFooter();
                        }
                    },
                    focus: function (editor, event) {
                        $("#user_signature").siblings('label').addClass('focused');
                        if(_this.$device.isMobile) {
                            _this.hideFooter();
                        }
                    }
                }
            });
        },
        created() {},
        watch: {
            editData: function(newVal, oldVal) {
                this.selected = newVal;
                this.fetchData(newVal);
            },
            // $route: 'fetchData(editData)'
            $route: "getallposts",
        },
        methods: {
            fetchData(id) {
                this.id = this.mainData.id;
                this.username = this.mainData.username;
                this.email = this.mainData.email;
                this.is_active = this.mainData.is_active;
                this.is_private = this.mainData.is_private;
                this.description = this.mainData.description;
                if (!_.isEmpty(this.mainData.profile_pic)) {
                    this.profileMediaType = this.mainData.profile_pic.type;
                    this.profileMediaData = this.serverUrl(this.mainData.profile_pic.url);
                } else {
                    this.profileMediaData = "/imgs/default.png";
                    this.profileMediaType = "image";
                }
            },
            type() {
                if (this.mode === "edit") {
                    console.log("edit");
                    this.updateProfile();
                    return;
                }
                // console.log("add");
                this.save();
                return;
            },
            updateProfile() {
                const url = `/profile/update`;

                let description = $("#user_signature").data("emojioneArea").getText();

                const params = {
                    id: this.mainData.id,
                    name: this.username,
                    username: this.username,
                    email: this.email,
                    image: this.media,
                    description: description,
                    is_private: this.is_private ? 1 : 0,
                };

                const formData = new FormData();
                for (let key in params) {
                    formData.append(key, params[key]);
                }

                const headers = {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                };
                this.axios.post(url, formData, headers).then(res => {
                    this.$toast.success("Edited Successfully.", "");
                    // this.$router.go(-1);
                    window.location.href = "/" + this.username;
                })
                .catch(e => this.$toast.error("Error editing media", ""));
            },
            limitText(count) {
                return `and ${count} other companies`;
            },
            previewProfileImage(event) {
                this.profileMediaData = "";
                this.profileMediaType = "";
                let input = event.target;

                if (input.files[0].type.match("image/*")) {
                    this.profileMediaType = "image";
                } else if (input.files[0].type.match("video/*")) {
                    this.profileMediaType = "video";
                }

                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    this.media = input.files[0];
                    reader.onload = e => {
                        this.profileMediaData = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            },
            handleScroll(e) {
                const element = e.target;
                const offset = 145;
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = element.getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offset;

                // device detection
                if (
                    /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(
                        navigator.userAgent
                    ) ||
                    /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(
                        navigator.userAgent.substr(0, 4)
                    )
                ) {
                    setInterval(
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: "smooth"
                        }),
                        1000
                    );
                }

                // window.scrollTo({
                //   top: offsetPosition,
                //   behavior: "smooth"
                // });
            },            
            activeUser(){
                let params = { id: this.mainData.id };
                let uri = `/user/activate`;
                this.axios.post(uri, params)
                    .then(response => {
                        this.is_active = response.data
                        this.openActivatePopup = false;
                    });
            },
            deleteUser(){
                if(!window.confirm('Are you sure?')) {
                    return false;
                }
                let params = { id: this.mainData.id };
                let uri = `/user/${this.mainData.id}`;
                this.axios.delete(uri, params).then(response => {
                        window.location.href = '/';
                    });
            },
            logout(event) {
                event.preventDefault();
                if(window.confirm('Are you sure?')) {
                    this.$store.dispatch('auth/logout');
                    window.location.href = "/";
                }                
            },
            serverUrl(item) {
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + '/imgs/default.png';
                }
            },
            showFooter() {
                $("#app").removeClass('focus_comment');
            },
            hideFooter() {
                $("#app").addClass('focus_comment');
            }
        }
    };
</script>

<style lang="scss" scoped> 
    // .vs-popup {
    //   height: 80% !important;
    // }
    // button {
    //     font-size: 20px;
    // }

    .btn-select {
        color: #000 !important;
    }

    input::placeholder {
        color: transparent;
    }

    .main-logo {
        height: 55px;
        width: auto;
    }

    // Floating
    textarea {
        background: #000;
        width: 100%;
        padding: 0.3rem 0;
        border: none;
        border-bottom: 1px solid white;
        &::placeholder {
            color: white;
            opacity: 1;
        }
    }

    .logo {
        height: 160px;
        width: 160px;
        background: url(/imgs/strains/template.png) center;
        background-size: cover;
        padding: 10px;
        margin: auto;
        border-radius: 100%;
        cursor: pointer;
        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 100%;
        }
        .companies__select .multiselect {
            color: #fff;
        }
        .companies__select .multiselect__tags {
            background-color: transparent !important;
            padding-top: 6px;
        }
    }

    .multiselect__tags {
        background-color: transparent !important;
        padding-top: 6px;
    }

    input::placeholder {
        color: transparent;
    }

    // Floating
    textarea {
        background: #000;
        width: 100%;
        padding: 0.3rem 0;
        border: none;
        border-bottom: 1px solid white;
        &::placeholder {
            color: white;
            opacity: 1;
        }
    }

    .emojionearea,
    .emojionearea.form-control {
        border: none;
        border-bottom: 1px solid white;
        box-shadow: none;
    }

    .emojionearea-editor {
        // padding: 10px 30px 6px 2px;
        padding-left: 0;
    }

    .form-control:focus,
    .emojionarea.focusd {
        box-shadow: none;
        border: none;
        border-color: none;
        border-bottom: 1px solid white;
    }

    // Floating
    .floating-label {
        position: relative;
        margin-bottom: 20px;
    }

    .floating-input,
    .floating-select {
        font-size: 16px;
        padding: 3px 2px;
        display: block;
        width: 100%;
        // height: 30px;
        background-color: transparent;
        border: none;
        // border-bottom: 1px solid #757575;
    }

    // .floating-input:focus,
    // .floating-select:focus {
    //   outline: none;
    //   border-bottom: 2px solid #5264ae;
    // }
    .multi-select__label {
        position: absolute;
        z-index: 1;
        top: 10px;
    }

    .multi-select__label.float-label {
        top: -12px;
        color: gray;
    }

    .floating-label label {
        // color: #999;
        // font-size: 14px;
        // font-weight: normal;
        position: absolute !important;
        pointer-events: none;
        // left: 5px;
        top: 30%;
        transition: 0.2s ease all;
        -moz-transition: 0.2s ease all;
        -webkit-transition: 0.2s ease all;
    }

    .multiselect {
        .multiselect__content-wrapper {
            display: none !important;
            visibility: hidden !important;
            color: transparent !important;
            background: black;
            border: none !important;
            z-index: -1 !important;
            height: 0 !important;
            max-height: 0 !important;
            overflow: hidden;
        }
        .multiselect__option {
            display: none !important;
            padding: 0 !important;
        }
    }

    .floating-input:focus ~ label,
    .floating-input:not(:placeholder-shown) ~ label {
        top: -15px;
        color: gray;
    }
    .floating-label label.focused {
        top: -15px;
        color: gray;
    }

    // .floating-select:focus ~ label,
    // .floating-select:not([value=""]):valid ~ label {
    //   top: -225px;
    // }

    /* active state */

    .floating-input:focus ~ .bar:before,
    .floating-input:focus ~ .bar:after,
    .floating-select:focus ~ .bar:before,
    .floating-select:focus ~ .bar:after {
        width: 50%;
    }

    *,
    *:before,
    *:after {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .floating-textarea {
        min-height: 30px;
        max-height: 260px;
        // overflow: hidden;
        overflow-x: hidden;
        overflow-y: auto;
    }

    // Custom checkbox
    .custom-check,
    label {
        cursor: pointer;
    }
    .round {
        position: relative;
    }
    .round label {
        background-color: #fff;
        border: 1px solid #ccc;
        cursor: pointer;
        border-radius: 50%;
        height: 20px;
        left: 0;
        position: absolute;
        top: 0;
        width: 20px;
        transition: all .1s linear;
    }
    .round label:after {
        border: 2px solid #fff;
        border-top: none;
        border-right: none;
        content: "";
        height: 6px;
        left: 3px;
        opacity: 0;
        position: absolute;
        top: 5px;
        transform: rotate(-45deg);
        width: 12px;
    }
    .round input[type="checkbox"] {
        visibility: hidden;
    }
    .round input[type="checkbox"]:checked+label {
        background-color: #efa720;
        border-color: #efa720;
    }
    .round input[type="checkbox"]:checked+label:after {
        opacity: 1;
    }

    .profileImg {
        position: relative;
        .btn-logout {
            position: absolute;
            top: 82px;
            right: 120px;
            cursor: pointer;
            img {width: 30px;}
            @media (max-width: 600px) {
                right: 30px;
            }
        }
        .btn-power {
            position: absolute;
            top: 82px;
            left: 120px;
            width: 30px;
            cursor: pointer;
            @media (max-width: 600px) {
                left: 30px;
            }
        }
    }

</style>


<style lang="scss">
    #form_user_edit {
        .emojionearea {
            height: 100% !important;
            padding-right: 70px !important;
            background-color: black !important;
            &.floating-textarea {
                border: none;
                border-bottom: solid 1px white;
                border-radius: 0;
                padding-right: 30px !important;
                overflow-x: unset;
                overflow-y: unset;
            }
            .emojionearea-editor {
                white-space: normal !important;
                padding-right: 0 !important;
                min-height: 60px !important;
            }
            .emojionearea-button {
                div {
                    background-image: url(https://i.imgur.com/xljqgrH.png) !important;
                }
            }
        }
    }
</style>