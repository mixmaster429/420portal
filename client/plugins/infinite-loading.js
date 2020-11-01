import Vue from 'vue'
import InfiniteLoading from 'vue-infinite-loading'
Vue.component('infinite-loading', InfiniteLoading);

// Vue.use(InfiniteLoading, {
// 	props: {
// 		spinner: 'spiral',
// 		/* other props need to configure */
// 	},
// 	system: {
// 		throttleLimit: 50,
// 		/* other settings need to configure */
// 	},
// 	slots: {
// 		noMore: 'No more media', // you can pass a string value
// 		// error: InfiniteError, // you also can pass a Vue component as a slot
// 	},
// });

// let InfiniteLoading
// if (process.BROWSER_BUILD) {
//   InfiniteLoading = require('vue-infinite-loading')
// }