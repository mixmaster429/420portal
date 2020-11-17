<template>
    <div class="row justify-content-center" id="signup_page">
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="card mt-4 register_card">
                <div class="card-body">
                    <div class="text-center mt-5">
                        <img src="~/assets/imgs/logo.png" width="80%" alt="">
                    </div>
                    <form @submit.prevent="reset" @keydown="form.onKeydown($event)">
                        <!-- Email -->
                        <!-- <div class="form-group floating-label mt-5">
                            <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email" name="email" class="floating-input" placeholder=" " />
                            <label class="">Email</label>
                            <has-error :form="form" field="email" />
                        </div> -->

                        <!-- Password -->
                        <div class="form-group floating-label mt-5">
                            <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" type="password" name="password" class="floating-input" placeholder=" " />
                            <label class="">New Password</label>
                            <has-error :form="form" field="password" />
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-md-12">
                                <v-button :loading="form.busy" :block="true">Reset Password</v-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <page-footer></page-footer>
    </div>
</template>

<script>
import Form from 'vform'
import PageFooter from "~/components/PageFooter";

export default {
    components: {
        PageFooter,
    },
    head () {
        return { title: '420Portal - Reset Password' }
    },
    data: () => ({
        status: '',
        form: new Form({
            token: '',
            email: '',
            password: '',
        }),
    }),
    created () {
        this.form.email = this.$route.query.email
        this.form.token = this.$route.params.token
    },
    methods: {
        async reset () {
            const { data } = await this.form.post('/password/reset')
            if(data.status == 'success') {
                const { data: { token } } = await this.axios.post('/login', {username: data.user.username, password: this.form.password})
                // Save the token.
                this.$store.dispatch('auth/saveToken', { token })
                // Update the user.
                await this.$store.dispatch('auth/updateUser', { user: data })
                this.form.reset()
                // Redirect home.
                window.location.href = "/";
            }
        },
        async register () {
            // Register the user
            const { data } = await this.form.post('/register')
            if (data) {
                // Log in the user.
                const { data: { token } } = await this.form.post('/login')

                // Save the token.
                this.$store.dispatch('auth/saveToken', { token })

                // Update the user.
                await this.$store.dispatch('auth/updateUser', { user: data })

                // Redirect home.
                window.location.href = "/";
            }
        },
    }
}
</script>
