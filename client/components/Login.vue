<template>
    <div class="login-component">
        <form id="login_modal" @submit.prevent="login" @keydown="form.onKeydown($event)">
            <div class="form-group floating-label">
                <input
                    v-model="form.username" 
                    :class="{ 'is-invalid': form.errors.has('username') }"
                    id="username" 
                    type="text"
                    class="floating-input"
                    placeholder=" "
                    autocomplete="off"
                    required
                >
                <has-error :form="form" field="username" />
                <label for="username">Username</label>
            </div>

            <div class="form-group floating-label">
                <input 
                    v-model="form.password" 
                    :class="{ 'is-invalid': form.errors.has('password') }"
                    type="password" 
                    id="password" 
                    class="floating-input"
                    required
                    placeholder=" "
                    autocomplete="off">
                    <has-error :form="form" field="password" />
                <label for="password">Password</label>
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary btn-block mb-2" style="font-size:16px">Login</button>
                </div>
            </div>
        </form>

        <form id="reset_modal" @submit.prevent="sendResetRequest" @keydown="form.onKeydown($event)" class="mt-3">
            <p style="color: white;font-size:18px;" class="text-center">Forgot Username/Password?</p>
            <alert-success :form="form_password" :message="reset_status" />
            <div class="form-group floating-label" id="reset_email">
                <input 
                    v-model="form_password.email" 
                    :class="{ 'is-invalid': form_password.errors.has('email') }" 
                    id="email" 
                    type="text" 
                    class="floating-input" 
                    name="email" 
                    required 
                    autocomplete 
                    placeholder=" ">
                <has-error :form="form_password" field="email" />
                <label for="username">Username / Email</label>
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block" style="font-size:16px">Reset Password</button>
                </div>
            </div>
        </form>

        <p class="text-center mt-3" style="color: white;font-size:16px;">Don't have an account? <a class="btn-link" href="/register" style="color: #EFA720;">Sign up</a></p>
    </div>
</template>

<script>
import Form from 'vform'

export default {

    data: () => ({
        form: new Form({
            email: '',
            username: '',
            password: ''
        }),
        form_password: new Form({
            email: '',
        }),
        reset_status: '',
        remember: false
    }),

    methods: {
        async login () {
            let data;
            // Submit the form.
            try {
                const response = await this.form.post('/login')
                data = response.data
            } catch (e) {
                return
            }

            // Save the token.
            this.$store.dispatch('auth/saveToken', {
                token: data.token,
                remember: this.remember
            })

            // Fetch the user.
            await this.$store.dispatch('auth/fetchUser')

            // Redirect home.
            //   this.$router.push({ name: 'home' })
            // window.location.reload();
            $("#loginmodal").modal('hide');
        },
        async requestResetPassword() {
            console.log('request reset password');
        },

        async sendResetRequest () {
            const { data } = await this.form_password.post('/password/email')
            if(data.status == 'success' || data.status == 'error') {
                this.reset_status = data.message
            }            
            this.form_password.reset()
        }
    }
}
</script>

<style lang="scss">
    .alert {
        margin: 0;
        padding: 5px 25px 5px 5px;
        button {
            padding: 3px !important;
            font-size: 20px;
        }
    }
</style>
