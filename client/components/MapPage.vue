<template>
    <div class="map-container row">
        <div class="search_bar col-md-12">
            <div class="mobile">
                <h4 class="text-420 results_count" @click="showList()" v-if="!loading && !show_map">{{results_count}} Results</h4>
                <h4 class="text-420 results_count" @click="showMap()" v-if="!loading && show_map && !show_list">{{results_count}} Results</h4>
                <img src="/imgs/maps-icon.png" alt="" @click="showMap()" v-if="!show_map">
                <img src="/imgs/list.png" alt="" @click="showList()" v-if="show_map && !show_list">
                <img src="/imgs/search_option.png" alt="" @click="tooglesearch">
            </div>
        </div>
        <div class="col-12 filter_mobile pt-2" id="filter_mobile" v-if="$device.isMobile">
            <div class="row">
                <div class="col-6 pt-3">
                    <div class="custom-control custom-checkbox filter-input" style="padding-top : 3px;">
                        <input type="checkbox" class="custom-control-input" id="mobile_filter_storefront" value="1" v-model="top_filter.business_type" >
                        <label class="custom-control-label" for="mobile_filter_storefront">Storefront</label>
                    </div>
                    <div class="custom-control custom-checkbox filter-input" style="padding-top : 3px;">
                        <input type="checkbox" class="custom-control-input" id="mobile_filter_delivery" value="2" v-model="top_filter.business_type" />
                        <label class="custom-control-label" for="mobile_filter_delivery">Delivery</label>
                    </div>
                    <div class="custom-control custom-checkbox filter-input" style="padding-top : 3px;">
                        <input type="checkbox" class="custom-control-input" id="mobile_filter_recreational" value="recreational" v-model="top_filter.filters">
                        <label class="custom-control-label" for="mobile_filter_recreational">Recreational</label>
                    </div>
                    <div class="custom-control custom-checkbox filter-input" style="padding-top : 3px;">
                        <input type="checkbox" class="custom-control-input" id="mobile_filter_medical" value="medical" v-model="top_filter.filters">
                        <label class="custom-control-label" for="mobile_filter_medical">Medical</label>
                    </div>
                    <div class="custom-control custom-checkbox filter-input" style="padding-top : 3px;">
                        <input type="checkbox" class="custom-control-input" id="mobile_filter_open_now" value="open_now" v-model="top_filter.filters">
                        <label class="custom-control-label" for="mobile_filter_open_now">Open Now</label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="loader text-center mt-4" v-if="loading">
                        <h6 class="text-center">Wait for it...</h6>
                        <img src="/imgs/default_sm.png" width="35" alt=" ">
                    </div>

                    <div class="form-group floating-label floating-multiselect mobile_filter_portal" :class="{ selected : top_filter.portal }">
                        <multiselect
                            class="filter_portal"
                            id="mobile_filter_portal"
                            :options="search_portals"
                            label="name"
                            track-by="id"
                            placeholder=" "
                            :show-labels="false"
                            @select="selectPortal"
                            @open="searchPortalOpen()"
                            @close="searchPortalClose()"
                        >
                            <span slot="noResult">No Results</span>
                        </multiselect>
                        <label for="mobile_filter_portal">Search Company</label>
                    </div>
                </div>
            </div>
            <hr class="my-3" style="background-color : #EFA720; height: 3px;">
            <div class="row">
                <div class="col-6">
                    <div class="custom-control custom-radio filter-input" style="padding-top : 3px;" v-for="(category, c_index) in categories" :key="c_index">
                        <input type="radio" class="custom-control-input" name="category" :id="'mobile_menu_flowers_' + category.slug" :value="category.id" v-model="top_filter.category" @change="selectCategory" />
                        <label class="custom-control-label" :for="'mobile_menu_flowers_' + category.slug">{{ category.name | category_name }}</label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="pt-5">
                        <div class="form-group floating-label">
                            <input type="text" class="form-control floating-input" id="mobile_search_products" placeholder=" " :value="top_filter.menu_strain" @input="e => top_filter.menu_strain = e.target.value" @focus="searchPortalOpen()" @blur="searchPortalClose()">
                            <label for="mobile_search_products" class="product_label">Search Brands &amp; Products</label>
                        </div>
                        <div class="form-group floating-label" v-if="top_filter.selected_category">
                            <select class="form-control floating-select mt-2" id="mobile_weight_amount" v-model="top_filter.menu_price_type">
                                <option value="price_each">Each</option>
                                <option value="price_half_gram" v-show="top_filter.selected_category.price_type == 1">0.5g</option>
                                <option value="price_gram" v-show="top_filter.selected_category.price_type > 0">g</option>
                                <option value="price_eighth" v-show="top_filter.selected_category.price_type == 2">1/8</option>
                                <option value="price_quarter" v-show="top_filter.selected_category.price_type == 2">1/4</option>
                                <option value="price_half_oz" v-show="top_filter.selected_category.price_type == 2">1/2</option>
                                <option value="price_oz" v-show="top_filter.selected_category.price_type == 2">oz</option>
                            </select>
                            <label for="mobile_weight_amount" :class="{focused : top_filter.menu_price_type}">Weight Amount</label>
                        </div>
                        <div class="form-group floating-label" v-if="top_filter.selected_category && top_filter.menu_price_type">
                            <input type="number" min="0"
                                class="form-control floating-input mt-2"
                                id="mobile_max_price"
                                placeholder=" "
                                v-model="top_filter.menu_price_max"
                            />
                            <label for="mobile_max_price">Max $</label>
                        </div>

                        <h5 class="mt-3"><a href="javascript:;" class="text-420" @click="resetCategory()">Reset Options</a></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="portal_filter" v-else>
            <div class="row">
                <div class="col-12 d-flex justify-content-center align-items-center">
                    <img src="/imgs/h_icon1.png" width="35" height="35" style="margin-top: -8px;margin-right:7px" alt="Marijuana Dispensaries and Deliveries" />
                    <h1 @click="openTitleModal()">{{page_title}}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="filter-dropdown">
                        <img src="/imgs/search_option.png" class="d-block mx-auto" width="30" @mouseover="open_menus = !open_menus">
                        <div class="filter-container">
                            <div class="filter-dropdown-menu" v-show="open_menus" style="width: 400px;" id="panel_menus" @mouseleave="open_menus = false">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a class="filter-dropdown-item" href="javascript:;">
                                            <div class="custom-control custom-checkbox filter-input">
                                                <input type="checkbox" class="custom-control-input" id="filter_storefront" value="1" v-model="top_filter.business_type" >
                                                <label class="custom-control-label" for="filter_storefront">Storefront</label>
                                            </div>
                                        </a>
                                        <a class="filter-dropdown-item" href="javascript:;">
                                            <div class="custom-control custom-checkbox filter-input">
                                                <input type="checkbox" class="custom-control-input" id="filter_delivery" value="2" v-model="top_filter.business_type" />
                                                <label class="custom-control-label" for="filter_delivery">Delivery</label>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <a class="filter-dropdown-item pl-0" href="javascript:;">
                                            <div class="custom-control custom-checkbox filter-input">
                                                <input type="checkbox" class="custom-control-input" id="filter_recreational" value="recreational" v-model="top_filter.filters">
                                                <label class="custom-control-label" for="filter_recreational">Recreational</label>
                                            </div>
                                        </a>
                                        <a class="filter-dropdown-item pl-0" href="javascript:;">
                                            <div class="custom-control custom-checkbox filter-input">
                                                <input type="checkbox" class="custom-control-input" id="filter_medical" value="medical" v-model="top_filter.filters">
                                                <label class="custom-control-label" for="filter_medical">Medical</label>
                                            </div>
                                        </a>
                                        <a class="filter-dropdown-item pl-0" href="javascript:;">
                                            <div class="custom-control custom-checkbox filter-input">
                                                <input type="checkbox" class="custom-control-input" id="filter_open_now" value="open_now" v-model="top_filter.filters">
                                                <label class="custom-control-label" for="filter_open_now">Open Now</label>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <!-- ************* -->
                                <hr style="background-color: #EFA720; height: 3px; margin: 15px 12px 8px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a class="filter-dropdown-item" href="javascript:;" v-for="(category, c_index) in categories" :key="c_index">
                                            <div class="custom-control custom-radio filter-input">
                                                <input type="radio" class="custom-control-input" name="category" :id="'menu_flowers_' + category.slug" :value="category.id" v-model="top_filter.category" @change="selectCategory" >
                                                <label class="custom-control-label" :for="'menu_flowers_' + category.slug">{{ category.name | category_name }}</label>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-6 pl-md-0 pr-md-3" id="panel_menu_search">
                                        <div class="pt-md-5" style="width:170px;">
                                            <div class="form-group floating-label">
                                                <input type="text" class="form-control floating-input" placeholder=" " id="filter_product" v-model="top_filter.menu_strain" autocomplete="off" />
                                                <label for="" style="font-size:14.4px">Search Brands &amp; Products</label>
                                            </div>
                                            <div class="form-group floating-label" v-if="top_filter.selected_category">
                                                <select class="form-control floating-select mt-2" id="filter_weight" v-model="top_filter.menu_price_type" @change="selectPriceType">
                                                    <option value="price_each">Each</option>
                                                    <option value="price_half_gram" v-show="top_filter.selected_category.price_type == 1">0.5g</option>
                                                    <option value="price_gram" v-show="top_filter.selected_category.price_type > 0">g</option>
                                                    <option value="price_eighth" v-show="top_filter.selected_category.price_type == 2">1/8</option>
                                                    <option value="price_quarter" v-show="top_filter.selected_category.price_type == 2">1/4</option>
                                                    <option value="price_half_oz" v-show="top_filter.selected_category.price_type == 2">1/2</option>
                                                    <option value="price_oz" v-show="top_filter.selected_category.price_type == 2">oz</option>
                                                </select>
                                                <label for="">Weight Amount</label>
                                            </div>

                                            <div class="form-group floating-label" v-if="top_filter.selected_category">
                                                <input type="number" min="0"
                                                    class="form-control floating-input mt-2"
                                                    placeholder=" "
                                                    v-model="top_filter.menu_price_max"
                                                />
                                                <label for="">Max $</label>
                                            </div>
                                            <h5 class="mt-3"><a href="javascript:;" class="text-420" @click="resetCategory()">Reset Options</a></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-8 mb-0 float-right offset-md-4 form-group floating-label" style="cursor:pointer;">
                            <multiselect
                                v-model="top_filter.portal"
                                class="floating-label filter_portal"
                                id="filter_portal"
                                :options="search_portals"
                                label="name"
                                track-by="id"
                                placeholder=" "
                                :show-labels="false"
                                @select="selectPortal"
                            >
                                <span slot="noResult">No Results</span>
                            </multiselect>
                            <label for="">Search Company</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 map_list" :style="{height: is_ios ? 'calc(100vh - 272px)' : 'calc(100vh - 214px)'}">
            <div class="row">
                <div class="w-100 border border" style="border-color: grey !important;margin: 1px;" v-if="!$device.isMobile">
                    <h5 class="text-center text-420 my-1">{{results_count}} Results</h5>
                </div>
                <div class="w-100 portal-container" v-for="(item, index) of portals" :key="index" v-if="filter == null || filter == index">
                    <div class="pt_list" v-if="item.menus.length == 0" @click="goPortal(item.username)" @mouseover="hover_portal = item.id" @mouseleave="hover_portal = null">
                        <div class="pt_data">
                            <div class="pt_logo">
                                <img v-bind:src="serverUrl(item.profile_pic ? item.profile_pic.url : default_logo)" alt/>
                            </div>
                            <div class="pt_details">
                                <p class="pt_name">{{item.name}}</p>
                                <div v-if="item.store_type == 2">
                                    <p class="pt_address" >Delivery</p>
                                    <p class="pt_address" >{{item.city}}, {{item.state}}, {{item.postal}}</p>
                                </div>

                                <div v-else>
                                    <p class="pt_address" >{{item.address}}</p>
                                    <p class="pt_address" >{{item.city}}, {{item.state}}, {{item.postal}}</p>
                                </div>

                                <p class="distance">
                                    <span class="shop_status" style="color: #93e612;" v-if="item.shop_status == 2">Open</span>
                                    <span class="shop_status" style="color: red" v-else>Closed</span>
                                    <span class="ml-3">{{item.distance}} Miles</span>
                                </p>
                            </div>
                            <div class="pt_type">
                                <img src="/imgs/dispensary.png" alt v-if="item.store_type == 1 || item.store_type == 3"/>
                                <img src="/imgs/delivery.png" alt v-if="item.store_type == 2 || item.store_type == 3"/>
                            </div>
                        </div>
                    </div>

                    <div class="result-container" v-for="(m_item, m_index) of item.menus" :key="m_index" v-if="item.menus.length != 0" @click="goPortal(item.username)" @mouseover="hover_portal = item.id" @mouseleave="hover_portal = null">
                        <div class="result-media">
                            <div class="media" v-if="m_item.media">
                                <img v-if="m_item.media.type == 'image'" :src="serverUrl(m_item.media.url)" alt="" />
                                <video v-if="m_item.media.type == 'video'" :src="serverUrl(m_item.media.url)" disablepictureinpicture controlslist="nodownload">
                                    <source v-bind:src="serverUrl(m_item.media.url)" type="video/mp4" />
                                    <source v-bind:src="serverUrl(m_item.media.url)" type="video/webm" />
                                    <source v-bind:src="serverUrl(m_item.media.url)" type="video/ogg" />Your browser does not support the video tag.
                                </video>
                                <img class="video__tag__mobile" v-if="m_item.media.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                            </div>
                            <div class="media" v-else-if="item.profile_pic">
                                <img v-bind:src="serverUrl(item.profile_pic ? item.profile_pic.url : default_logo)" alt/>
                            </div>
                            <div class="media" v-else>
                                <img src="/imgs/default.png" alt="" />
                            </div>
                        </div>
                        <div class="result-data">
                            <p class="category-strain">
                                <span class="category">{{m_item.category.price_type == 2 ? 'Flower | ' + m_item.category.name : m_item.category.name}}</span>
                                <span class="strain" v-if="m_item.strain"> | {{m_item.strain.name}}</span>
                            </p>
                            <h4 class="title">{{m_item.item_name}}</h4>
                            <h5 class="company-name my-0">
                                <img class="store-type-img" src="/imgs/dispensary.png" alt v-if="item.store_type == 1 || item.store_type == 3" />
                                <img class="store-type-img" src="/imgs/delivery.png" alt v-if="item.store_type == 2 || item.store_type == 3" />
                                <span class="company_name">{{item.name}}</span>
                            </h5>
                            <div class="company-status">
                                <span class="shop_status open" v-if="item.shop_status == 2">Open</span>
                                <span class="shop_status closed" v-else>Closed</span>
                                <span class="distance">{{item.distance}} Miles</span>
                            </div>
                            <div class="price_data mb-0 mt-1">
                                <span class="text-white pr-2" v-if="m_item.price_each != null">{{m_item.price_each}}<sup class="text-420">each</sup></span>
                                <span class="text-white pr-2" v-if="m_item.price_half_gram != null">{{m_item.price_half_gram}}<sup class="text-420">1/2g</sup></span>
                                <span class="text-white pr-2" v-if="m_item.price_gram != null">{{m_item.price_gram}}<sup class="text-420">g</sup></span>
                                <span class="text-white pr-2" v-if="m_item.price_eighth != null">{{m_item.price_eighth}}<sup class="text-420">1/8</sup></span>
                                <span class="text-white pr-2" v-if="m_item.price_quarter != null">{{m_item.price_quarter}}<sup class="text-420">1/4</sup></span>
                                <span class="text-white pr-2" v-if="m_item.price_half_oz != null">{{m_item.price_half_oz}}<sup class="text-420">1/2</sup></span>
                                <span class="text-white pr-2" v-if="m_item.price_oz != null">{{m_item.price_oz}}<sup class="text-420">oz</sup></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 loader text-center py-4" v-show="loading">
                    <div class="spinner-border text-warning"></div>
                </div>
            </div>
        </div>
        <div class="col-md-9 map-wraper" :style="{height: is_ios ? 'calc(100vh - 272px)' : 'calc(100vh - 214px)'}">
            <div class="row">
                <GmapMap
                    ref="mapRef"
                    :options="{
                           mapTypeControl: false,
                           streetViewControl: false,
                         }"
                    :center="current_position"
                    :zoom="12"
                    map-type-id="roadmap"
                    style="width: 100%; height: 100%"
                    @bounds_changed="boundsChangedMap($event)"
                >
                    <GmapMarker :position="current_position" :icon="mylocationicon"/>

                    <GmapMarker
                        :key="index"
                        v-for="(m, index) in portals"
                        :position="{lat: Number(m.latitude), lng: Number(m.longitude)}"
                        :clickable="true"
                        :animation="m.id == hover_portal ? 1 : 0"
                        @click="openWindow({lat: Number(m.latitude), lng: Number(m.longitude)}, index)"
                        :icon="markerOptions[m.store_type-1]"
                        @mouseover="filterlist(index)"
                        @mouseout="filterlist(null)"
                    />
                </GmapMap>

                <div class="portal_add">
                    <a href="/portals/add">
                        <img src="/imgs/add_portal.png" alt/>
                        <p>Add</p>
                        <p>Dispensary/Delivery</p>
                    </a>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mapinfowindow">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="border: none;margin: auto" role="document">
                <div class="modal-content">
                    <div class="modal-header" :style="{'background': details && details.menus.length > 0 ? '#000' : 'transparent'}">
                        <button type="button" class="close text-420" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body p-0" v-if="details">
                        <div class="pt_list" v-if="details && details.menus.length == 0" @click="goPortal(details.username)">
                            <div class="pt_data">
                                <div class="pt_logo">
                                    <img :src="details.profile_pic ? serverUrl(details.profile_pic.url) : default_logo">
                                </div>
                                <div class="pt_details" style="width: calc(100% - 60px);">
                                    <p class="pt_name" style="font-size: 18px;">{{ details ? details.name : ""}}</p>
                                    <p class="pt_address" v-if="details.store_type == 2">Delivery</p>
                                    <p class="pt_address" v-else>{{ details ? details.address : ""}}</p>
                                    <p class="pt_address" >{{details ? details.city : ""}}, {{details ? details.state : ""}}, {{details ? details.postal : ""}}</p>
                                    <p class="distance">
                                        <span class="shop_status" style="color: #93e612;" v-if="details && details.shop_status == 2">Open</span>
                                        <span class="shop_status" style="color: red" v-else>Closed</span>
                                        <span class="ml-3">{{ details ? details.distance : ""}} Miles</span>
                                    </p>
                                </div>
                                <div class="pt_type">
                                    <img src="/imgs/delivery.png" alt v-if="(details.store_type == 2 || details.store_type == 3)"/>
                                    <img src="/imgs/dispensary.png" alt v-if="(details.store_type == 1 || details.store_type == 3)"/>
                                </div>
                            </div>
                            <div v-if="details.menus.length">
                                <div class="pt_menu w-100" v-for="(m_item, m_index) of details.menus" :key="m_index">
                                    <h6 class="text-420 mb-1 pl-2 strain_title">{{m_item.strain_title}}</h6>
                                    <p class="price_data mb-0">
                                        <span class="text-white px-2" v-if="m_item.price_each != null">{{m_item.price_each}}<sup class="text-420">each</sup></span>
                                        <span class="text-white px-2" v-if="m_item.price_half_gram != null">{{m_item.price_half_gram}}<sup class="text-420">1/2g</sup></span>
                                        <span class="text-white px-2" v-if="m_item.price_gram != null">{{m_item.price_gram}}<sup class="text-420">g</sup></span>
                                        <span class="text-white px-2" v-if="m_item.price_eighth != null">{{m_item.price_eighth}}<sup class="text-420">1/8</sup></span>
                                        <span class="text-white px-2" v-if="m_item.price_quarter != null">{{m_item.price_quarter}}<sup class="text-420">1/4</sup></span>
                                        <span class="text-white px-2" v-if="m_item.price_half_oz != null">{{m_item.price_half_oz}}<sup class="text-420">1/2</sup></span>
                                        <span class="text-white px-2" v-if="m_item.price_oz != null">{{m_item.price_oz}}<sup class="text-420">oz</sup></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="result-container" v-for="(m_item, m_index) of details.menus" :key="m_index" v-if="details.menus.length != 0" @click="goPortal(details.username)">
                            <div class="result-media">
                                <div class="media" v-if="m_item.media">
                                    <img v-if="m_item.media.type == 'image'" :src="serverUrl(m_item.media.url)" alt="" />
                                    <video v-if="m_item.media.type == 'video'" :src="serverUrl(m_item.media.url)" controls disablepictureinpicture controlslist="nodownload">
                                        <source v-bind:src="serverUrl(m_item.media.url)" type="video/mp4" />
                                        <source v-bind:src="serverUrl(m_item.media.url)" type="video/webm" />
                                        <source v-bind:src="serverUrl(m_item.media.url)" type="video/ogg" />Your browser does not support the video tag.
                                    </video>
                                    <img class="video__tag__mobile" v-if="m_item.media.type==='video'" src="https://i.imgur.com/88aBgwi.png" alt="">
                                </div>
                                <div class="media" v-else-if="details.profile_pic">
                                    <img v-bind:src="serverUrl(details.profile_pic ? details.profile_pic.url : default_logo)" alt/>
                                </div>
                                <div class="media" v-else>
                                    <img src="/imgs/default.png" alt="" />
                                </div>
                            </div>
                            <div class="result-data">
                                <p class="category-strain my-0">
                                    <span class="category">{{m_item.category.price_type == 2 ? 'Flower | ' + m_item.category.name : m_item.category.name}}</span>
                                    <span class="strain" v-if="m_item.strain"> | {{m_item.strain.name}}</span>
                                </p>
                                <h4 class="title">{{m_item.item_name}}</h4>
                                <h5 class="company-name">
                                    <img class="store-type-img" src="/imgs/dispensary.png" alt v-if="details.store_type == 1 || details.store_type == 3" />
                                    <img class="store-type-img" src="/imgs/delivery.png" alt v-if="details.store_type == 2 || details.store_type == 3" />
                                    <span class="company_name">{{details.name}}</span>
                                </h5>
                                <div class="company-status">
                                    <span class="shop_status open" v-if="details.shop_status == 2">Open</span>
                                    <span class="shop_status closed" v-else>Closed</span>
                                    <span class="distance">{{details.distance}} Miles</span>
                                </div>
                                <div class="price_data mb-0 mt-1">
                                    <span class="text-white pr-2" v-if="m_item.price_each != null">{{m_item.price_each}}<sup class="text-420">each</sup></span>
                                    <span class="text-white pr-2" v-if="m_item.price_half_gram != null">{{m_item.price_half_gram}}<sup class="text-420">1/2g</sup></span>
                                    <span class="text-white pr-2" v-if="m_item.price_gram != null">{{m_item.price_gram}}<sup class="text-420">g</sup></span>
                                    <span class="text-white pr-2" v-if="m_item.price_eighth != null">{{m_item.price_eighth}}<sup class="text-420">1/8</sup></span>
                                    <span class="text-white pr-2" v-if="m_item.price_quarter != null">{{m_item.price_quarter}}<sup class="text-420">1/4</sup></span>
                                    <span class="text-white pr-2" v-if="m_item.price_half_oz != null">{{m_item.price_half_oz}}<sup class="text-420">1/2</sup></span>
                                    <span class="text-white pr-2" v-if="m_item.price_oz != null">{{m_item.price_oz}}<sup class="text-420">oz</sup></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="titleModal" v-if="!map_location.state && !map_location.city">
            <div class="modal-dialog modal-lg border-0">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <button type="button" class="close text-420" data-dismiss="modal"><fa icon="times" fixed-width></fa></button>
                    </div>
                    <div class="modal-body" style="background:white !important;">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <img src="/imgs/dispensary.png" alt=""><img src="/imgs/delivery.png" alt="">
                                <h2 class="pt-2">Search Marijuana Dispensaries and Marijuana Deliveries Near You</h2>
                                <edit-description class="strain__modal__content" type="map-modal" :strain="modal_data" :auth="auth_user"></edit-description>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="titleModal" v-else>
            <div class="modal-dialog modal-lg border-0">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <button type="button" class="close text-420" data-dismiss="modal"><fa icon="times" fixed-width></fa></button>
                    </div>
                    <div class="modal-body" style="background:white !important;">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <img src="/imgs/dispensary.png" alt="">
                                <img src="/imgs/delivery.png" alt="">
                                <p class="pt-3">Our map displays both <span style="font-size: 18px">marijuana dispensaries and deliveries in <font style="text-transform: capitalize">{{city}}, {{state}}</font> </span>. Find your local cannabis dispensary and delivery nearest you. We display both recreational and medical marijuana dispensaries and deliveries in <font style="text-transform: capitalize">{{city}}, {{state}}</font>. So if you're looking for weed products, you have come to the right place!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from "vue-multiselect";
    import _ from "lodash";
    import EditDescription from '~/components/strain/EditDescription'
    import { mapGetters } from "vuex";
    export default {
        name: "MapPage",
        props: ['page_title'],
        data() {
            return {
                loading: false,
                default_logo : '/imgs/default.png',
                showmap: false,
                portals: [],
                info_marker: null,
                infowindow: {lat: 10, lng: 10.0},
                window_open: false,
                markerOptions: [
                    {
                        url: "/imgs/dispensary.png",
                        size: {width: 30, height: 30, f: "px", b: "px"},
                        scaledSize: {width: 30, height: 30, f: "px", b: "px"}
                    },
                    {
                        url: "/imgs/delivery.png",
                        size: {width: 30, height: 30, f: "px", b: "px"},
                        scaledSize: {width: 30, height: 30, f: "px", b: "px"}
                    },
                    {
                        url: "/imgs/dispensary.png",
                        size: {width: 30, height: 30, f: "px", b: "px"},
                        scaledSize: {width: 30, height: 30, f: "px", b: "px"}
                    }
                ],
                mylocationicon: {
                    url: "/imgs/blue-dot.png",
                    size: {width: 30, height: 30, f: "px", b: "px"},
                    scaledSize: {width: 30, height: 30, f: "px", b: "px"}
                },
                current_position: {lat: 0, lng: 0},
                details: null,
                filter : null,

                // Customized Filter

                open_business_type : false,
                open_filters : false,
                open_menus : false,
                categories : [],
                top_filter : {
                    portal : null,
                    category : null,
                    selected_category : null,
                    business_type : [1, 2],
                    filters : [],
                    menu_strain : '',
                    menu_price_type : '',
                    menu_price_max : null,
                },
                hover_portal : null,
                show_map: true,
                show_list: false,
                show_filter: false,
                search_count: 0,
                results_count: 0,
                state: "",
                city: "",
                bounds: null,
                is_ios: false,
                search_portals: [],
            };
        },
        serverPrefetch () {
            return this.getModalData()
        },
        computed: mapGetters({
            auth_user: 'auth/user',
            map_location: 'auth/map_location',
            modal_data: 'map/modal_data',
        }),
        components: {
            Multiselect,
            EditDescription
        },
        filters : {
            category_name : function(name) {
                let flowers = ['Indica', 'Sativa', 'Hybrid'];
                let filtered_name = '';
                if(flowers.indexOf(name) !== -1) {
                    filtered_name = 'Flowers - ' + name;
                } else {
                    filtered_name = name;
                }
                return filtered_name;
            }
        },
        watch : {
            top_filter : {
                handler : function(new_value, old_value) {
                    this.search_filter(new_value);
                },
                deep : true,
            }
        },
        created() {
            if(process.client) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(this.showPosition);
                } else {
                    alert(
                        "Geolocation is not supported by this browser. \n Please enable it."
                    );
                }
            }
        },
        mounted() {
            if(this.$route.params.state) {
                this.state = this.tocapitalize(this.$route.params.state)
            }
            if(this.$route.params.city) {
                this.city = this.tocapitalize(this.$route.params.city)
            }
            this.init();
            var self = this;
            $(document).on('mouseleave', '.filter-dropdown-toggle', function(){
                if(!$(this).siblings('.filter-dropdown-menu').is(':hover')){
                    self.open_business_type = false;
                    self.open_menus = false;
                    self.open_filters = false;
                }
            });
            if (this.$device.isMobile && navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0)  {
                this.is_ios = true;
            }
        },
        methods: {
            getSearchPortals() {
                this.axios.get('/portals').then(response => {
                    this.search_portals = response.data;
                });
            },
            getModalData(){
                return this.$store.dispatch('map/getMapModalData');
            },
            tocapitalize(string) {
                let text = string.replace("-", " ")
                return text.charAt(0).toUpperCase() + text.slice(1)
            },
            init() {
                this.axios.post('/categories').then(response => {
                    this.categories = response.data;
                });
                this.getSearchPortals();
            },
            openWindow(position, index) {
                this.details = this.portals[index];
                if (this.$device.isMobile) {
                    $("#mapinfowindow").modal('show');
                } else {
                    window.location.href = "/" + this.details.username;
                }
            },
            closewindow() {
                this.window_open = false
            },
            showPosition(position) {
                this.current_position.lat = position.coords.latitude;
                this.current_position.lng = position.coords.longitude;
                // this.getportals();
            },
            getportals: _.debounce(function() {
                    let uri = "/portals/getall";

                    let params = {
                        lat: this.current_position.lat,
                        lng: this.current_position.lng,
                        business_type : this.top_filter.business_type,
                        filters : this.top_filter.filters,
                        portal_id : this.top_filter.portal ? this.top_filter.portal.id : null,
                        category : this.top_filter.category,
                        menu_strain : this.top_filter.menu_strain,
                        menu_price_type : this.top_filter.menu_price_type,
                        menu_price_max : this.top_filter.menu_price_max,
                        bounds: this.bounds,
                    };
                    this.loading = true;
                    this.axios.post(uri, params).then(response => {
                        this.search_count++;
                        this.portals = this.insetStrainData(response.data.portals, params);
                        this.results_count = this.portals.length;
                        // this.portals.map(portal => {
                        //     this.results_count += portal.menus.length;
                        // });
                        this.portals.sort(function (a, b) {
                            return a.distance - b.distance;
                        });
                        if (this.portals.length > 0) {
                            this.details = this.portals[0];
                        }
                        this.loading = false;
                    });
            }, 1000),
            tooglemap() {
                $('.map-container .filter_mobile').hide();
                this.show_mobile = false;
                if($('.map-container .map_list').is(':visible')) {
                    $('.map-container .map_list').hide();
                    $('.map-container .map-wraper').show();
                    this.show_list = false;
                    this.show_map = true;
                } else {
                    $('.map-container .map_list').show();
                    $('.map-container .map-wraper').hide();
                    this.show_list = true;
                    this.show_map = false;
                }
            },
            tooglesearch(){
                $('.map-container .map_list').hide();
                if($('.map-container .filter_mobile').is(':visible')) {
                    $('.map-container .filter_mobile').hide();
                    $('.map-container .map-wraper').show();
                } else {
                    $('.map-container .filter_mobile').show();
                    $('.map-container .map-wraper').hide();
                }
            },
            filterlist(index){
                this.filter = index;
            },
            selectPortal(selected_portal, id) {
                // this.top_filter.portal = selected_portal.id;
                $(".filter_portal").siblings('label').addClass('focused');
                window.location.href = "/" + selected_portal.username;
            },
            search_filter(filter_data) {
                this.getportals();
                if(this.top_filter.menu_price_type != ""){
                    $("#filter_weight").siblings('label').addClass('focused');
                } else {
                    $("#filter_weight").siblings('label').removeClass('focused');
                }
            },
            resetCategory() {
                this.top_filter.category = null;
                this.top_filter.selected_category = null;
                this.top_filter.menu_strain = '';
                this.top_filter.menu_price_type = '';
                this.top_filter.menu_price_max = '';
                this.top_filter.filters = [];
            },
            insetStrainData(data, params) {
                data.forEach(portal => {
                    portal.menus.forEach(portal_menu => {
                        const strain_name = portal_menu.strain ? portal_menu.strain.name : portal_menu.item_name;
                        portal_menu.strain_title = `${strain_name} [ ${portal_menu.category.name} ]`;
                    });
                });

                return data;
            },
            goPortal(username) {
                window.location.href = username;
            },
            showList() {
                $('.map-container .filter_mobile').hide();
                $('.map-container .map_list').show();
                $('.map-container .map-wraper').hide();
                this.show_filter = false;
                this.show_list = true;
                this.show_map = false;
            },
            showMap(){
                $('.map-container .filter_mobile').hide();
                $('.map-container .map_list').hide();
                $('.map-container .map-wraper').show();
                this.show_filter = false;
                this.show_list = false;
                this.show_map = true;
            },
            selectCategory() {
                if(this.top_filter.category) {
                    this.top_filter.selected_category = this.categories.filter(cat => cat.id == this.top_filter.category)[0];
                } else {
                    this.top_filter.selected_category = null;
                }
                let category = this.top_filter.selected_category;
                if (category && category.price_type == 0) {
                    this.top_filter.menu_price_type = 'price_each';
                } else {
                    this.top_filter.menu_price_type = '';
                }
            },
            selectPriceType(){

            },
            searchPortalOpen(){
                $("#app").addClass('focus_comment');
            },
            searchPortalClose(){
                $("#app").removeClass('focus_comment');
            },
            openTitleModal() {
                $("#titleModal").modal();
            },
            serverUrl(item) {
                if(item.charAt(0) != '/'){item = '/' + item;}
                try {
                    return process.env.serverUrl + item;
                } catch (error) {
                    return process.env.serverUrl + 'imgs/default.png';
                }
            },
            boundsChangedMap: _.debounce(function(e) {
                this.bounds = e;
                this.getportals();
            }, 1000),
        },
    };
