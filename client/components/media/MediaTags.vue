<template>
  <div class="media-taged-items" v-if="tagged_data">
    <p class="text-center text-white m-1 popup-title">In This Media</p>
    <hr class="bg-white m-0 mb-3">

    <div
      v-for="item in tagged_data.tagged_strainData"
      :key="item.id"
      class="taged_item"
    >
      <p class="d-flex align-items-center">
        <a :href="'/marijuana-strains/' + item.slug">
            <img :src="serverUrl(item.main_media.url)" alt="Profile picture" />
        </a>
        <span>
          <a :href="'/marijuana-strains/'+item.slug" class="username">{{ item.name }}</a>
        </span>
      </p>
      <p>
        <Button
          @click="remove_tag('strain', item.id)"
          v-show="logged_user_id === 1"
          >Untag</Button
        >
        <Button
          @click="strainfollow(item.id)"
          v-show="logged_user_id != item.id"
          v-if="!item.following"
          >Follow</Button
        >
        <img
          src="~assets/imgs/unfollow.png"
          alt
          class="pf-unfollow"
          @click="strainfollow(item.id)"
          v-show="logged_user_id != item.id"
          v-if="item.following"
        />
      </p>
    </div>
    
    <div
      v-for="item in tagged_data.tagged_users"
      :key="item.id"
      class="taged_item"
    >
      <p class="d-flex align-items-center">
        <a :href="'/' + item.name">
            <img :src="serverUrl(item.profile_pic.url)" alt="Profile picture" />
        </a>
        <a :href="'/'+item.name" class="username">{{ item.name }}</a>
      </p>
      <p>
        <Button
          @click="remove_tag('user', item.id)"
          v-show="logged_user_id === item.id || logged_user_id === 1"
          >Untag</Button
        >
        <Button
          @click="follow(item.id)"
          v-show="logged_user_id != item.id"
          v-if="!item.following"
          >Follow</Button
        >
        <img
          src="~assets/imgs/unfollow.png"
          alt
          class="pf-unfollow"
          @click="unfollow(item.id)"
          v-show="logged_user_id != item.id"
          v-if="item.following"
        />
      </p>
    </div>

    <div
      v-for="item in tagged_data.tagged_companyData"
      :key="item.id"
      class="taged_item"
    >
      <p class="d-flex align-items-center">
        <a :href="'/' + item.name">
            <img :src="serverUrl(item.profile_pic.url)" alt="Profile picture" />
        </a>
        <a :href="'/'+item.name" class="username">{{ item.name }}</a>
      </p>
      <p>
        <Button
          @click="remove_tag('portal', item.id)"
          v-show="logged_user_id === item.id  || logged_user_id === 1"
          >Untag</Button
        >
        <Button
          @click="follow(item.id)"
          v-show="logged_user_id != item.id"
          v-if="!item.following"
          >Follow</Button
        >
        <img
          src="~assets/imgs/unfollow.png"
          alt
          class="pf-unfollow"
          @click="unfollow(item.id)"
          v-show="logged_user_id != item.id"
          v-if="item.following"
        />
      </p>
    </div>

    
  </div>
</template>

<script>
export default {
  name: "MediaTags",
  props: ["media", "logged_user_id"],
  data() {
    return {
      mediadata: this.media,
      tagged_data: null
    };
  },
  watch: {
    media: function(newVal, oldVal) {
      console.log("------------");
      console.log(newVal);
      console.log(this.logged_user_id)
      this.mediadata = newVal;
      this.gettagged();
    }
  },
  methods: {
    gettagged() {
      let uri = "/media/gettaged";
      let params = {
        id: this.mediadata.id
      };
      let _this = this;
      this.axios.post(uri, params).then(response => {
        _this.tagged_data = response.data;
      });
    },
    serverUrl(item) {
      if (item.charAt(0) != "/") {
        item = "/" + item;
      }
      try {
        return process.env.serverUrl + item;
      } catch (error) {
        return process.env.serverUrl + "/imgs/default.png";
      }
    },
    strainfollow(id) {
      if (this.logged_user_id) {
        let uri = "/marijuana-strains/follow";
        let params = {
          user_id: this.logged_user_id,
          follower_id: id
        };
        let _this = this;
        this.axios.post(uri, params).then(response => {
          _this.gettagged();
        });
      } else {
        $("#loginmodal").modal("show");
      }
    },
    remove_tag(type, id) {
      if (this.logged_user_id) {
        let uri = "/media/removetag";
        let params = {
          type: type,
          media_id: this.mediadata.id,
          target_id: id
        };
        let _this = this;
        this.axios.post(uri, params).then(response => {
          _this.gettagged();
        });
      } else {
        $("#loginmodal").modal("show");
      }
    },
    follow(id) {
      if (this.logged_user_id) {
        let uri = "/user/follow";
        let params = {
          user_id: this.logged_user_id,
          follower_id: id
        };
        let _this = this;
        this.axios.post(uri, params).then(response => {
          _this.gettagged();
        });
      } else {
        $("#loginmodal").modal("show");
      }
    },
    unfollow(id) {
      if (this.logged_user_id) {
        let uri = "/user/unfollow";
        let params = {
          user_id: this.logged_user_id,
          follower_id: id
        };
        let _this = this;
        this.axios.post(uri, params).then(response => {
          _this.gettagged();
        });
      } else {
        $("#loginmodal").modal("show");
      }
    }
  },
  mounted() {
    this.gettagged();
  }
};
</script>

<style lang="scss">
.media-taged-items {

  .popup-title {
    font-size: 20px;
  }
  
  .taged_item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    img {
      width: 35px;
      height: 35px;
      border-radius: 100%;
      object-fit: cover;
      margin-right: 20px;
    }

    .username {
      color: #efa720;
      text-decoration: none;
    }

    .pf-unfollow {
      width: 25px;
      height: 25px;
      cursor: pointer;
    }

    button {
      background-color: #efa720;
      padding: 2px 4px;
      border: none;
    }
  }
}
</style>
