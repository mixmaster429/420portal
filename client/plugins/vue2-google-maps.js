import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'

Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyAs_LAuZVVqNTEdv765oUp6arI5Tjxs43s',
        libraries: 'places',
        mapTypeControl: false,
    },
});