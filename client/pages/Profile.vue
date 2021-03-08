<template>
    <user-profile v-if="profile.type == 'user'" :user="profile"></user-profile>
    <portal-profile :profile="profile" ref="portalprofile" v-else></portal-profile>
</template>

<script>
import { mapGetters } from "vuex";
import _ from 'lodash';
import UserProfile from "~/components/UserProfile"
import PortalProfile from "~/components/PortalProfile"

export default {
    components: {
        UserProfile, PortalProfile
    },
    head () {
        return {
            title: this.profile.title_tag,
            meta: [
                { hid: 'title', name: 'title', content: this.profile.type == 'user' ? `${this.profile.name} - 420Portal.com` : `${this.profile.username} - ${this.profile.name} - ${this.profile.store_type == 1 ? 'Dispensary' : this.profile.store_type == 2 ? 'Delivery' : this.profile.store_type == 3 ? 'Dispensary and Delivery' : ''}` },
                { hid: 'keywords', name: 'keywords', content: this.profile.type == 'user' ? `${this.profile.name}, marijuana, weed, cannabis, pictures, videos` : `${this.profile.username}, ${this.profile.name}, marijuana, cannabis, delivery, dispensary, recreational, medical` },
                { hid: 'description', name: 'description', content: this.profile.type == 'user' ? `${this.profile.name} - Member on 420Portal.com - Sharing Pictures and Videos.` : `${this.profile.username} - ${this.profile.name} is a Marijuana ${this.profile.store_type == 1 ? 'Dispensary' : this.profile.store_type == 2 ? 'Delivery' : this.profile.store_type == 3 ? 'Dispensary and Delivery' : ''} Selling Cannabis. Find your Weed.` }
            ],
        }
    },
    data(){
        return {

        }
    },
    watch: {
        $route: "fetchProfile",
    },
    serverPrefetch () {
        return this.fetchProfile()
    },
    computed: {
        ...mapGetters({
            user: 'auth/user',
            profile: 'auth/profile',
        }),
    },
    created() {
    },
    mounted() {
        if (!this.profile) {
            this.fetchProfile();
        }
        if (this.profile.is_active == 0) {
            alert('Deactivated user or company');
            window.location.href = "/";
        }
    },
    methods: {
        fetchProfile () {
            return this.$store.dispatch('auth/fetchProfile', this.$route.params.username)
        },
    }
}
</script>