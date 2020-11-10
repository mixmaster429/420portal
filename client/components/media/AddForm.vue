<template>
    <form @submit.prevent="type()" :id="'form_media_' + mode" class="form-media mt-3">
        <div class="form-group text-center">
            <div class="logo" v-if="mediaType !== 'video' && mode === 'edit'">
                <img v-if="mediaType === 'image'" class="signup_logo" :src="mediaData" />
            </div>
            <div v-if="mediaData.length > 0">
                <video v-if="mediaType === 'video'" width="320" height="240" controls disablepictureinpicture controlslist="nodownload">
                    <source :src="mediaData" type="video/mp4" />
                    <source :src="mediaData" type="video/webm" />
                    <source :src="mediaData" type="video/ogg" />Your browser does not support the video tag.
                </video>
            </div>
            <div v-if="mode==='add'">
                <label v-if="mediaData" class="mt-2 upload__label" for="file">
                    <div class="logo" for="file" v-if="mediaType !== 'video'">
                        <img v-if="mediaType === 'image'" class="signup_logo" :src="mediaData" style="image-orientation: from-image;" />
                    </div>Change
                </label>
                <label v-else class="mt-2 upload__label" for="file">
                    <div class="logo" for="file" v-if="mediaType !== 'video'">
                        <img v-if="mediaType === 'image'" class="signup_logo" :src="mediaData" />
                    </div>Add Media
                </label>
                <input type="file" hidden required id="file" name="file" class="btn-file" @change="previewImage" accept="image/*|video/*" />
            </div>
        </div>

        <div class="floating-label" @click="handleScroll" v-show="!$device.isMobile || ($device.isMobile && mediaData != '')">
            <textarea v-model="description" :id="'media_description_' + mode" class="form-control media-description floating-textarea" rows="2" placeholder=" "></textarea>
            <label for="media-description">Description</label>
        </div>

        <div class="floating-label" @click="handleScroll" style="position: relative" v-show="!$device.isMobile || ($device.isMobile && mediaData != '')" v-if="auth_user && auth_user.type == 'user'">
            <label v-bind:class="[floatUserLabel ? 'multi-select__label float-label' : 'multi-select__label']" for="users__select">Tag Users</label>
            <multiselect v-model="taggedUsers" class="users__select floating-input floating-select" label="name" open-direction="bottom" track-by="id" placeholder :options="users" :searchable="true" :multiple="true" :loading="userLoading" :showLabels="false" :internal-search="false" :clear-on-select="false" :close-on-select="true" :options-limit="5" :limit="8" :max-height="600" :show-no-results="false" :showNoOptions="false" :hide-selected="true" @search-change="asyncFind($event, 'user')" @open="floatUserLabel = true" @select="floatUserLabel = true" @close="taggedUsers.length >= 1 ? floatUserLabel = true : floatUserLabel = false" @remove="taggedUsers.length >= 1 ? '' : floatUserLabel = false">
                <template slot="tag" slot-scope="{ option, remove }">
                    <span class="custom__tag">
                        <span>{{ option.name }}</span>
                        <span class="custom__remove" @click="remove(option)"><fa icon="times" fixed-width class="ml-1"></fa></span>
                    </span>
                </template>
                <template slot="clear" slot-scope="props">
                    <div class="multiselect__clear" v-if="taggedUsers.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                </template>
                <span slot="noResult">No users with that name.</span>
                <!-- <span slot="noOptions">Type to search</span> -->
                <span slot="caret"></span>
            </multiselect>
        </div>

        <div class="floating-label" @click="handleScroll" style="position: relative" v-show="!$device.isMobile || ($device.isMobile && mediaData != '')" v-if="!from && auth_user && auth_user.type == 'user'">
            <label v-bind:class="[floatCompanyLabel ? 'multi-select__label float-label' : 'multi-select__label']">Tag Companies</label>
            <multiselect v-model="taggedCompanies" class="companies__select floating-input floating-select" v-bind:class="[taggedCompanies.length >= 1 ? 'select__disable' : '']" label="username" track-by="id" placeholder open-direction="bottom" :options="companies" :searchable="isCompanySearchable" :multiple="true" :loading="companyLoading" :showLabels="false" :internal-search="false" :clear-on-select="true" :close-on-select="true" :options-limit="5" :limit="1" :max="1" :max-height="companyMaxHeight" :show-no-results="false" :showNoOptions="false" :hide-selected="true" @search-change="asyncFind($event, 'company')" @open="floatCompanyLabel = true" @select="onSelect('company')" @close="onClose('company')" @remove="onRemove('company')">
                <template slot="tag" slot-scope="{ option, remove }">
                    <span class="custom__tag">
                <span>{{ option.username }}</span>
                    <span class="custom__remove" @click="remove(option)">
                <fa icon="times" fixed-width class="ml-1"></fa>
                </span>
                    </span>
                </template>
                <template slot="clear" slot-scope="props">
                    <div class="multiselect__clear" v-if="taggedCompanies.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                </template>
                <span slot="maxElements">You</span>
                <span slot="noResult">No company with that name.</span>
                <!-- <span slot="noOptions">Type to search</span> -->
                <span slot="caret"></span>
            </multiselect>
        </div>

        <div class="floating-label" @click="handleScroll" style="position: relative" v-show="!$device.isMobile || ($device.isMobile && mediaData != '')">
            <label v-bind:class="[floatStrainLabel ? 'multi-select__label float-label' : 'multi-select__label']" for="strains__select">Tag Marijuana Strain</label>
            <multiselect
                v-model="taggedStrains"
                class="strains__select floating-input floating-select"
                label="name"
                open-direction="bottom"
                track-by="id" placeholder
                :options="strains"
                :searchable="isStrainSearchable"
                :multiple="true"
                :loading="strainLoading"
                :internal-search="false"
                :clear-on-select="false"
                :close-on-select="true"
                :showLabels="false"
                :limit="1"
                :max="1"
                :max-height="strainMaxHeight"
                :show-no-results="false"
                :showNoOptions="false"
                :hide-selected="true"
                @search-change="asyncFind($event, 'strain')"
                @open="floatStrainLabel = true"
                @select="onSelect('strain')"
                @close="onClose('strain')"
                @remove="onRemove('strain')"
            >
                <template slot="tag" slot-scope="{ option, remove }">
                    <span class="custom__tag">
                        <span>{{ option.name }}</span>
                        <span class="custom__remove" @click="remove(option)">
                            <fa icon="times" fixed-width class="ml-1"></fa>
                        </span>
                    </span>
                </template>
                <template slot="clear" slot-scope="props">
                    <div class="multiselect__clear" v-if="taggedStrains.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                </template>
                <span slot="maxElements">You can only tag one strain.</span>
                <span slot="noResult">No strain with that name.</span>
                <!-- <span slot="noOptions">Type to search</span> -->
                <span slot="caret"></span>
            </multiselect>
        </div>

        <input type="hidden" name="model" :value="model = 'post'" />
        <div class="form-group" v-show="!$device.isMobile || ($device.isMobile && mediaData != '')">            
            <button class="btn btn-primary mt-2 d-block px-3 mx-auto d-flex align-items-center" style="font-size: 20px;" :disabled="loading">
                <span v-if="loading" class="spinner-border text-dark spinner-border-sm mr-1"></span>
                <span v-if="mode === 'add'">Post</span>
                <span v-else-if="mode === 'edit'">Post</span>
            </button>
        </div>
        <span v-if="success === true" class="text-success ml-1">Strain added successfully.</span>
        <span v-else-if="success === false" class="text-danger ml-1">Error adding media.</span>
    </form>
