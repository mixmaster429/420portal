<template>
  <card title="Your Password">
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" message="Password has been updated" />

      <!-- Password -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">New Password</label>
        <div class="col-md-7">
          <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" type="password" name="password" class="form-control">
          <has-error :form="form" field="password" />
        </div>
      </div>

      <!-- Password Confirmation -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">Confirm Password</label>
        <div class="col-md-7">
          <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" type="password" name="password_confirmation" class="form-control">
          <has-error :form="form" field="password_confirmation" />
        </div>
      </div>

      <!-- Submit Button -->
      <div class="form-group row">
        <div class="col-md-9 ml-md-auto">
          <v-button :loading="form.busy" type="success">
            Update
          </v-button>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import Form from 'vform'

export default {
  scrollToTop: false,

  head () {
    return { title: 'Settings' }
  },

  data: () => ({
    form: new Form({
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async update () {
      await this.form.patch('/settings/password')

      this.form.reset()
    }
  }
}
</script>
