import Vue from 'vue'
import { library, config } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import {
  faUser, faLock, faSignOutAlt, faSignInAlt, faCog, faUserPlus, faBookmark, faHeart, faComment, faTimes, faEdit, faTrashAlt  
} from '@fortawesome/free-solid-svg-icons'

import { 
  faHeart as farHeart,
  faBookmark as farBookmark,
  faComment as farComment,
} from '@fortawesome/free-regular-svg-icons'

import {
  faInstagram, faFacebookSquare, faTwitterSquare, faYoutubeSquare
} from '@fortawesome/free-brands-svg-icons'

config.autoAddCss = false

library.add(
  faUser, faLock, faSignOutAlt, faSignInAlt, faCog, faUserPlus, faBookmark, faHeart, faComment, faTimes, faEdit, faTrashAlt,
  farBookmark, farHeart, farComment,
  faInstagram, faFacebookSquare, faTwitterSquare, faYoutubeSquare
);

Vue.component('fa', FontAwesomeIcon)
