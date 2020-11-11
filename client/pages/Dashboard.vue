<template>
    <div class="row">
        <div class="col-12">            
            <div class="container">
                <div class="row" id="dashboardpage">
                    <div class="col-md-12 text-center">
                        <div class="dashboard-header">
                            <img :src="selected.is_active ? '/imgs/active.png' : '/imgs/inactive.png'" alt=" " class="btn-power" @click="openActivePopup = true">
                            <div class="logoimage" @click="goProfile(selected)">
                                <img :src="serverUrl(selected.profile_pic ? selected.profile_pic.url : '/imgs/default.png')" alt />
                            </div>
                        </div>
                        <div class="mx-auto mt-3" style="width:300px;">
                            <multiselect 
                                v-model="selected" 
                                class="floating-label" 
                                id="search_user"
                                :options="users" 
                                label='name'
                                track-by="id"
                                placeholder=" "
                                :show-labels="false"
                                @select="selectUser"
                            ></multiselect>
                        </div>
                    </div>
                </div>

                <vs-popup class="active_popup" type="border" title :active.sync="openActivePopup">
                    <div class="form-group floating-label mx-auto mb-0 mt-3" style="width:262px;" v-if="selected.type == 'company' && !selected.is_active">
                        <input type="text" class="form-control floating-input" placeholder=" " id="state_license" v-model="selected.state_license" autocomplete="off" />
                        <label for="state_license">State License #</label>
                    </div>
                    <div class="text-center py-3">
                        <button class="btn btn-primary mr-3" @click="activeUser">{{selected.is_active ? 'Deactivate' : 'Activate'}}</button>
                        <button class="btn btn-420" @click="deleteUser">Delete</button>
                    </div>
                </vs-popup>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from "vue-multiselect";
    import { mapGetters } from "vuex";
    export default {
        components: {
            Multiselect,
        },
        middleware({ store, redirect }) {
            // If the user is not admin
            if (!store.state.auth.user || store.state.auth.user.name != '420portal') {
                return redirect('/')
            }
        },
        data() {
            return {
                selected: '',
                users: [],
                selectedIndex: null,
                maindata: {},
                profileId: {},              
                openActivePopup: false,
            };
        },
        computed: mapGetters({
            auth_user: 'auth/user',
        }),
        mounted() {
            this.getUsers();
        },
        methods: {
            getUsers(){
                const uri = '/users/list';
                this.axios.post(uri).then(response => {
                    this.users = response.data;
                    this.selected = this.users[0];
                    this.selectedIndex = this.users.findIndex(u => u.id == this.auth_user.id);
                });               
            },
            selectUser(option, id) {
                
            },
            activeUser(){
                if(this.selected.type == 'company' && !this.selected.is_active && !this.selected.state_license) {
                    alert('State License is required field.');
                    return false;
                }
                let params = { 
                    id: this.selected.id,
                    state_license: this.selected.state_license,
                };
                let uri = `/user/activate`;
                this.axios.post(uri, params)
                    .then(response => {
                        this.openActivePopup = false;
                        this.selected.is_active = response.data;
                        this.users[this.selectedIndex].is_active = response.data;
                    });
            },
            deleteUser(){
                if(!window.confirm('Are you sure?')) {
                    return false;
                }
                let params = { id: this.selected.id };
                let uri = `/user/${this.selected.id}`;
                this.axios.delete(uri, params)
                    .then(response => {
                        if(this.auth_user.id == this.selected.id) {
                            window.location.href = '/';
                        }
                        this.openActivePopup = false;
                        this.users.splice(this.selectedIndex, 1);
                        this.getUsers();
                    });
            },
            goProfile() {
                window.location.href = "/" + this.selected.username;
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
    @import '~/assets/sass/dashboard';
    // Floating
    .floating-label {
        position: relative;
    }
    .floating-input {
        padding: 4px 2px;
        display: block;
        width: 100%;
        background-color: transparent;
        border: none;
        box-shadow: unset;
    }

    .floating-label label {
        position: absolute !important;
        pointer-events: none;
        left: 0px;
        top: 30%;
        transition: 0.2s ease all;
        -moz-transition: 0.2s ease all;
        -webkit-transition: 0.2s ease all;
    }

    .floating-input:not(:placeholder-shown) ~ label,
    .floating-input:focus ~ label {
        top: -15px;
        color: gray;
    }
</style>

<style lang="scss">
    .link_portal {
        margin: 0 auto;
        a {
            font-size: 21px;
            color: #EFA720;
            text-decoration: none;
        }
        .multiselect__tags {
            border-bottom: none;
        }
        .multiselect__tags .multiselect__single {
            color: #EFA720;
            cursor: pointer;
            font-size: 21px !important;
        }
    }
    #dashboardpage {
        .multiselect__single {
            text-align: center;
            font-size: 26px;
        }
        .multiselect__tags {
            padding-right: 0;
        }
    }
</style>