</template>

<script>
import Axios from "axios";
import isEmpty from "lodash";
import { mapGetters } from "vuex";
import Multiselect from "vue-multiselect";

export default {
    name: "AddMedia",
    components: {
        Multiselect
    },
    props: {
        mode: {
            default: "add"
        },
        from: {
            default: 0
        },
        editData: 0,
        mainData: {}
    },
    data() {
        return {
            companiesUrl: "/portals",
            usersUrl: "/users",
            strainsUrl: "/marijuana-strains",
            media: "",
            mediaType: "",
            mediaData: "",
            description: "",
            model: "",
            taggedCompanies: [],
            taggedUsers: [],
            taggedStrains: [],
            companies: [],
            users: [],
            strains: [],
            companyData: [],
            userData: [],
            strainData: [],
            success: null,
            loading: false,
            companyLoading: false,
            userLoading: false,
            strainLoading: false,
            floatCompanyLabel: false,
            floatUserLabel: false,
            floatStrainLabel: false,
            companyMaxHeight: 600,
            strainMaxHeight: 600,
            isCompanySearchable: true,
            isStrainSearchable: true,
        };
    },
    computed: mapGetters({
        auth_user: 'auth/user',
    }),
    mounted() {
        var _this = this;
        this.getData();
        this.$root.$on("eventing", () => {
            this.update();
        });
        $(`#media_description_${this.mode}`).emojioneArea({
            pickerPosition: "bottom",
            search: false,
            autocomplete: false,
            placeholder: " ",
            events: {
                ready: function() {
                    if(_this.mode == 'edit' && _this.mainData) {
                        this.setText(_this.mainData.description);
                    }
                },
                blur: function (editor, event) {
                    _this.description = this.getText();
                    if(_this.description == '') {
                        $(".media-description").siblings('label').removeClass('focused');
                    }
                },
                focus: function (editor, event) {
                    $(".media-description").siblings('label').addClass('focused');
                }
            }
        });
        this.mode === "edit" && this.fetchData(this.editData);
    },
    watch: {
        editData: function(newVal, oldVal) {
            this.selected = newVal;
            if(this.mode == 'edit') {
                this.fetchData(newVal);
            }
        }
        // $route: 'fetchData(editData)'
    },
    methods: {
        fetchData(id) {
            this.description = this.mainData.description;
            if(this.description) {
                $(".media-description").siblings('label').addClass('focused');
            }
            this.mediaType = this.mainData.type;
            this.mediaData = this.serverUrl(this.mainData.url);
            this.taggedUsers = this.mainData.tagged_usersData ? this.mainData.tagged_usersData : "";
            this.taggedCompanies = this.mainData.tagged_companyData ? this.mainData.tagged_companyData : "";
            this.taggedStrains = this.mainData.tagged_strainData ? this.mainData.tagged_strainData : "";


            if (!_.isEmpty(this.taggedCompanies)) {
                this.floatCompanyLabel = true;
                this.isCompanySearchable = false;
                this.companyMaxHeight = 0;
            } else {
                this.floatCompanyLabel = false;
                this.isCompanySearchable = true;
            }
            if (!_.isEmpty(this.taggedUsers)) {
                this.floatUserLabel = true;
            } else {
                this.floatUserLabel = false;
            }
            if (!_.isEmpty(this.taggedStrains)) {
                this.floatStrainLabel = true;
                this.isStrainSearchable = false;
                this.strainMaxHeight = 0;
            } else {
                this.floatStrainLabel = false;
                this.isStrainSearchable = true;
            }
        },
        type() {
            if (this.mode === "edit") {
                // console.log("edit");
                this.update();
                return;
            }
            // console.log("add");
            this.save();
            return;
        },
        update() {
            const url = `/media/${this.editData}`;
            const usersList = [];
            this.taggedUsers.map(user => usersList.push(user.id));

            const params = {
                description: this.description,
                taggedCompanies: !_.isEmpty(this.taggedCompanies) ? this.taggedCompanies[0].id : "",
                taggedUsers: JSON.stringify(usersList),
                taggedStrains: !_.isEmpty(this.taggedStrains) ? this.taggedStrains[0].id : ""
            };
            const headers = {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            };
            this.loading = true;
            this.axios
                .put(url, params)
                .then(res => {
                    this.loading = false;
                    // console.log('response data is', res.data);
                    this.$toast.success("Edited Successfully.", "");
                    if(this.auth_user) {
                        if(this.from) {
                            this.axios.post('/get/portal', {id : this.from}).then(response => {
                                window.location.href = "/" + response.data.username;
                            });
                        } else {
                            window.location.href = "/" + this.auth_user.name;
                        }
                    } else {
                        window.location.href = '/';
                    }
                })
                .catch(e => {this.$toast.error("Error editing media", "");this.loading = false;});
            
        },
        save() {
            const url = "/media";
            const usersList = [];
            this.taggedUsers.map(user => usersList.push(user.id));

            const params = {
                file: this.media,
                type: this.mediaType,
                description: this.description,
                model: this.model,
                portal_id: this.from,
                taggedCompanies: this.taggedCompanies.length >= 1 ? this.taggedCompanies[0].id : "",
                taggedUsers: JSON.stringify(usersList),
                taggedStrains: this.taggedStrains.length >= 1 ? this.taggedStrains[0].id : "",
            };
            const headers = {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            };
            const formData = new FormData();
            for (let key in params) {
                formData.append(key, params[key]);
            }
            this.loading = true;
            this.axios.post(url, formData, headers)
                .then(res => {
                    console.log('success');
                    this.loading = false;
                    this.$toast.success("Media has been added", "");
                    if(this.auth_user) {                        
                        window.location.href = "/" + this.auth_user.username;                        
                    } else {
                        window.location.href = '/';
                    }
                })
                .catch(e => {this.$toast.error("Error adding media", ""); console.log('error'); this.loading = true;});
        },
        getData() {
            const req1 = this.axios.post(this.companiesUrl);
            const req2 = this.axios.get(this.strainsUrl);
            const req3 = this.axios.get(this.usersUrl);

            this.axios
                .all([req1, req2, req3])
                .then(
                    this.axios.spread((...responses) => {
                        this.companyData = responses[0].data;
                        this.strainData = responses[1].data;
                        this.userData = responses[2].data;
                    })
                )
                .catch(errors => {
                    console.log("error loading data.", errors);
                });
        },
        limitText(count) {
            return `and ${count} other companies`;
        },
        asyncFind(query, type) {
            switch (type) {
                case "company":
                    this.companyLoading = true;
                    this.ajaxFindData(query, type).then(response => {
                        this.companies = response;
                        this.companyLoading = false;
                    });
                    break;

                case "user":
                    this.userLoading = true;
                    this.ajaxFindData(query, type).then(response => {
                        this.users = response;
                        this.userLoading = false;
                    });
                    break;

                case "strain":
                    this.strainLoading = true;
                    this.ajaxFindData(query, type).then(response => {
                        this.strains = response;
                        this.strainLoading = false;
                    });
                    break;

                default:
                    break;
            }
        },
        ajaxFindData(query, type) {
            return new Promise((resolve, reject) => {
                switch (type) {
                    case "company":
                        setTimeout(() => {
                            const results = this.companyData.filter(
                                (element, index, array) => {
                                    return element.username
                                        .toLowerCase()
                                        .includes(query.toLowerCase());
                                }
                            );
                            resolve(results);
                        }, 1000);

                        break;

                    case "user":
                        setTimeout(() => {
                            let results = this.userData.filter((element, index, array) => {
                                return element.name.toLowerCase().includes(query.toLowerCase());
                            });

                            resolve(results);
                        }, 1000);

                        break;

                    case "strain":
                        setTimeout(() => {
                            let results = this.strainData.filter(
                                (element, index, array) => {
                                    return element.name
                                        .toLowerCase()
                                        .includes(query.toLowerCase());
                                }
                            ).sort(function(a, b){
                                let x = a.name.toLowerCase();
                                let y = b.name.toLowerCase();
                                if (x < y) {return -1;}
                                if (x > y) {return 1;}
                                return 0;
                            });

                            for(let i = results.length-1 ; i >= 0 ; i--) {
                                let el = results[i];
                                if(el.name.toLowerCase().indexOf(query.toLowerCase()) == 0) {
                                    let index = results.findIndex(item => item.id === el.id);
                                    results.splice(index, 1);
                                    results.unshift(el);
                                }
                            }
                            resolve(results);
                        }, 1000);

                        break;

                    default:
                        break;
                }
                setTimeout(() => {
                    const results = this.companyData.filter((element, index, array) => {
                        return element.name.toLowerCase().includes(query.toLowerCase());
                    });
                    resolve(results);
                }, 1000);
            });
        },
        clearAll() {
            this.taggedCompanies = [];
        },
        onSelect(type) {
            switch (type) {
                case "company":
                    this.isCompanySearchable = false;
                    this.companyMaxHeight = 0;
                    this.floatCompanyLabel = true;
                    break;

                case "strain":
                    this.isStrainSearchable = false;
                    this.strainMaxHeight = 0;
                    this.floatStrainLabel = true;
                    break;

                default:
                    break;
            }
        },
        onRemove(type) {
            switch (type) {
                case "company":
                    this.companyMaxHeight = 600;
                    this.floatCompanyLabel = false;
                    this.isCompanySearchable = true;
                    break;

                case "strain":
                    this.strainMaxHeight = 600;
                    this.floatStrainLabel = false;
                    this.isStrainSearchable = true;
                    break;

                default:
                    break;
            }
        },
        onClose(type) {
            switch (type) {
                case "company":
                    if (_.isEmpty(this.taggedCompanies)) {
                        this.floatCompanyLabel = false;
                    } else {
                        this.floatCompanyLabel = true;
                    }
                    break;

                case "strain":
                    if (_.isEmpty(this.taggedStrains)) {
                        this.floatStrainLabel = false;
                    } else {
                        this.floatStrainLabel = true;
                    }
                    break;

                default:
                    break;
            }
        },
        previewImage(event) {
            this.mediaData = "";
            this.mediaType = "";
            let input = event.target;

            if (input.files[0].type.match("image/*")) {
                this.mediaType = "image";
            } else if (input.files[0].type.match("video/*")) {
                this.mediaType = "video";
            }

            if (input.files && input.files[0]) {
                this.media = input.files[0];
                this.mediaData = URL.createObjectURL(this.media);
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
        reset() {
            console.log('reset');
            this.description = '';
            $(".media-description").data('emojioneArea').setText('');
            this.taggedUsers = [];
            this.taggedCompanies = [];
            this.taggedStrains = [];
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


<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss" scoped>
    // .vs-popup {
    //   height: 80% !important;
    // }
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
        }
    }

    // Floating
    .floating-label {
        margin-bottom: 22px;
    }

    .floating-input,
    .floating-select {
        // font-size: 14px;
        padding: 4px 2px;
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
        top: -13px;
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
            background-color: #000 !important;
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
        &__tags .multiselect__input {
            margin-bottom: 0 !important;
        }
    }

    .floating-textarea ~ label.focused,
    .floating-input:focus ~ label,
    .floating-input:not(:placeholder-shown) ~ label {
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

    .media__add .multiselect__content-wrapper {
        background-color: #000 !important;
    }
</style>

<style lang="scss">
    .form-media {
        .emojionearea {
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
