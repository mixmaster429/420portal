import Vue from 'vue'
import Router from 'vue-router'
import { scrollBehavior } from '~/utils'

Vue.use(Router)

const page = path => () => import(`~/pages/${path}`).then(m => m.default || m)

const routes = [
  { path: '/', name: 'home', component: page('Home.vue') },

  { path: '/marijuana-strains', name: 'strain_index', component: page('Strain.vue') },
  { path: '/marijuana-strains/:strain', name: 'strain_detail', component: page('StrainDetail.vue') },
  { path: '/medical-recreational-marijuana-dispensary-delivery/:state?/:city?', name: 'map_page', component: page('MapPage.vue')},

  { path: '/marijuana-coupons', name: 'coupon', component: page('Coupon.vue') },
  { path: '/marijuana-brands', name: 'brand', component: page('Brand.vue') },

  { path: '/marijuana-news', name: 'news', component: page('News.vue') },
  { path: '/marijuana-news/:slug', name: 'news_detail', component: page('NewsDetail.vue') },

  { path: '/marijuana-forums', name: 'forum', component: page('forum/ForumList.vue') },
  { path: '/marijuana-forums/:slug/:id', name: 'forum_detail', component: page('forum/ForumDetail.vue') },
  { path: '/marijuana-forums/:slug/:origin_id/:id', name: 'forum_reply_detail', component: page('forum/ForumDetail.vue') },

  { path: '/notification', name: 'notification', component: page('Notification.vue') },

  { path: '/media/:id', name: 'media', component: page('Media.vue') },

  { path: '/mobile/slideshow', name: 'weedgram', component: page('mobile/Weedgram.vue') },
  { path: '/mobile/comment', name: 'comment', component: page('mobile/Comment.vue') },
  { path: '/mobile/chatlist', name: 'mobile_chatlist', component: page('mobile/ChatList.vue') },
  { path: '/mobile/chatbox', name: 'mobile_chatbox', component: page('mobile/ChatBox.vue') },
  { path: '/mobile/media/add', name: 'add_media', component: page('mobile/AddMedia.vue') },
  { path: '/mobile/media/edit', name: 'edit_media', props: true, component: page('mobile/EditMedia.vue') },
  { path: '/mobile/profile/edit', name: 'edit_profile', props: true, component: page('mobile/EditProfile.vue') },

  { path: '/portals/add', name: 'add_portal', component: page('AddPortal.vue') },
  { path: '/brand/add', name: 'add_brand', component: page('AddBrand.vue') },

  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  { path: '/contact-us', name: "contactus", component: page('Contact-us.vue')},

  { path: '/settings',
    component: page('settings/index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
    ] 
  },

  { path: '/admin',
    component: page('admin/Index.vue'),
    children: [
      { path: '', redirect: { name: 'admin.post' } },
      { path: 'post', name: 'admin.post', component: page('admin/post/Index.vue') },
      { path: 'post/:mode', name: 'admin.post_form', component: page('admin/post/Form.vue') },
      { path: 'category', name: 'admin.category', component: page('admin/Category.vue') },
    ] 
  },

  { path: '/dashboard', name: 'dashboard', component: page('Dashboard.vue') },
  { path: '/:username', name: 'profile', component: page('Profile.vue') },
]

export function createRouter () {
  return new Router({
    routes,
    scrollBehavior,
    mode: 'history'
  })
}
