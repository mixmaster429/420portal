import Vue from 'vue'
import { library, config } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import {
  faUser, faLock, faSignOutAlt, faSignInAlt, faCog, faUserPlus, faBookmark, faHeart, faComment, faTimes, faEdit, faTrashAlt, faEllipsisH, faArrowLeft, faAngleDown, faPhoneAlt, faEnvelope, faGlobeAmericas, faPaperclip, faTimesCircle, faBars, faCheck, faSearch, faDotCircle, faArrowRight, faPlus, faReply, faChevronUp, faArrowDown, faArrowUp, faChevronDown,  
} from '@fortawesome/free-solid-svg-icons'

import { 
  faHeart as farHeart,
  faBookmark as farBookmark,
  faComment as farComment,
  faDotCircle as farDotCircle
} from '@fortawesome/free-regular-svg-icons'

import {
  faInstagram, faFacebookSquare, faTwitterSquare, faYoutubeSquare, faTelegramPlane
} from '@fortawesome/free-brands-svg-icons'

config.autoAddCss = false

library.add(
  faUser, faLock, faSignOutAlt, faSignInAlt, faCog, faUserPlus, faBookmark, faHeart, faComment, faTimes, faEdit, faTrashAlt, faEllipsisH, faArrowLeft, faArrowRight, faArrowDown, faArrowUp, faAngleDown, faPhoneAlt, faEnvelope, faGlobeAmericas, faPaperclip, faTimesCircle, faBars, faCheck, faSearch, faDotCircle, faPlus, faReply, faChevronUp, faChevronDown,
  farBookmark, farHeart, farComment, farDotCircle,
  faInstagram, faFacebookSquare, faTwitterSquare, faYoutubeSquare, faTelegramPlane
);

Vue.component('fa', FontAwesomeIcon)
