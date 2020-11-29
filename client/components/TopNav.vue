<template>
    <header class="sticky-top">
        <div class="container">
            <!-- Desktop Nav -->
            <div class="row desktop_nav">
                <div class="col col-3 logo_image">
                    <a href="/">
                        <img src="~assets/imgs/logo.png" height="50" alt="">
                    </a>
                </div>
                <div class="col-7 nav_bar">
                    <ul>
                        <li class="nav-item">
                            <a href="/medical-recreational-marijuana-dispensary-delivery">
                                <img src="~assets/imgs/h_icon1.png" alt="">
                                <p>Dispensary</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-strains">
                                <img src="~assets/imgs/h_icon2.png" alt="">
                                <p>Strains</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-coupons">
                                <img src="~assets/imgs/h_icon3.png" alt="">
                                <p>Coupons</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-brands">
                                <img src="~assets/imgs/brand_logo.png" alt="">
                                <p>Brands</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-forums">
                                <img src="~assets/imgs/h_icon4.png" alt="">
                                <p>Forums</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-news">
                                <img src="~assets/imgs/news.png" alt="">
                                <p>News</p>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col col-2 auth_nav">
                    <ul v-if="user">
                        <li>
                            <a href="javascript:;" @click.prevent="logout">
                                <fa icon="sign-out-alt" fixed-width />
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                    <ul v-else>
                        <li>
                            <a href="javascript:;" @click="openLoginModal()">
                                <fa icon="sign-in-alt" fixed-width />
                                <p>Login</p>
                            </a>
                        </li>
                        <li>
                            <a href="/register">
                                <fa icon="user" fixed-width />
                                <p>Register</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div> 
            <!-- Mobile Nav -->
            <div class="mobile_nav">
                <div class="logo_image">
                    <a href="/"><img src="~assets/imgs/logo.png" height="50" alt=""></a>
                </div>
                <div class="nav_bar">
                    <ul>
                        <li class="nav-item">
                            <a href="/medical-recreational-marijuana-dispensary-delivery">
                                <img src="~assets/imgs/h_icon1.png" alt="">
                                <p>Dispensary</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-strains">
                                <img src="~assets/imgs/h_icon2.png" alt="">
                                <p>Strains</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-coupons">
                                <img src="~assets/imgs/h_icon3.png" alt="">
                                <p>Coupons</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-brands">
                                <img src="~assets/imgs/brand_logo.png" alt="">
                                <p>Brands</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-forums">
                                <img src="~assets/imgs/h_icon4.png" alt="">
                                <p>Forums</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/marijuana-news">
                                <img src="~assets/imgs/news.png" alt="">
                                <p>News</p>
                            </a>
                        </li>
                    </ul>
                    <a href="javascript:;" class="btn-login" data-toggle="modal" data-target="#loginmodal" v-if="!user">
                        <img src="~assets/imgs/icon-sign-in.png" height="30" alt="">
                    </a>
                </div>
            </div>           
        </div>
    </header>
</template>

<script>
import { mapGetters } from 'vuex'

export default {

    data: () => ({
        appName: process.env.appName
    }),

    computed: mapGetters({
        user: 'auth/user',
    }),

  methods: {
    openLoginModal() {
        $("#loginmodal").modal();
    },
    async logout () {
        // Log out the user.
        await this.$store.dispatch('auth/logout');

        // Redirect to login.
        // this.$router.push({ name: 'login' })
    }
  }
}
</script>

<style scoped>

</style>
