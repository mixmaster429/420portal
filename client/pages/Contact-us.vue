<template>
  <div class="row">
    <div class="mt-5 col-md-6 offset-md-3">
      <form
        id="login_modal"
        @submit.prevent="sendmessage"
        @keydown="form.onKeydown($event)"
      >
        <div class="form-group floating-label">
          <input
            v-model="form.name"
            id="fullname"
            type="text"
            class="floating-input"
            placeholder=" "
            autocomplete="off"
            required
          />
          <label for="fullname">Full Name:</label>
        </div>

        <div class="form-group floating-label">
          <input
            v-model="form.email"
            type="email"
            id="email"
            class="floating-input"
            required
            placeholder=" "
            autocomplete="off"
          />
          <label for="email">Email Address:</label>
        </div>

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
            <v-button :loading="loading">
              Submit
            </v-button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Form from "vform";

export default {
  head() {
    return { title: "Contact-us" };
  },

  data: () => ({
    form: new Form({
      name: "",
      email: "",
      description: ""
    }),
    loading: false
  }),
  methods: {
    async sendmessage() {
      let data;
      this.loading = true;
      // Submit the form.
      try {
        const response = await this.form.post("/contact-us");
        data = response.data;
      } catch (e) {
        return;
      }
    }
  }
};
</script>

<style lang="scss"></style>
