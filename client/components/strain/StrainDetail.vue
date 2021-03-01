<template>
  <div class="col-12">
    <div class="container-fluid" id="strain_show_page">
      <div class="row">
        <div class="col-md-9">
          <div
            class="single__content d-flex text-center align-items-center mt-4"
          >
            <div class="single__img">
              <div class="container_img">
                <img
                  class="img-fluid"
                  :src="
                    strain_data.main_media
                      ? serverUrl(strain_data.main_media.url)
                      : '~assets/imgs/strains/blue.jpg'
                  "
                  :alt="strain_data.strain.name + ' Marijuana Strain'"
                />
              </div>
            </div>
            <div class="flex-grow-1">
              <div class="strain-information d-flex justify-content-center">
                <div class="single__post__container">
                  <div class="single__post__count">
                    {{ strain_data.posts_count }}
                  </div>
                  <div class="single__post__label">Posts</div>
                </div>
                <div
                  class="single__follow__container"
                  style="cursor: pointer;"
                  @click="openFollowerPopup = true"
                >
                  <div
                    class="single__follow__count btn-open-modal"
                    id="followers_count"
                  >
                    {{ strain_data.followers_count }}
                  </div>
                  <div class="single__follow__label btn-open-modal">
                    Followers
                  </div>
                </div>
              </div>
              <div class="strain-information text-center strain-action">
                <img
                  src="~assets/imgs/follow-icon.png"
                  alt
                  class="btn-follow st-follow"
                  @click="follow"
                  :class="{ 'd-none': strain_data.is_follower == 1 }"
                />
                <img
                  src="~assets/imgs/unfollow.png"
                  alt
                  class="btn-follow st-unfollow"
                  @click="follow"
                  :class="{ 'd-none': strain_data.is_follower != 1 }"
                />
                <div
                  class="single__comment mt-1 mx-auto"
                  style="width: 60px;"
                  data-toggle="modal"
                  data-target="#commentModal"
                  v-if="$device.isMobile"
                >
                  <fa
                    :icon="['far', 'comment']"
                    fixed-width
                    class="single__icon"
                  />
                  <p id="count_comments">0</p>
                </div>
              </div>
            </div>
          </div>
          <div class="single__content mb-5">
            <h1 class="single__name mt-2 text-white">
              {{ strain_data.strain.name }}
              <a
                href="#"
                v-if="user && user.name == '420portal'"
                @click.prevent="openEditPopup = true"
              >
                <img
                  class="portal__edit"
                  src="~assets/imgs/edit.png"
                  width="28"
                  alt
                />
              </a>
            </h1>
            <div class="single__category">
              {{ strain_data.strain.category.name }}
            </div>
          </div>
          <h2 class="text-center my-4 category__header">
            {{ strain_data.strain.name }} Marijuana Strain
          </h2>

          <edit-description
            class="category__content"
            type="strain"
            :strain="strain_data.strain"
            :auth="user"
          ></edit-description>
        </div>
        <div class="col-md-3" v-if="!$device.isMobile">
          <div class="commentbox-header">
              <h5>Reviews - Comments</h5> 
          </div>
          <page-comment
            :page="strain_data.strain"
            model="strain"
          ></page-comment>
        </div>
      </div>

      <div class="strains__extra mt-3">
        <ul
          class="nav extra__list justify-content-center mb-3"
          id="pills-tab"
          role="tablist"
          v-if="this.menu.all"
        >
          <li class="nav-item" v-if="menu.flowers > 0">
            <a
              class="nav-link active"
              id="pills-flowers-tab"
              data-toggle="pill"
              href="#pills-flowers"
              role="tab"
              aria-controls="pills-flowers"
              aria-selected="true"
              >Flowers</a
            >
          </li>
          <li class="nav-item" v-if="menu.concentrates > 0">
            <a
              class="nav-link"
              id="pills-concentrates-tab"
              data-toggle="pill"
              href="#pills-concentrates"
              role="tab"
              aria-controls="pills-concentrates"
              aria-selected="false"
              >Concentrates</a
            >
          </li>
          <li class="nav-item" v-if="menu.vapepens > 0">
            <a
              class="nav-link"
              id="pills-vape-pens-tab"
              data-toggle="pill"
              href="#pills-vape-pens"
              role="tab"
              aria-controls="pills-vape-pens"
              aria-selected="false"
              >Vape Pens</a
            >
          </li>
          <li class="nav-item" v-if="menu.clones > 0">
            <a
              class="nav-link"
              id="pills-clones-tab"
              data-toggle="pill"
              href="#pills-clones"
              role="tab"
              aria-controls="pills-clones"
              aria-selected="false"
              >Clones</a
            >
          </li>
          <li class="nav-item" v-if="menu.seeds > 0">
            <a
              class="nav-link"
              id="pills-seeds-tab"
              data-toggle="pill"
              href="#pills-seeds"
              role="tab"
              aria-controls="pills-seeds"
              aria-selected="false"
              >Seeds</a
            >
          </li>
          <li class="nav-item" v-if="menu.preroll > 0">
            <a
              class="nav-link"
              id="pills-pre-roll-tab"
              data-toggle="pill"
              href="#pills-pre-roll"
              role="tab"
              aria-controls="pills-pre-roll"
              aria-selected="false"
              >Pre Roll</a
            >
          </li>
        </ul>
        <client-only>
          <div
            class="tab-content extra__content"
            style="border: none"
            id="pills-tabContent"
          >
            <div
              class="tab-pane fade show"
              v-bind:class="{'active': menu.active === 'flower'}"
              id="pills-flowers"
              role="tabpanel"
              aria-labelledby="pills-flowers-tab"
            >
              Flowers
              <strain-menu
                :strain="strain_data.strain"
                category="flowers"
                type="2"
              ></strain-menu>
            </div>
            <div
              class="tab-pane fade show"
              v-bind:class="{'active': menu.active === 'concentrates'}"
              id="pills-concentrates"
              role="tabpanel"
              aria-labelledby="pills-concentrates-tab"
            >
              Concentrates
              <strain-menu
                :strain="strain_data.strain"
                category="concentrates"
                type="1"
              ></strain-menu>
            </div>
            <div
              class="tab-pane fade show"
              v-bind:class="{'active': menu.active === 'vapepens'}"
              id="pills-vape-pens"
              role="tabpanel"
              aria-labelledby="pills-vape-pens-tab"
            >
              Vape Pens
              <strain-menu
                :strain="strain_data.strain"
                category="vape-pens"
                type="1"
              ></strain-menu>
            </div>
            <div
              class="tab-pane fade show"
              v-bind:class="{'active': menu.active === 'clones'}"
              id="pills-clones"
              role="tabpanel"
              aria-labelledby="pills-clones-tab"
            >
              Clones
              <strain-menu
                :strain="strain_data.strain"
                category="clones"
                type="0"
              ></strain-menu>
            </div>
            <div
              class="tab-pane fade show"
              v-bind:class="{'active': menu.active === 'seeds'}"
              id="pills-seeds"
              role="tabpanel"
              aria-labelledby="pills-seeds-tab"
            >
              Seeds
              <strain-menu
                :strain="strain_data.strain"
                category="seeds"
                type="0"
              ></strain-menu>
            </div>
            <div
              class="tab-pane fade show"
              v-bind:class="{'active': menu.active === 'preroll'}"
              id="pills-pre-roll"
              role="tabpanel"
              aria-labelledby="pills-pre-roll-tab"
            >
              Pre Roll
              <strain-menu
                :strain="strain_data.strain"
                category="pre-roll"
                type="0"
              ></strain-menu>
            </div>
          </div>
        </client-only>
      </div>

      <h2 class="text-center mt-4 mb-1 category__header">
        {{ strain_data.strain.name }} Pictures & Videos
      </h2>
      <p class="text-center category__addmedia" v-if="$device.isMobile">
        Upload Your Pictures &amp; Videos of this Strain.
        <a href="/mobile/media/add" class="d-block">
          <img src="/imgs/strain_add_media.png" alt="" />
        </a>
      </p>
      <p class="text-center category__addmedia" v-else>
        <strain-add-media :strain_data="strain_data"></strain-add-media>
      </p>
    </div>
    <template>
      <!-- <keep-alive v-if="$device.isMobile">
                <router-view></router-view> -->
      <strain-mobile
        :strain="strain_data.strain"
        v-if="$device.isMobile"
      ></strain-mobile>
      <!-- </keep-alive> -->
      <strain-media :strain="strain_data.strain" v-else></strain-media>
    </template>
    <client-only>
      <vs-popup
        v-if="user && strain_data && user.id == 1"
        class="strains__popup strain_edit_popup"
        type="border"
        title="Edit Strain"
        :active.sync="openEditPopup"
      >
        <edit-strain :from="strain_data.strain"></edit-strain>
      </vs-popup>

      <vs-popup
        v-if="strain_data"
        class="strains__popup strain_followers_popup"
        type="border"
        title="Edit Strain"
        :active.sync="openFollowerPopup"
      >
        <strain-followers :strain="strain_data.strain"></strain-followers>
      </vs-popup>
    </client-only>

    <!-- Comment Modal -->
    <div class="modal fade" id="commentModal" v-if="$device.isMobile">
      <div class="modal-dialog" role="document">
        <div class="modal-content comment_page">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true"><fa icon="arrow-left"></fa></span>
            </button>
            <h5
              class="modal-title ml-4"
              style="font-size: 1.3rem;color:#EFA720;"
            >
              Reviews - Comments
            </h5>
          </div>
          <div class="modal-body" style="padding: 0;">
            <page-comment
              :page="strain_data.strain"
              model="strain"
            ></page-comment>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import _ from "lodash";
