<template>
  <map-page :page_title="map_location.page_title"></map-page>
</template>

<script>
import { mapGetters } from 'vuex'
import MapPage from '~/components/MapPage'
export default {
  components: {
    MapPage,
  },
  head() {
    return {
      title: `${this.map_location.city ? this.map_location.city.name + ', ' : ''}${
        this.map_location.state ? this.map_location.state.name + ' - ' : ''
      }Marijuana Dispensary - Marijuana Delivery`,
      meta: [
        {
          hid: 'title',
          name: 'title',
          content: `${this.map_location.city ? this.map_location.city.name + ', ' : ''}${
            this.map_location.state ? this.map_location.state.name + ' - ' : ''
          }Marijuana Dispensary - Marijuana Delivery`,
        },
        {
          hid: 'keywords',
          name: 'keywords',
          content: `${this.map_location.city ? this.map_location.city.name.toLowerCase() + ', ' : ''}${
            this.map_location.state ? this.map_location.state.name.toLowerCase() + ', ' : ''
          }marijuana, weed, dispensary, collective, delivery, medical, recreational, cannabis`,
        },
        {
          hid: 'description',
          name: 'description',
          content: `Medical and Recreational Marijuana Dispensaries and Marijuana Deliveries${
            this.map_location.state ? ' in ' : ''
          }${this.map_location.city ? this.map_location.city.name + ', ' : ''}${
            this.map_location.state ? this.map_location.state.name : ''
          }. Medical and Recreational Cannabis.`,
        },
      ],
    }
  },
  watch: {
    $route: 'fetchMapLocation',
  },
  serverPrefetch() {
    return this.fetchMapLocation()
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      map_location: 'auth/map_location',
    }),
  },
  created() {},
  mounted() {
    if (!this.map_location) {
      this.fetchMapLocation()
    }
  },
  methods: {
    fetchMapLocation() {
      return this.$store.dispatch('auth/fetchMapLocation', this.$route.params)
    },
  },
}
</script>
