<template>
  <div class="row">
    <div class="col-lg-8 m-auto">
      <card title="Verify Email">
        <template v-if="success">
          <div class="alert alert-success" role="alert">
            {{ status }}
          </div>

          <router-link :to="{ name: 'login' }" class="btn btn-primary">
            Login
          </router-link>
        </template>
        <template v-else>
          <div class="alert alert-danger" role="alert">
            {{ status || 'Failed to verify email' }}
          </div>

          <router-link :to="{ name: 'verification.resend' }" class="small float-right">
            Resend verification link
          </router-link>
        </template>
      </card>
    </div>
  </div>
</template>

<script>

const qs = params => Object.keys(params).map(key => `${key}=${params[key]}`).join('&')

export default {
  middleware: 'guest',

  metaInfo () {
    return { title: 'Verify Email' }
  },

  async asyncData ({ params, query }) {
    try {
      const { data } = await this.axios.post(`/email/verify/${params.id}?${qs(query)}`)

      return { success: true, status: data.status }
    } catch (e) {
      return { success: false, status: e.response.data.status }
    }
  }
}
</script>
