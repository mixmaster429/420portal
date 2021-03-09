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
                { hid: 'title', name: 'title', content: this.profile.meta_title},
                { hid: 'keywords', name: 'keywords', content: this.profile.meta_keywords},
                { hid: 'description', name: 'description', content: this.profile.meta_description }
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
            if(this.user == null || (this.user && this.user.id != this.profile.id && this.user.id != 1)) {
                alert('Deactivated user or company');
                window.location.href = "/"; 
            }
        }
    },
    methods: {
        fetchProfile () {
            return this.$store.dispatch('auth/fetchProfile', this.$route.params.username)
        },
    }
}
</script>