</script>
<style lang="scss" scoped>
    #portal_filter {
        padding-top: 20px;
        padding-bottom: 8px;
        h1 {
            color: #EFA720;
            text-align: center;
            cursor: pointer;
            font-size: 2.25rem;
        }
        .filter-dropdown {
            position: relative;
            margin-top: 6px;
            .filter-container {
                position: absolute;
                top: 37px;
                z-index: 1;
                width: 100%;
            }
            &-menu {
                background-color: #000;
                border: solid 1px white;
                color: #EFA720;
                width: 100%;
                border-radius: 0;
                padding-top: 10px;
                padding-bottom: 10px;
                margin: 0 auto;
            }
            &-item {
                display: block;
                color: #d2d1d0;
                padding-top: 3px;
                padding-left: 12px;
                padding-left: 12px;
                &:hover, &:focus {
                    color: #EFA720;
                    background-color: unset;
                    cursor: pointer;
                }
                label {
                    cursor: pointer !important;
                }
            }
            &-toggle {
                text-align: left;
                border-radius: 0;
            }
            &-toggle::after {
                display: block;
                float: right;
                margin-top: 10px;
            }
            .btn-primary:hover,
            .btn-primary:focus {
                color: black;
                background-color: #EFA720;
                box-shadow: unset;
                border-color: #EFA720;
            }
        }
    }
    .form-control:focus {
        box-shadow: none;
    }
    @media only screen and (max-width: 600px) {
        #panel_menus {
            width: 100% !important;
            #panel_menu_search {
                padding-left: 28px;
                padding-top: 15px;
            }
        }
        #portal_filter {
            display: none;
        }
        .multiselect__tags {
            min-height: 40px !important;
        }
    }
    .filter_mobile {
        display: none;
        color: #d2d1d0;
        padding-bottom: 60px;
        .mobile_filter_portal {
            position: absolute;
            bottom: 0;
            margin-bottom: 0;
            width: calc(100% - 30px);
        }
    }

    .filter-input {
        width: 170px;
        border-bottom: solid 1px #666;
        padding-bottom: 4px;
    }
    .custom-checkbox .custom-control-label::before {
        border-radius: 0;
        border-color: #d2d1d0;
        background-color: #000;
        color: #000;
    }
    .custom-checkbox .custom-control-label::after {
        left: -1.59rem;
        background: no-repeat 80% / 80% 80%;
    }
    .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OENBRjk0MUE2NjBBMTFFQTlEOTVGMzFBMEE3NjhGMzciIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OENBRjk0MUI2NjBBMTFFQTlEOTVGMzFBMEE3NjhGMzciPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo4Q0FGOTQxODY2MEExMUVBOUQ5NUYzMUEwQTc2OEYzNyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo4Q0FGOTQxOTY2MEExMUVBOUQ5NUYzMUEwQTc2OEYzNyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Piya4HIAAA0RSURBVHjazFlpdFXV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');
    }
    .custom-radio .custom-control-label::before {
        border-color: #d2d1d0;
        background-color: #000;
        color: #000;
    }
    .custom-radio .custom-control-label::after {
        left: -1.56rem;
        background: no-repeat 80% / 74% 76%;
    }
    .custom-radio .custom-control-input:checked ~ .custom-control-label::after {
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAADuSURBVHja7NrBDcMwCAVQPuoAXaFbdcRs1RW6AT1UvSWOcZqYj8gAmBfASiRgZpLhuYmIvJfHP2KNvhEcPfj+fH0hE5LfioFDFZkEaMXF2ZCrBsoN0oCIoTM1MMJ1tgZHdOegBIiuXJQEsZuTEiGauSkZYjNHlSSPElZjNdeUFWH8nrf0M0IPYf5NtGqtghSkIAUpSEEKUpCCuCAgNqBaKzoErG2VurXAWI30ww62arQqAibEXmuBBdEzI2BA9A47oiM8txYiI7zXL6IiRPybD7/AFgUwCjkbdPlSzdrB09acRESQZfHsAwAA//8DAKaFJ2siR2N6AAAAAElFTkSuQmCC');
    }

    // Floating
    .floating-label {
        position: relative;
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
        box-shadow: unset;
    }

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
        &.product_label {
            top: 6px;
            line-height: 13px;
        }
    }

    .multiselect {
        &__select {
            display: none;
        }
        &.floating-label {
            margin-bottom: 0;
        }
        &__tags {
            background-color: #000 !important;
        }
        &__content-wrapper {
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

        &__option {
            display: none !important;
            padding: 0 !important;
        }

        &__tags &__input {
            margin-bottom: 0 !important;
        }
    }

    .floating-input:focus ~ label,
    .floating-select:focus ~ label,
    .floating-input:not(:placeholder-shown) ~ label,
    .floating-textarea.focused label,
    .multiselect--active ~ label,
    .filter_portal ~ label.focused,
    .floating-multiselect.selected > label,
    .floating-select ~ label.focused {
        top: -15px;
        color: gray;
    }

    /* active state */
    .floating-input:focus ~ .bar:before,
    .floating-input:focus ~ .bar:after,
    .floating-select:focus ~ .bar:before,
    .floating-select:focus ~ .bar:after {
        width: 50%;
    }
    .custom-control-label {
        width: 100%;
    }

    .result-container {
        display: flex;
        border: solid 1px gray;
        padding: 5px;
        margin: 1px;
        .result-media {
            position: relative;
            width: 100px;
            .media {
                img, video {
                    width: 100px;
                    height: 100px;
                    object-fit: cover;
                }
                img.video__tag__mobile {
                    position: absolute;
                    height: 25px;
                    width: auto;
                    top: -2px;
                    right: 0;
                }
            }
        }
        .result-data {
            flex-grow: 1;
            padding-left: 10px;
            max-width: calc(100% - 100px);
            .category-strain {
                color: gray;
                margin-top: 0;
                margin-bottom: 0;
                font-size: 13px;
            }
            .title {
                color: #EFA720;
                font-size: 20px;
                margin-bottom: 0;
            }
            .sub-title {
                padding-left: 5px;
                color: gray;
                font-size: 13px;
            }
            .company-name {
                display: flex;
                align-items: center;
                margin-top: .25rem;
                margin-bottom: 0;
                .store-type-img {
                    width: 22px;
                }
                .company_name {
                    color: #EFA720;
                    font-size: 16px;
                    display: inline-block;
                    max-width: calc(100% - 45px);
                    text-overflow: ellipsis;
                    overflow: hidden;
                    white-space: pre;
                    margin-left: 5px;
                }
            }
            .company-status {
                margin-left: 8px;
                .shop_status {
                    font-size: 15px;
                    margin-right: 5px;
                    &.open {
                        color: #93E612;
                    }
                    &.closed {
                        color: #FF0000;
                    }
                }
                .distance {
                    font-size: 13px;
                    color: gray;
                }
            }
            .price_data {
                span {
                    sup {
                        padding-left: 3px;
                    }
                }
            }
        }
    }
    #mapinfowindow {
        .modal-header {
            width: 100%;
            height: 46px;
        }
        .modal-content {
            background: transparent;
            border: none;
            max-height: calc(100% - 90px);
            .modal-header {
                position: fixed;
                top: 0;
                right: 0;
            }
        }
    }
</style>
<style lang="css">
    .multiselect__tags {
        padding-right: 0;
        min-height: 43px !important;
    }
</style>