import StrainMenu from "./StrainMenu";
import StrainMedia from "./StrainMedia";
import StrainMobile from "../mobile/StrainMedia";
import EditStrain from "./EditStrain";
import EditDescription from "./EditDescription";
import StrainFollowers from "./StrainFollowers";
import PageComment from "../PageComment";
import StrainAddMedia from "../media/StrainAddMedia.vue";

export default {
  name: "StrainDetail",
  props: ["strain_data"],
  components: {
    StrainMenu,
    StrainMedia,
    EditDescription,
    EditStrain,
    StrainFollowers,
    PageComment,
    StrainMobile,
    StrainAddMedia
  },
  data: function() {
    return {
      openEditPopup: false,
      openFollowerPopup: false,
      menu: {
        all: false,
        active: "",
        flowers: 0,
        concentrates: 1,
        vapepens: 0,
        clones: 0,
        seeds: 0,
        preroll: 0
      }
    };
  },
  computed: {
    ...mapGetters({
      user: "auth/user"
    })
  },
  mounted() {
    const menus = Object.values(this.strain_data.menus);
    for (let i = 0; i < menus.length; i++) {
      if (menus[i]["strain_id"] !== this.strain_data.strain.id) continue;
      if (menus[i]["category_id"] < 3) {
        this.menu.flowers++;
        this.menu.all = 1;
        if (!this.menu.all) this.menu.all = true;
        if (this.menu.active === "") this.menu.active = "flower";
      }
      if (menus[i]["category_id"] === 4) {
        this.menu.concentrates++;
        if (!this.menu.all) this.menu.all = true;
        if (this.menu.active === "") this.menu.active = "concentrates";
      }
      if (menus[i]["category_id"] === 5) {
        this.menu.vapepens++;
        if (!this.menu.all) this.menu.all = true;
        if (this.menu.active === "") this.menu.active = "vapepens";
      }
      if (menus[i]["category_id"] === 9) {
        this.menu.clones++;
        if (!this.menu.all) this.menu.all = true;
        if (this.menu.active === "") this.menu.active = "clones";
      }
      if (menus[i]["category_id"] === 10) {
        this.menu.seeds++;
        if (!this.menu.all) this.menu.all = true;
        if (this.menu.active === "") this.menu.active = "seeds";
      }
      if (menus[i]["category_id"] === 12) {
        this.menu.preroll++;
        if (!this.menu.all) this.menu.all = true;
        if (this.menu.active === "") this.menu.active = "preroll";
      }
    }
  },
  methods: {
    openLoginModal() {
      $("#loginmodal").modal();
    },
    follow() {
      if (!this.user) {
        $("#loginmodal").modal();
      } else {
        let url = `/marijuana-strains/follow`;
        let params = {
          user_id: this.user.id,
          follower_id: this.strain_data.strain.id
        };
        this.axios.post(url, params).then(response => {
          if (response.data.status == 200) {
            $("#followers_count").text(response.data.count);
            $(".btn-follow").hide();
            if (response.data.is_follower) {
              $(".st-unfollow")
                .removeClass("d-none")
                .show();
            } else {
              $(".st-follow")
                .removeClass("d-none")
                .show();
            }
          }
        });
      }
    },
    serverUrl(item) {
      if (item.charAt(0) != "/") {
        item = "/" + item;
      }
      try {
        return process.env.serverUrl + item;
      } catch (error) {
        return process.env.serverUrl + "imgs/default.png";
      }
    }
  }
};
</script>

