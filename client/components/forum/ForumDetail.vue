<template id="forumdetail">
    <div v-show="loaded" class="forum_area">
        <section class="loader" v-show="loader">
            <img src="/imgs/loader2.gif" alt="" srcset="">      
        </section>
        <div>
            <div class="forum_fixed">           
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="forum_header">
                                <img src="/imgs/h_icon4.png" width="35" style="margin-top: -8px;margin-right:3px;" alt="Marijuana Forums" />
                                <h2 @click="goForumHome()">Marijuana Forums</h2>
                                <div class="forum_button">
                                    <div class="forum_button_area">
                                        <ul>
                                            <li class="dropdown">
                                                <button class="btn_forum_search dropdown-toggle" @click="toggleSearchBox()" data-toggle="dropdown">
                                                    <span><fa icon="search" fixed-width></fa></span>
                                                </button>
                                                    <div class="search_input dropdown-menu" >
                                                    <button type="button" class="btn_times" @click="searchTimes()"><fa icon="times" fixed-width></fa></button>
                                                    <input type="text" name="" class="forum_search" v-model="searchValue" placeholder="Search Topics & Users" @keyup.enter="getSearchResult()">
                                                    <button class="btn_find" @click="getSearchResult()">Search</button>
                                                </div>
                                            </li>
                                            <li>
                                                <button class="btn_forum_line" @click="toggleSelectBox()">
                                                    <span><fa icon="bars" fixed-width></fa></span>
                                                </button>
                                                <div class="select_input text-center" v-show="this.selectbox">
                                                    <div class="nav_top">
                                                        <ul>
                                                            <li :class="{ selecteditem: showTopics }">
                                                                <span @click="showUserTopics()"><fa icon="edit"></fa></span>
                                                            </li>
                                                            <li :class="{ selecteditem: showBookmarks }">
                                                                <span @click="showUserBookmark()"><fa icon="bookmark" class="fs-20"></fa></span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="nav_body">
                                                        <div v-show="showBookmarks">
                                                            <p class="limittextlength" v-for="item in curtopic.bookmarkedTopic" :key="item.id">
                                                                <a v-if="item.title" :href="'/marijuana-forums/' + item.slug + '/' + item.id">
                                                                    <span><fa icon="bookmark" class="main-color fs-10" fixed-width></fa></span>
                                                                    <span class="bookmarktitle">{{ item.title }}</span>
                                                                </a>
                                                                <a v-else :href="`/marijuana-forums/${item.origin.slug}/${item.origin.id}`">
                                                                    <span><fa icon="bookmark" class="main-color fs-10" fixed-width></fa></span>
                                                                    <span class="bookmarktitle">
                                                                        <span class="col-blue">Reply:</span>
                                                                        <span>{{ item.user ? item.user.name : 'Removed User' }}</span>                                                              
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
                                                            <p class="limittextlength" v-for="item in curtopic.usertopics" :key="item.id">
                                                                <a v-if="item.title" :href="'/marijuana-forums/' + item.slug + '/' + item.id">
                                                                    <span class="col-blue">Started:</span>
                                                                    <span class="bookmarktitle">{{ item.title }}</span>
                                                                </a>    
                                                                <a v-else :href="'/marijuana-forums/' + item.get_m_parent.title + '/' + item.get_m_parent.id">
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
                            <div class="forum_category"></div>
                            <div class="forum_topic">
                                <button @click="newtopictoggle()" class="btn_forum_topic new_topic"><span><fa icon="plus" fixed-width></fa></span> New Topic</button>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="col-md-12">
                        <div class="cur_forum" v-show="loaded">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/marijuana-forums">Forums</a></li>
                                <li class="breadcrumb-item"><a href="javascript:;" class="text-420" @click="viewCategoryForums(forum.category)">{{ forum.category }}</a></li>
                                <li class="breadcrumb-item active">{{ forum.title }}</li>
                            </ul>
                            <h1>{{ forum.title }}</h1>
                            <div class="cur_forum_detail critical-error-div">
                                <div class="topic-avatar">
                                    <a v-if="forum.user" class="trigger-user-card main-avatar" :href="'/'+forum.user.username" data-user-card="">
                                        <img alt="" width="45" height="45" :src="serverUrl(forum.user.profile_pic ? forum.user.profile_pic.url : '/imgs/default_sm.png')" class="avatar">
                                    </a>
                                    <a v-else class="trigger-user-card main-avatar" href="javascript:;" data-user-card="">
                                        <img alt="" width="45" height="45" src="/imgs/default_sm.png" class="avatar">
                                    </a>
                                </div>
                                <div class="topic-body">
                                    <div class="topic-meta-data">
                                        <div class="names">
                                            <a v-if="forum.user" :href="'/'+forum.user.username">
                                                <img alt="" width="45" height="45" :src="serverUrl(forum.user.profile_pic ? forum.user.profile_pic.url : '/imgs/default_sm.png')" class="mo_show avatar">
                                                &nbsp;&nbsp;<span class="mo_lh45 username">{{ forum.user.name }}</span>                                            
                                            </a>
                                            <a v-else href="javascript:void(0);">
                                                <img alt="" width="45" height="45" src="/imgs/default_sm.png" class="mo_show avatar">
                                                <span class="mo_lh45 username">
                                                    Removed User
                                                </span>                                            
                                            </a>
                                        </div>

                                        <div class="post-infos">
                                            <span :title="forum.created_at" data-time="" data-format="" class="relative-date">{{ forum.created_at | moment("from",curtopic.curtime,true) }}</span>
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                    <div class="topic-text">
                                        <div class="previewdetail" v-html="forum.detail"></div>
                                    </div>
                                    <div class="post-menu-area">
                                        <div class="topic_action">
                                            <div>
                                                <ul>
                                                    <li>
                                                        <button class="btn-transparent btn_like_num" v-show="forum.likes.length">{{ forum.likes.length }}</button>
                                                        <button class="btn-transparent btn_like_topic" :data-topic-id="forum.id" :data-liked="is_like(curtopic.curforum.likeslist)" @click="changelike(forum, 'main')">
                                                            <fa v-if="is_like(curtopic.curforum.likeslist)" icon="heart" class="main-color" fixed-width></fa>
                                                            <fa v-else :icon="['far', 'heart']" fixed-width></fa>
                                                        </button>
                                                        &nbsp;
                                                        <button class="btn-transparent btn_bookmark" @click="changebookmark(forum)">
                                                            <fa v-if="curtopic.curforum.bookmarked" icon="bookmark" class="main-color" fixed-width></fa>
                                                            <fa v-else :icon="['far', 'bookmark']" fixed-width></fa>
                                                        </button>
                                                        &nbsp;
                                                        <div v-if="auth_user && (auth_user.id == forum.user_id || isAdmin)" style="display:inline-block;" class="dropdown">
                                                            <button class="btn-transparent btn_edit dropdown-toggle" data-toggle="dropdown"  @click="editConfirmModal(forum.id,forum.mparent)"><fa icon="edit"></fa>
                                                            </button>
                                                            <ul class="dropdownEdit dropdown-menu">
                                                                <li>
                                                                    <button slot="button" class="btn_modal_edit mr-20" v-on:click="showEdit()">Edit</button>
                                                                </li>
                                                                <li>
                                                                    <button slot="button" class="btn_modal_delete"  @click="deleteTopic()">Delete</button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <button class="btn_topic_reply" data-topic-id="" @click="toggle(forum.user_id)" title="Reply to this topic">
                                                            <fa icon="reply" fixed-width></fa> Reply
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div style="clear:both"></div>
                            <div class="reply_list">
                                <div class="reply_item" :id="'topic_id_'+item.id" :ref="'topic_id_'+item.id" v-for="item in curtopic.replies" :key="item.id">
                                    <div class="original_topic" :id="'embeddedID_'+item.id" :ref="'embeddedID_'+item.id" v-if="item.sParent_user" v-show="hidden_id == item.id">
                                        <section class="embedded-posts top">
                                            <button @click="collapse()" class="widget-button btn btn collapse-down no-text btn-icon" aria-label="collapse" title="collapse">
                                                <fa icon="chevron-down" fixed-width></fa>
                                            </button>
                                            <div>
                                                <div data-post-id="" class="reply">
                                                    <div class="">
                                                        <div class="topic-avatar">
                                                            <a v-if="item.user" class="trigger-user-card main-avatar" :href="'/'+item.user.name" data-user-card="">
                                                                <img alt="" width="45" height="45" :src="serverUrl(item.sParent_user.profile_pic ? item.sParent_user.profile_pic.url : '/imgs/default_sm.png')" :title="item.sParent_user.name" class="avatar">
                                                            </a>
                                                            <a v-else class="trigger-user-card main-avatar" href="javascript:void(0);" data-user-card="">
                                                                <img alt="" width="45" height="45" src="/imgs/default_sm.png" title="Removed User" class="avatar">
                                                            </a>
                                                            <div class="poster-avatar-extra"></div>
                                                        </div>
                                                        <div class="topic-body">
                                                            <div class="topic-meta-data">
                                                                <div class="names trigger-user-card">
                                                                    <span class="first username">
                                                                        <a v-if="item.sParent_user" :href="'/'+item.sParent_user.name" :data-user-card="item.sParent_user.name">
                                                                            <img v-if="forum.user" alt="" width="45" height="45" :src="serverUrl(forum.user.profile_pic ? forum.user.profile_pic.url : '/imgs/default_sm.png')" title="" class="mo_show avatar">
                                                                            <img v-else alt="" width="45" height="45" src="/imgs/default_sm.png" title="" class="mo_show avatar">
                                                                            <span class="mo_lh45 username">{{ item.sParent_user.name }}</span>
                                                                        </a>
                                                                        <a v-else href="javascript:void(0);" data-user-card="">
                                                                            <img v-if="forum.user" alt="" width="45" height="45" :src="serverUrl(forum.user.profile_pic ? forum.user.profile_pic.url : '/imgs/default_sm.png')" title="" class="mo_show avatar">
                                                                            <img v-else alt="" width="45" height="45" src="/imgs/default_sm.png" title="" class="mo_show avatar">
                                                                            <span class="mo_lh45 username">Removed User</span>
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            <div class="post-link-arrow">
                                                            <a class="post-info arrow" @click="showselectedTopic(item.get_s_parent.id)" title="jump to earlier reply">
                                                                <fa icon="arrow-up" fixed-width></fa>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div style="clear:both;"></div>
                                                    <div class="cooked" v-html="item.get_s_parent.detail"></div>
                                                </div>
                                            </div>
                                            </div></div>
                                        </section>
                                        <div style="clear:both;"></div>
                                    </div>
                                    <div class="reply_body">
                                        <div class="topic-avatar">
                                            <a v-if="item.user" class="trigger-user-card main-avatar" :href="'/'+item.user.name" data-user-card="">
                                                <img alt="" width="45" height="45" :src="serverUrl(item.user.profile_pic ? item.user.profile_pic.url : '/imgs/default_sm.png')" title="" class="avatar">
                                            </a>
                                            <a v-else class="trigger-user-card main-avatar" href="javascript:void(0);" data-user-card="">
                                                <img alt="" width="45" height="45" src="/imgs/default_sm.png" title="" class="avatar">
                                            </a>
                                        </div>
                                        <div class="topic-body">
                                            <div class="topic-meta-data">
                                                <div class="names">
                                                    <a v-if="item.user" :href="'/'+item.user.name">
                                                        <img width="45" height="45" :src="serverUrl(item.user.profile_pic ? item.user.profile_pic.url : '/imgs/default_sm.png')" title="" class="mo_show avatar">                                                        
                                                        <span class="mo_lh45 username">
                                                        {{ item.user.name }}</span>
                                                    </a>
                                                    <a v-else href="javascript:void(0);">
                                                        <img alt="" width="45" height="45" src="/imgs/default_sm.png" title="" class="mo_show avatar">&nbsp;&nbsp;
                                                        <span class="mo_lh45 username">
                                                        Removed User</span>
                                                    </a>
                                                </div>

                                                <div class="post-infos">
                                                    <button class="mr-20 btn-trans" @click="showEmbeded(item.id)" :data-topic-id="item.id" v-if="item.sParent_user">
                                                        <img width="15" height="15" src="/imgs/reply.png" alt="" srcset="">
                                                        <img width="30" height="30" class="radius50" :src="serverUrl(item.sParent_user.profile_pic ? item.sParent_user.profile_pic.url : '/imgs/default_sm.png')" alt="">
                                                    </button>
                                                    <span  :title="item.created_at" data-time="" data-format="" class="relative-date">{{ item.created_at | moment("from",curtopic.curtime,true) }}</span>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                            <div class="topic-text">
                                                <div class="preview" v-html="item.detail"></div>
                                            </div>
                                            <div class="post-menu-area">
                                                <button class="btn_topic_reply" :data-topic-id="item.id"  @click="showSEmbeded(item.id)" v-if="item.schildren.length">{{ item.schildren.length }} Replies <fa icon="chevron-down"></fa></button>
                                                <div class="topic_action">
                                                    <ul>
                                                        <li>
                                                            <button class="btn-transparent btn_like_num" :title="item.likes+'people liked this post'" v-show="item.likes.length">{{ item.likes.length }}</button>
                                                            <button class="btn-transparent btn_like_topic" :data-topic-id="item.id" :data-liked="is_like(item.likeslist)" @click="changelike(item, 'reply')">
                                                                <fa v-if="is_like(item.likeslist)" icon="heart" class="main-color"></fa>
                                                                <fa v-else :icon="['far', 'heart']"></fa>
                                                            </button>
                                                            &nbsp;
                                                            <button class="btn-transparent" @click="changebookmark(item)">
                                                                <fa v-if="item.bookmarked" icon="bookmark" class="main-color"></fa>
                                                                <fa v-else :icon="['far', 'bookmark']"></fa>
                                                            </button>
                                                            &nbsp;
                                                            <div v-if="auth_user && (auth_user.id == item.user_id || isAdmin)" class="dropdown" style="display:inline-block;">
                                                                <button  class="btn-transparent dropdown-toggle" data-toggle="dropdown"  @click="editConfirmModal(item.id,item.mparent)">
                                                                    <fa icon="edit"></fa>
                                                                </button>
                                                                <ul class="dropdownEdit dropdown-menu">
                                                                    <li>
                                                                        <button slot="button" class="btn_modal_edit mr-20" v-on:click="showEdit()">Edit</button>
                                                                    </li>
                                                                    <li>
                                                                        <button slot="button" class="btn_modal_delete"  @click="deleteTopic()">Delete</button>
                                                                    </li>
                                                                </ul>
                                                            </div>                                                            
                                                        </li>
                                                        <li>
                                                            <button class="btn_topic_reply" :data-topic-id="item.id" @click="toggle(item.user_id)" title="Reply to this post">
                                                                <fa icon="reply" fixed-width></fa> Reply
                                                            </button>
                                                        </li>
                                                    </ul>

                                                </div>
                                            </div>
                                            <section class="embedded-posts bottom" v-show="Shidden_id == item.id"  v-if="item.schildrens.length">
                                                <button  @click="Scollapse()" class="widget-button btn btn collapse-up no-text btn-icon" aria-label="collapse" title="collapse">
                                                    <fa icon="chevron-up" fixed-width></fa>
                                                </button>

                                                <div data-post-id="" v-for="sitem in item.schildrens" :key="sitem.id" class="reply" >
                                                    <div class="topic-avatar">
                                                        <a v-if="sitem.user" class="trigger-user-card main-avatar" :href="'/'+sitem.user.name" data-user-card="">
                                                            <img alt="" width="45" height="45" :src="serverUrl(sitem.user.profile_pic ? sitem.user.profile_pic.url : '/imgs/default_sm.png')" :title="sitem.user.name" class="avatar">
                                                        </a>
                                                        <a v-else class="trigger-user-card main-avatar" href="javascript:void(0);" data-user-card="">
                                                            <img alt="" width="45" height="45" src="/imgs/default_sm.png" title="Removed User" class="avatar">
                                                        </a>
                                                        <div class="poster-avatar-extra"></div>
                                                    </div>
                                                    <div class="topic-body">
                                                        <div class="topic-meta-data">
                                                            <div class="names trigger-user-card">
                                                                <span class="first username">
                                                                    <a v-if="sitem.user" :href="'/'+sitem.user.name" :data-user-card="sitem.user.name">
                                                                        <img alt="" v-if="forum.user" width="45" height="45" :src="serverUrl(forum.user.profile_pic ? forum.user.profile_pic.url : '/imgs/default_sm.png')" title="" class="mo_show avatar">
                                                                        <img v-else alt="" width="45" height="45" src="/imgs/default_sm.png" title="" class="mo_show avatar">
                                                                        <span class="mo_lh45 username">{{ sitem.user.name }}</span>
                                                                    </a>
                                                                    <a v-else href="javascript:void(0);" data-user-card="">
                                                                        <img alt="" v-if="forum.user" width="45" height="45" :src="serverUrl(forum.user.profile_pic ? forum.user.profile_pic.url : '/imgs/default_sm.png')" title="" class="mo_show avatar">
                                                                        <img v-else alt="" width="45" height="45" src="/imgs/default_sm.png" title="" class="mo_show avatar">
                                                                        <span class="mo_lh45 username">Removed User</span>
                                                                    </a>
                                                                </span>
                                                            </div>
                                                            <div class="post-link-arrow">
                                                                <button class="post-info arrow btn-trans" @click="showselectedTopic(item.schildrens[item.schildrens.length - 1].id)" title="jump to later reply">
                                                                    <fa icon="arrow-down" fixed-width></fa>
                                                                </button>
                                                            </div>

                                                            <div style="clear:both;"></div>
                                                            <div class="cooked" v-html="sitem.detail"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
                                <div class="lastborder"></div>
                                <div v-if="noSearchResult">
                                    <p style="text-align:center;">There is no match data.</p>
                                </div>
                            </div>
                            <client-only>
                                <div class="slider_range" v-if="loaded">
                                    <div class="switch_moshow" v-show="!$device.isMobile">
                                        <vue-slider @change="dragfunction()" v-model="slidervalue" width="4px" height="200px" :min="1" :max="maxvalue" direction="ttb" tooltip='always'></vue-slider>
                                    </div>                                    
                                    <div class="topic_count">
                                        <span class="total_number" @click="toggleSlider()">{{ curtopic.replies.length+1 }}</span>
                                    </div>
                                </div>
                            </client-only>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="new_forum" id="new_forum_reply" v-show="isOpen">
            <div class="grippie"></div>
            <div class="body">
                <div class="contaier-fluid">
                    <div class="row">
                        <div class="editorsection" >
                            <div v-show="this.createEditStatus">
                                <div>
                                    <span class="fs-20" style="color:#fefefe;">
                                        <img width="15" height="15" src="/imgs/reply.png" alt="" srcset="">
                                        <img v-if="this.topicUser" width="40" height="40" class="radius50" :src="serverUrl(this.topicUser.pic ? this.topicUser.pic : 'imgs/default_sm.png')">
                                        <img v-else width="40" height="40" class="radius50" src="/imgs/default_sm.png" alt="" >
                                        <span v-if="this.topicUser">{{ this.topicUser.name }}</span>
                                        <span v-else>Removed User</span>
                                    </span>
                                </div>
                                <p v-if="errors.length">
                                    <ul>
                                        <li style="display:inline-block;color:red;font-family:Arial;" v-for="error in errors" :key="error.id">{{ error }}</li>
                                    </ul>
                                </p>
                            </div>   
                            <div class="mt-15">
                                <div class="quill-editor" 
                                    ref="myReplyQuillEditor"
                                    @focus="hideFooter()"
                                    @blur="showFooter()"
                                    v-model="replydetail"
                                    v-quill:myReplyQuillEditor="editorOption">
                                </div>
                            </div> 
                            
                            <div class="mt-15 mb-3">
                                <button class="btn_forum_topic" @click="createTopicReply()">
                                    <span v-if="this.createEditStatus"><fa icon="reply" fixed-width></fa>Reply</span>
                                    <span v-else><fa icon="edit"></fa>Edit</span>                                    
                                </button>
                                &nbsp;&nbsp;&nbsp;
                                <button class="btn_cancel" @click="alertconfirm()">Cancel</button>
                            </div>                  
                        </div>                       
                    </div>
                </div>
            </div>
        </div>
        <div class="new_forum" id="new_forum" v-show="isOpenNewTopic">
            <div class="grippie"></div>
            <div class="body">
                <div class="contaier-fluid">
                    <div class="row">
                        <div class="editorsection">
                            <div>
                                <span v-if="newcreatetopic.id" class="fs-20" style="color:#fefefe;">
                                    <fa icon="edit"></fa>&nbsp;&nbsp;Edit Topic
                                </span>
                                <span v-else class="fs-20" style="color:#fefefe;">
                                    <fa icon="arrow-right"></fa>&nbsp;&nbsp;Create a New Topic
                                </span>
                            </div>
                            <p v-if="errors.length">
                                <ul>
                                    <li style="display:inline-block;color:red;font-family:Arial;" v-for="error in errors" :key="error.id">{{ error }}</li>
                                </ul>
                            </p>
                            <div class="mt-15">
                                <input type="text" name="forumtitle" placeholder="Title" class="form-control " v-model="title">
                            </div>
                            <div class="mt-15">
                                <select name="" id="" class="form-control" v-model="category">
                                    <option value="">Select Category</option>
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
                            <div class="mt-15">
                                <div class="quill-editor"
                                    @focus="hideFooter()"
                                    @blur="showFooter()"
                                    v-model="detail"
                                    v-quill:forumQuillEditor="editorOption">
                                </div>
                            </div>
                            
                            <div class="mt-15 mb-3">
                                <button v-if="newcreatetopic.id" class="btn_forum_topic" @click="editMainTopic()">
                                    <span ><fa icon="edit"></fa>Edit Topic</span>
                                </button>
                                <button v-else class="btn_forum_topic" @click="createTopic()">
                                    <span><fa icon="plus" fixed-width></fa>Create Topic</span>
                                </button>
                                &nbsp;&nbsp;&nbsp;
                                <button class="btn_cancel" @click="alertconfirm()">Cancel</button>
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
    
    import 'quill/dist/quill.core.css'
    import 'quill/dist/quill.snow.css'
    import 'quill/dist/quill.bubble.css'

    export default {
        props: ['forum'],
        components: { },
        computed: mapGetters({
            auth_user: 'auth/user',
            curtopic: 'prefetch/topic',
        }),
        serverPrefetch() {
            return this.getCurTopic();
        },
        data() {
            return {
                isOpen: false,
                isOpenNewTopic: false,
                searchbox: false,
                selectbox: false,
                createEditStatus: true,
                topic:{},
                replydetail: "",
                title: "",
                detail: "",
                category: "",
                newtopic:{},
                newcreatetopic:{},
                seltopic:{},
                editMTopic:{},
                editTopic:{},
                errors:{},
                topicUser:{},
                editTopicInfo:{},
                bookmarkedTopic:{},
                edit_id:"",
                mparent:"",
                capsule_id: {},
                text: "",
                currentUrl:"",
                searchValue:"",
                forumid:"",
                sparent:"",
                loaded: false,
                loader: false,
                likedid:"",
                bookmarkedid:"",
                noSearchResult: false,
                hidden_id: '',
                Shidden_id: '',
                slidervalue: 1,
                maxvalue : 1,
                isAdmin: false,
                showTopics: false,
                showCategories: false,
                showBookmarks: true,
                showMobile: true,
                customToolbar: [["bold", "italic", "underline"],["blockquote", "code-block"], [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],[{ 'color': [] }, { 'background': [] }], ["link", "image", "video"]],
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
            this.noSearchResult = false;
            if(process.client) {
                this.currentUrl = window.location.pathname;
                var pos = this.currentUrl.lastIndexOf('/');
            }
        },
        mounted() {
            this.forumid = this.forum.id;
            this.topic.mparent = this.forumid;
            this.topic.sparent = "";   
            this.category = "";
            if(this.curtopic) {
                this.getCurTopic();
            }
            // let uri = '/topic/detail/'+this.forumid;
            // this.axios.get(uri).then(response => {
            //     this.curtopic = response.data;
                this.maxvalue = this.curtopic.replies.length + 1;
                this.isAdmin = this.curtopic.isAdmin;
                this.loaded = true;
                if (process.client) {
                    $(".reply_item").each(function(index){
                        var condition = $(this).offset().top - $(window).scrollTop();
                        if(condition > 180 && condition < 200)
                        {
                            app.slidervalue = index + 2;
                        }                       
                    });
                }
            // });


            this.scroll();
        },
        updated() {

        },
        methods: {
            getCurTopic() {
                return this.$store.dispatch('prefetch/fetchTopic', this.$route.params.id);
            },
            scroll() {
                var app = this;
                var total = 0;
                window.onscroll = () => {
                    $(".reply_item").each(function(index){
                        var condition = $(this).offset().top - $(window).scrollTop();
                        if(condition > 180 && condition < 300)
                        {
                            app.slidervalue = index + 2;
                        }
                    });
                    if($(".reply_item").last().offset()){                        
                        if($(".reply_item").last().offset().top - $(window).scrollTop() < 260)
                        {
                            app.slidervalue = app.maxvalue;
                        }
                    }

                    if($(window).scrollTop() < 30)
                    {
                        app.slidervalue = 1;
                    }

                };
            },
            dragfunction() {
                var position = this.slidervalue;
                if(position == 1) {
                    var scrollTop = $(".cur_forum_detail").offset().top;
                    $('html').animate({scrollTop: scrollTop-220}, 'fast');
                } else  {
                    position = position - 1;
                    if($(".reply_item:eq("+position+")").offset()){
                        var scrollTop = $(".reply_item:eq("+position+")").offset().top;
                        $('html').animate({scrollTop: scrollTop-147},'fast');
                    }                    
                }

            },
            showEdit() {
                this.createEditStatus = false;
                if(this.mparent < 1) {
                    this.isOpenNewTopic = true;
                    let uri = '/topic/getedittopic';  
                    this.editTopicInfo.id = this.edit_id;   
                            
                    this.axios.post(uri,this.editTopicInfo).then(response => {                    
                        this.newcreatetopic= response.data;  
                        this.title = response.data.title;
                        this.category = response.data.category;
                        this.detail = response.data.detail;                    
                    });
                } else {
                    this.isOpen = true;
                    let uri = '/topic/getedittopic';  
                    this.editTopicInfo.id = this.edit_id;        
                    this.axios.post(uri,this.editTopicInfo).then(response => {                    
                        this.replydetail= response.data.detail;                        
                    });
                }
            },
            deleteTopic() {
                if(window.confirm('Are you sure?')) {
                    let uri = '/topic/delete';
                    this.capsule_id.topicId = this.edit_id;
                    this.axios.post(uri,this.capsule_id).then(response => {
                        if(response.data == "0") {
                            window.location.href = '/marijuana-forums';
                        } else {
                            window.location.reload();
                        }
                    });  
                }
            },
            editConfirmModal(id,mparent) {
                this.edit_id = id;
                this.mparent = mparent;
            },
            alertconfirm()
            {
                if(window.confirm("Are you sure?")){
                    this.isOpen = false;
                    this.isOpenNewTopic = false;
                    this.replydetail = "";
                    this.errors= ""; 
                    this.title = "";
                    this.detail = "";
                    this.category = "";
                    if(this.$device.isMobile) {
                        this.showMobile = !this.showMobile;
                    }
                };
            },
            createTopicReply() {
                if(this.replydetail)
                {
                    this.loader = true;
                    let uri = '/topicreply/create';
                    this.topic.detail = this.replydetail;
                    this.topic.id = this.edit_id;
                    this.topic.createdit = this.createEditStatus;
                    this.axios.post(uri, this.topic).then((response) => {
                        this.maxvalue = response.data.replies.length + 1;
                        // this.curtopic = response.data;
                        this.isOpen = false;
                        this.topic.title = "";
                        this.topic.category = "";
                        this.topic.detail = "";  
                        this.loader = false;                                   
                        this.topic.sparent = "";  
                        this.topic.id = '';     
                        this.showMobile = true;
                        let topic_user_id = this.forum.user_id;
                        if(this.auth_user.id != topic_user_id) {                                
                            let noti_fb = firebase.database().ref('notifications/' + topic_user_id).push();
                            noti_fb.set({
                                notifier_id: this.auth_user.id,
                                type: 'reply_topic',
                            });
                        }
                        this.$store.dispatch('prefetch/fetchForum', this.$route.params.id);
                        this.$store.dispatch('prefetch/fetchTopic', this.$route.params.id);
                    });
                }
                else
                {
                    this.errors = [];
                    if(!this.replydetail)
                    {
                        this.errors.push("Detail required.");
                    }
                    return false;
                }
            },
            editMainTopic() {
                this.errors = [];
                if(this.title && this.category && this.detail)
                {
                    this.loader = true;
                    this.newcreatetopic.title = this.title;
                    this.newcreatetopic.category = this.category;
                    this.newcreatetopic.detail = this.detail;
                    let uri = '/topic/edit';
                    this.axios.post(uri, this.newcreatetopic).then((response) => {
                        this.$store.dispatch('prefetch/fetchForum', this.$route.params.id);
                        this.$store.dispatch('prefetch/fetchTopic', this.$route.params.id);
                        this.loaded = true;
                        this.isOpenNewTopic = false;
                        this.loader = false;
                    });
                } else {
                    this.errors = [];
                    if(!this.title)
                    {
                        this.errors.push("Title required.");
                    }
                    if(!this.category)
                    {
                        this.errors.push("Category required.");
                    }
                    if(!this.detail)
                    {
                        this.errors.push("Detail required.");
                    }
                    return false;
                }
            },
            createTopic() {
                this.errors = [];            
                if(this.title && this.category && this.detail) {
                    this.loader = true;
                    this.newcreatetopic.title = this.title;
                    this.newcreatetopic.category = this.category;
                    this.newcreatetopic.detail = this.detail;
                    let uri = '/topic/create';
                    this.axios.post(uri, this.newcreatetopic).then((response) => {
                        window.top.location.href = '/marijuana-forums';
                    });
                } else {
                    this.errors = [];
                    if(!this.title)
                    {
                        this.errors.push("Title required.");
                    }
                    if(!this.category)
                    {
                        this.errors.push("Category required.");
                    }
                    if(!this.detail)
                    {
                        this.errors.push("Detail required.");
                    }
                    return false;
                }
            },
            showEmbeded(id) {
                var topic_id = event.target.parentElement.dataset.topicId;
                if(this.hidden_id == id) {
                    this.hidden_id = '';
                } else {
                    this.hidden_id = id;
                }

            },
            showSEmbeded(id) {
                var topic_id = event.target.parentElement.dataset.topicId;
                if(this.Shidden_id == id) {
                    this.Shidden_id = '';
                } else {
                    this.Shidden_id = id;
                }

            },
            showChildEmbeded(id) {
                var topic_id = event.target.parentElement.dataset.topicId;
                if(this.hidden_id == id) {
                    this.hidden_id = '';
                } else {
                    this.hidden_id = id;
                }

            },
            showselectedTopic(id) {
                var ref = "topic_id_"+id;
                var scrollTop = $("#topic_id_"+id).offset().top;
                $('html').animate({scrollTop: scrollTop-147},'slow');
            },
            collapse() {
                this.hidden_id = '';
            },
            Scollapse() {
                this.Shidden_id = '';
            },
            getSearchResult() {    
                localStorage.setItem('forum_keyword', this.searchValue);
                window.location.href = '/marijuana-forums';
            },
            searchTimes()
            {
                this.searchbox = false;
            },
            newtopictoggle() {
                this.errors = [];     
                this.title = "";
                this.detail = "";
                this.category = "";       
                if(this.auth_user) {
                    this.isOpen = false;
                    if(this.$device.isMobile) {
                        this.showMobile = !this.showMobile;
                    }
                    this.createEditStatus = true;                
                    this.isOpenNewTopic = !this.isOpenNewTopic;
                    var scrollTop = $(".lastborder").offset().top;                             
                    $('html').animate({scrollTop: scrollTop-100},'fast');
                } else {
                    $("#loginmodal").modal();
                }
            },
            toggle(user_id) {
                this.topic.sparent = event.target.dataset.topicId;
                if(this.auth_user) {
                    this.isOpenNewTopic = false;
                    if(this.$device.isMobile) {
                        this.showMobile = !this.showMobile;
                    }
                    
                    this.createEditStatus = true;
                    this.topicUser.user_id = user_id;
                    let uri = '/gettopicuser/'+user_id;
                    this.axios.get(uri).then(response => {
                        this.topicUser = response.data;
                    });
                    this.isOpen = !this.isOpen;
                    this.replydetail = "";
                    var scrollTop = $(".lastborder").offset().top;                
                    $('html').animate({scrollTop: scrollTop-130},'fast');
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
            changelike(topic, type) {
                if(this.auth_user) {
                    let uri = '/topic/like';
                    this.axios.post(uri, {likedid: topic.id}).then((response) => {                    
                        let topic_user_id = this.forum.user_id;
                        if(response.data.status == 'like') {
                            if(this.auth_user.id != topic_user_id) {                                
                                let noti_fb = firebase.database().ref('notifications/' + topic_user_id).push();
                                noti_fb.set({
                                    notifier_id: this.auth_user.id,
                                    type: 'like_topic',
                                });
                            }
                        }
                        this.$store.dispatch('prefetch/fetchForum', this.$route.params.id);
                        this.$store.dispatch('prefetch/fetchTopic', this.$route.params.id);
                    });
                } else {
                    $("#loginmodal").modal();
                }
            },
            changebookmark(item) {
                if(this.auth_user) {
                    let uri = '/topic/bookmark';
                    this.axios.post(uri, {bookmarkedid: item.id}).then((response) => {
                        this.$store.dispatch('prefetch/fetchForum', this.$route.params.id);
                        this.$store.dispatch('prefetch/fetchTopic', this.$route.params.id);
                    });
                } else {
                    $("#loginmodal").modal();
                }
            },
            is_like(likelist) {
                if(!this.auth_user) return false;
                return likelist.filter(item => item.user_id == this.auth_user.id).length;
            },
            is_bookmarked() {
                return 0;
            },
            replacetitle(title)
            {
                let strtitle = title.toLowerCase();
                let returnval = "";
                for (let index = 0; index < strtitle.length; index++) {
                if(strtitle[index] == " ") {
                    returnval += "-";
                } else {
                    returnval += strtitle[index];
                }
                }
                return returnval;
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
            toggleSlider() {
                if(!this.$device.isMobile) {
                    return false;
                } else {
                    // this.isMobile = !this.isMobile;
                }
            },
            viewCategoryForums(item) {
                localStorage.setItem('forum_category', item);
                window.location.href = '/marijuana-forums';
            },
            hideFooter() {
                if(this.$device.isMobile) $("#app").addClass('focus_comment');
                
            },
            showFooter() {
                if(this.$device.isMobile) $("#app").removeClass('focus_comment');
            },
            goForumHome() {
                window.location.href = "/marijuana-forums";
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
<style lang="scss" scoped>
    .forum_header h2 {
        color: #EFA720;
        font-size: 2.5rem;
        cursor: pointer;
        @media (max-width: 600px) {
            font-size: 20px;
            margin-right: 40px;
        }
    }
    .cur_forum {
        h1 {
            font-size: 1.75rem;
        }
    }
</style>
<style lang="scss">
    #emoji-palette {
        top: 0 !important;
        left: 0 !important;
    }
</style>