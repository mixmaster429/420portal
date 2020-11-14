<template id="forumlist">
    <div class="forum_area">
        <section class="loader" v-if="loader">
            <img src="/imgs/loader2.gif" alt="" srcset="">      
        </section>
        <div>
            <div class="forum_fixed">                
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="forum_header">
                                <img src="/imgs/h_icon4.png" width="35" style="margin-top: -8px; margin-right:3px;" alt="" />
                                <h1 @click="showmainalert()">Marijuana Forums</h1>
                                <div class="forum_button">
                                    <div class="forum_button_area">
                                        <ul>
                                            <li class="dropdown">
                                                <button class="btn_forum_search dropdown-toggle" data-toggle="dropdown" @click="toggleSearchBox()">
                                                    <span><fa icon="search" fixed-width></fa></span>
                                                </button>
                                                <div class="search_input dropdown-menu">
                                                    <button class="btn_times" @click="searchTimes()"><fa icon="times" fixed-width></fa></button>
                                                    <input type="text" name="" class="forum_search" v-model="searchValue" placeholder="Search Topics & Users" @keyup.enter="getSearchResult()">
                                                    <button class="btn_find" @click="getSearchResult()">Search</button>
                                                </div>
                                            </li>
                                            <li class="dropdown">
                                                <button class="btn_forum_line" @click="toggleSelectBox()">
                                                    <span><fa icon="bars"></fa></span>
                                                </button>
                                                <div class="select_input text-center" v-show="this.selectbox">
                                                    <div class="nav_top">
                                                        <ul>
                                                            <li :class="{ selecteditem: showTopics }">
                                                                <span @click="showUserTopics()"><fa icon="edit" class="fs-20"></fa></span>
                                                            </li>
                                                            <li :class="{ selecteditem: showBookmarks }">
                                                                <span @click="showUserBookmark()"><fa icon="bookmark" class="fs-20"></fa></span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="nav_body">
                                                        <div v-show="showBookmarks">
                                                            <p class="limittextlength" v-for="item in this.alltopic.bookmarkedTopic" :key="item.id">
                                                                <a v-if="item.title" :href="'/marijuana-forums/' + item.slug + '/' +item.id">
                                                                    <span><fa icon="bookmark" class="main-color fs-10"></fa></span>
                                                                    <span v-if="item.title" class="bookmarktitle">{{ item.title }}</span>
                                                                </a>
                                                                <a v-if="item.origin" :href="'/marijuana-forums/' + item.origin.slug+ '/' + item.origin.id">
                                                                    <span><fa icon="bookmark" class="main-color fs-10"></fa></span>
                                                                    <span class="bookmarktitle">
                                                                        <span class="col-blue">Reply:</span>
                                                                        <span v-if="item.user">
                                                                            {{ item.user.name }}
                                                                        </span>
                                                                        <span v-else>
                                                                            Removed user
                                                                        </span>                                                                   
                                                                    </span>
                                                                </a>
                                                            </p>
                                                        </div>
                                                        <div v-show="showCategories">
                                                            <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;Stoner's Lounge</p>
                                                            <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;Concentrates</p>
                                                            <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;Edibles</p>
                                                            <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;Indoor/Outdoor Grow</p>
                                                            <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;Paraphernalia</p>
                                                            <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;CBD/THC</p>
                                                            <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;Marijuana News</p>
                                                            <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;Advertise</p>
                                                        </div>
                                                        <div v-show="showTopics">
                                                            <p class="limittextlength" v-for="item in this.alltopic.usertopics" :key="item.id">
                                                                <a v-if="item.title" :href="'/marijuana-forums/'+item.slug+'/'+item.id">
                                                                    <span class="col-blue">Started:</span>
                                                                    <span v-if="item.title" class="bookmarktitle">{{ item.title }}</span>
                                                                </a>    
                                                                <a v-else :href="'/marijuana-forums/' + item.get_m_parent.slug + '/' + item.get_m_parent.id">
                                                                    <span class="col-blue">Reply:</span>
                                                                    <span v-if="item.get_m_parent.title" class="bookmarktitle">{{ item.get_m_parent.title }}</span>
                                                                </a>                                                            
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="custom_modal_back" id="custom_modal_back" @click="hiddenmodal()" v-show="this.selectbox"></div>                                                                            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn_area">
                            <div class="forum_category">
                                <select name="" id="" @change="onChange()" v-model="catId">
                                    <option value="">All Categories</option>
                                    <option value="Stoner's Lounge">Stoner's Lounge</option>
                                    <option value="Concentrates">Concentrates </option>
                                    <option value="Edibles">Edibles</option>
                                    <option value="Indoor/Outdoor Grow">Indoor/Outdoor Grow</option>
                                    <option value="Paraphernalia">Paraphernalia</option>
                                    <option value="CBD/THC">CBD/THC</option>
                                    <option value="Marijuana News">Marijuana News</option>
                                    <option value="Advertise">Advertise</option>
                                </select>
                            </div>
                            <div class="forum_topic">
                                <button @click="toggle()" class="btn_forum_topic new_topic"><span><fa icon="plus" fixed-width></fa></span> New Topic</button>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="col-md-12">
                        <div class="forum_list">
                            <table class="forum_list_table">
                                <thead>
                                    <tr>
                                        <td align="left">
                                            <p>Topic</p>
                                        </td>
                                        <td width="150">

                                        </td>
                                        <td width="65" align="center">
                                            <p>Replies</p>
                                        </td>
                                        <td width="65" align="center">
                                            <p>Views</p>
                                        </td>
                                        <td width="90" align="center">
                                            <p>Activity</p>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in displaytopic" :key="item.id">
                                        <td>                                        
                                            <div @click="goThread(item)" class="mo-mr25 w-100 mo_hidden" style="cursor:pointer;">
                                                <p class="forum_title">{{ item.title }}</p>
                                                <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;{{ item.category }}</p>
                                            </div>
                                            <a :href="'/marijuana-forums/' + item.slug + '/' + item.id" class="w100 mo_show">
                                                <ul class="mo_topic_item">
                                                    <li style="width:45px">
                                                        <img v-if="item.user" class="img_user_pic" :src="serverUrl(item.user.profile_pic ? item.user.profile_pic.url : '/imgs/default_sm.png')" alt />
                                                        <img v-else class="img_user_pic" src="/imgs/default_sm.png" alt />
                                                    </li>
                                                    <li style="width: calc(100% - 105px);" class="mo_topicinfo">
                                                        <a class="forum_title">{{ item.title }}</a>
                                                        <p class="forum_category"><fa :icon="['far', 'dot-circle']" class="main-color fs-10"></fa>&nbsp;&nbsp;{{ item.category }}</p>
                                                    </li>
                                                    <li style="width:50px;margin-left:5px;" class="mo_topicactivity">
                                                        <p style="margin-bottom:0px;">
                                                            <span class="forum_replies" v-if="item.children && item.children.length">{{ item.children.length }}</span>
                                                        </p>
                                                        <p style="margin-bottom:0px;">
                                                            <span class="forum_activity">{{ item.time }}</span>
                                                        </p>
                                                    </li>
                                                </ul>                                            
                                            </a>
                                        </td>
                                        <td class="mo_hidden">
                                            <a v-if="item.user" :href="'/'+item.user.username" :title="item.user.name">
                                                <img class="img_user_pic" :src="serverUrl(item.user.profile_pic ? item.user.profile_pic.url : '/imgs/default.png')" alt />
                                            </a>
                                            <a v-else href="javascript:void(0);" title="Removed User">
                                                <img class="img_user_pic" src="/imgs/default_sm.png" alt />
                                            </a>
                                            <a v-for="sitem in item.reply_users" :key="sitem.id" :href="'/'+sitem.username" :title="sitem.name">
                                                <img class="img_user_pic" :src="serverUrl(sitem.profile_pic ? sitem.profile_pic.url : '/imgs/default.png')" alt />
                                            </a>
                                        </td>
                                        <td  class="mo_hidden" align="center">
                                            <span class="forum_replies" v-if="item.children && item.children.length">{{ item.children.length }}</span>
                                        </td>
                                        <td class="mo_hidden" align="center">
                                            <span class="forum_views">{{ item.views }}</span>
                                        </td>
                                        <td class="mo_hidden" align="center">
                                            <span class="forum_activity">{{ item.time }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="sel_line"></div>
                    </div>
                </div>
            </div>
            <div class="mo_show_count">
                <span class="cur_position"></span>
                <span class="total_number">{{ lastpage }}</span>
            </div>
        </div>
        
        <client-only>
            <div class="new_forum" id="new_forum" v-show="isOpen">
                <div class="grippie"></div>
                <div class="body">
                    <div class="contaier-fluid">
                        <div class="row">
                            <div class="editorsection">
                                <div>
                                    <span class="fs-20" style="color:#fefefe;">
                                        <fa icon="arrow-right"></fa>&nbsp;&nbsp;Create a New Topic
                                    </span>
                                </div>
                                <p v-if="errors.length">
                                    <ul>
                                        <li style="display:inline-block;color:red;font-family:Arial;" v-for="error in errors" :key="error.id">{{ error }}</li>
                                    </ul>
                                </p>
                                <form @submit.prevent="createTopic">
                                    <div class="mt-15">
                                        <input type="text" name="forumtitle" placeholder="Title" class="form-control " v-model="topic.title">
                                    </div>
                                    <div class="mt-15">
                                        <select name="" id="" class="form-control" v-model="topic.category">
                                            <option value="" selected>Select Category</option>
                                            <option value="Stoner's Lounge">Stoner's Lounge</option>
                                            <option value="Concentrates">Concentrates </option>
                                            <option value="Edibles">Edibles</option>
                                            <option value="Indoor/Outdoor Grow">Indoor/Outdoor Grow</option>
                                            <option value="Paraphernalia">Paraphernalia</option>
                                            <option value="CBD/THC">CBD/THC</option>
                                            <option value="Marijuana News">Marijuana News</option>
                                            <option value="Advertise">Advertise</option>
                                        </select>
                                    </div>
                                </form>
                                <div class="mt-15">
                                    <div class="quill-editor" 
                                        v-model="topic.detail"
                                        @focus="hideFooter()"
                                        @blur="showFooter()"
                                        v-quill:forumQuillEditor="editorOption">
                                    </div>
                                </div>                            
                                <div class="mt-15 mb-3">
                                    <button class="btn_forum_topic" @click="createTopic()">
                                        <span><fa icon="plus" fixed-width></fa></span>
                                        Create Topic
                                    </button>
                                    &nbsp;&nbsp;&nbsp;
                                    <button class="btn_cancel" @click="alertconfirm()">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </client-only>

        <div class="modal fade" id="titleModal">
            <div class="modal-dialog modal-lg border-0">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <button type="button" class="close text-420" data-dismiss="modal"><fa icon="times" fixed-width></fa></button>
                    </div>
                    <div class="modal-body" style="background:white !important;">
                        <div class="row">
                            <div class="col-12 text-center">
                                <p style="font-size: 18px;">
                                    If You're Looking for Marijuana Forums, 420Portal is the Community for You. <br />
                                    Learn and Share Your Knowledge on Weed Topics.
                                    420Portal's Cannabis Forums will Help you Grow!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import firebase from '../../Firebase';
    import { mapGetters } from "vuex";

    // require styles
    import 'quill/dist/quill.core.css'
    import 'quill/dist/quill.snow.css'
    import 'quill/dist/quill.bubble.css'

    export default {
        computed: mapGetters({
            auth_user: 'auth/user',
        }),
        data() {
            return {
                isOpen: false,
                searchbox: false,
                selectbox: false,
                topic: {},
                alltopic: [],
                displaytopic: [],
                text: "",
                catId: "",
                selectedCategory: "",
                searchValue: "",
                loader: false,
                errors: {},
                page: 1,
                lastpage: 0,
                showTopics: true,
                showCategories: false,
                showBookmarks: false,
                showMobile: true,
                customToolbar: [
                    ["bold", "italic", "underline"],
                    ["blockquote", "code-block"],
                    [{
                        list: "ordered"
                    }, {
                        list: "bullet"
                    }, {
                        list: "check"
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    ["link", "image", "video"]
                ],
                editorOption: {
                    modules: {
                        "emoji-toolbar": true,
                        "emoji-shortname": true,
                        toolbar: [
                            ['emoji'],
                            ['bold', 'italic'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'size': ['small', false, 'large', 'huge'] }],
                            [{ 'color': [] }, { 'background': [] }],
                            ['link', 'image', 'video']
                        ],
                        syntax: {
                            highlight: text => hljs.highlightAuto(text).value
                        }
                    }
                },
            }
        },
        created() {

        },
        mounted() {
            if(process.client) {
                let category = localStorage.getItem('forum_category');
                if(category) {
                    this.catId = category;
                }
                let keyword = localStorage.getItem('forum_keyword');
                if(keyword) {
                    this.searchValue = keyword;
                }
                localStorage.clear();
            }
            this.getalltopics();
            this.scroll();
        },
        methods: {
            scroll() {
                window.onscroll = () => {
                    if (this.page > Math.ceil(this.lastpage / 30) || this.loader) return false;
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;
                    if (bottomOfWindow) {
                        this.getalltopics();
                    }
                };
            },
            getalltopics() {
                let uri = '/topic/all';
                let params = {
                    keyWord: this.searchValue,
                    page: this.page,
                    catId: this.catId,
                };
                this.loader = true;
                this.axios.post(uri, params).then(response => {
                    this.alltopic = response.data;
                    if (this.displaytopic.length == 0) {
                        this.displaytopic = response.data.allforum;
                    } else {
                        this.displaytopic = _.concat(this.displaytopic, response.data.allforum);
                    }
                    this.page++;
                    this.lastpage = this.alltopic.lastpage;
                    // console.log(this.lastpage);
                    this.loader = false;
                });
                this.topic.category = "";
            },
            showmainalert() {
                $("#titleModal").modal();
            },
            alertconfirm() {
                if(window.confirm("Are you sure?")) {
                    this.isOpen = !this.isOpen;
                    if (this.$device.isMobile) {
                        this.showMobile = !this.showMobile;
                    }
                }
            },
            replacetitle(title) {
                let strtitle = title.toLowerCase();
                strtitle = strtitle.replace("%", '');
                let returnval = "";
                for (let index = 0; index < strtitle.length; index++) {
                    if (strtitle[index] == " ") {
                        returnval += "-";
                    } else {
                        returnval += strtitle[index];
                    }
                }
                return returnval;
            },
            goThread(item) {
                window.location.href = `/marijuana-forums/${item.slug}/${item.id}`;
            },
            getSearchResult() {
                this.catId = "";
                this.keyWord = this.searchValue;
                this.displaytopic = "";
                this.page = 1;
                this.getalltopics();
            },
            searchTimes() {
                this.searchbox = false;
            },
            onChange() {
                this.selectbox = false;
                this.searchbox = false;
                this.keyWord = "";
                this.searchValue = "";
                this.displaytopic = "";
                this.catId = event.target.value;
                this.page = 1;
                this.getalltopics();
            },
            toggle() {
                if (this.auth_user) {
                    if (this.$device.isMobile) {
                        this.showMobile = !this.showMobile;
                    }

                    this.isOpen = !this.isOpen;
                    this.errTitle = false;
                    this.errCategory = false;
                    this.errDetail = false;
                    var scrollTop = $(".sel_line").offset().top;
                    $('html').animate({
                        scrollTop: scrollTop - 100
                    }, 'fast');
                } else {
                    $("#loginmodal").modal();
                }
            },
            toggleSearchBox() {
                this.searchbox = !this.searchbox;
                this.selectbox = false;
            },
            toggleSelectBox() {
                this.selectbox = !this.selectbox;
                this.searchbox = false;
            },
            createTopic() {
                if (this.topic.title && this.topic.category && this.topic.detail) {
                    let uri = '/topic/create';
                    this.page = 1;
                    this.topic.page = 1;
                    this.loader = true;
                    this.axios.post(uri, this.topic).then((response) => {
                        this.alltopic = response.data;
                        this.displaytopic = "";
                        this.displaytopic = this.alltopic.allforum;
                        this.lastpage = this.alltopic.lastpage;
                        this.isOpen = false;
                        this.loader = false;
                        this.page++;
                        this.topic.title = "";
                        this.topic.category = "";
                        this.topic.detail = "";
                        this.showMobile = true;

                    });
                } else {
                    this.errors = [];
                    if (!this.topic.title) {
                        this.errors.push("Title required.");
                    }
                    if (!this.topic.category) {
                        this.errors.push("Category required.");
                    }
                    if (!this.topic.detail) {
                        this.errors.push("Detail required.");
                    }
                    return false;
                }

            },
            hiddenmodal() {
                this.selectbox = false;
            },
            showCategoryList() {
                this.showTopics = false;
                this.showCategories = true;
                this.showBookmarks = false;
            },
            showUserTopics() {
                this.showTopics = true;
                this.showCategories = false;
                this.showBookmarks = false;
            },
            showUserBookmark() {
                this.showTopics = false;
                this.showCategories = false;
                this.showBookmarks = true;
            },
            hideFooter() {
                if(this.$device.isMobile) $("#app").addClass('focus_comment')
            },
            showFooter() {
                if(this.$device.isMobile) $("#app").removeClass('focus_comment')
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
    } 
</script>
<style lang="scss">
    #emoji-palette {
        top: 0 !important;
        left: 0 !important;
    }
</style>