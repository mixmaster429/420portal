<template>
  <div class="row justify-content-center">
    <div class="mt-4 col-md-6">
      <div class="page-header">
          <!-- <img src="~/assets/imgs/h_icon3.png" alt="Contact Us" />                 -->
          <h1 >Contact Us</h1>
      </div>
      <form
        id="login_modal"
        @submit.prevent="sendmessage"
        @keydown="form.onKeydown($event)"
      >
        <div class="form-group floating-label">
          <textarea
            v-model="form.description"
            id="message"
            class="floating-input"
            required
            placeholder=" "
            autocomplete="off"
            rows="5"
          ></textarea>
          <label for="message">Message:</label>
        </div>

        <div class="form-group row mb-0">
          <div class="col-md-12 text-center">
            <v-button :loading="loading" v-if="user">
              Submit
            </v-button>
            <p class="mt-3 text-white" v-if="success" >Thank you for contacting us.</p>
          </div>
        </div>
      </form>
      <div class="form-group row mb-0" v-if="!user">
        <div class="col-md-12 text-center">
          <p class="text-white">
            Please login to submit message:
            <a href="#" style="color: #efa720;" @click="openLoginModal()" >
              Login
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Form from "vform";

export default {
  head() {
    return { title: "Contact-us" };
  },

  computed: mapGetters({
    user: "auth/user"
  }),

  data: () => ({
    form: new Form({
      description: ""
    }),
    loading: false,
    success: false
  }),
  methods: {
    async sendmessage() {
      this.loading = true;
      try {
        const response = await this.form.post("/contact-us");
        this.loading = false;
        if(response.data.success === "success") this.success = true
        this.form.description = ""
      } catch (e) {
        return;
      }
    },
    openLoginModal() {
      $("#loginmodal").modal();
    }
  }
};
</script>

<style lang="scss">

.floating-input:not(:placeholder-shown) ~ label {
  top: -20px;
}

</style>
