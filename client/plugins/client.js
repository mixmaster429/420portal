require('emojionearea');
import '../assets/css/emoji.min.css';

import Vue from "vue";
import VueTrix from "vue-trix";

import Vue2TouchEvents from "vue2-touch-events";
import quillEmoji from 'quill-emoji-vue';

import VueSlider from 'vue-slider-component/dist-css/vue-slider-component.umd.min.js'
import 'vue-slider-component/dist-css/vue-slider-component.css'
import 'vue-slider-component/theme/default.css';

if (process.client) {
    const VueQuillEditor = require('vue-quill-editor/dist/ssr')
    // const quillEmoji = require('quill-emoji-vue');
    Vue.use(VueQuillEditor);
}
    Vue.use(quillEmoji);

Vue.use(VueTrix);
Vue.use(Vue2TouchEvents);
Vue.component('VueSlider', VueSlider)
// Vue.use(VueSlider);