<style lang="scss" scoped>
#pills-tabContent table tr td {
  color: #efa720;
}
#pills-tabContent table tr td sup {
  color: white;
}
#commentModal .modal-header {
  color: #efa720;
  padding: 6px 0.8rem;
  background: #000;
  border-radius: 0;
  -webkit-box-pack: start;
  justify-content: flex-start;
  -webkit-box-align: center;
  align-items: center;
  border-bottom: solid 1px white;
}
#commentModal .modal-header .close {
  position: initial;
  margin-left: 0;
  padding: 0;
  color: #efa720;
  text-shadow: none;
}
#commentModal .modal-dialog {
  margin: 80px auto;
}
@media (max-width: 600px) {
  #commentModal .modal-dialog {
    margin: 0;
  }
}
.image-panel {
  position: absolute;
  display: none;
  padding: 8px;
  border: solid 1px white;
  border-radius: 5px;
  background-color: #000;
  z-index: 1000;
}
.image-panel .menu-media img {
  width: 200px;
  height: 200px;
  object-fit: cover;
}
.image-panel .menu-description {
  color: gray;
  max-width: 300px;
}
.container_img {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
  border-radius: 50%;
}
.container_img img {
  display: inline;
  margin: 0 auto;
  min-height: 100%;
  width: auto;
}
#editModal {
  color: #fff;
  font-size: 16px;
}
</style>
<style lang="scss">
.strain_edit_popup {
  .vs-popup {
    height: 270px;
  }
}
</style>
