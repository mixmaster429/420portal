import Vue from 'vue'
import Router from 'vue-router'
import { scrollBehavior } from '~/utils'

Vue.use(Router)

const page = path => () => import(`~/pages/${path}`).then(m => m.default || m)

const routes = [
  { path: '/', name: 'home', component: page('Home.vue') },
  { path: '/mobile/slideshow', name: 'weedgram', component: page('mobile/Weedgram.vue') },
  { path: '/mobile/comment', name: 'comment', component: page('mobile/Comment.vue') },

  { path: '/marijuana-strains', name: 'strain_index', component: page('Strain.vue') },
  { path: '/marijuana-strains/:strain', name: 'strain_detail', component: page('StrainDetail.vue') },
  { path: '/medical-recreational-marijuana-dispensary-delivery/:state?/:city?', name: 'map_page', component: page('MapPage.vue')},

  { path: '/mobile/media/edit', name: 'edit_media', component: page('media/EditMedia.vue') },

  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  // { path: '/settings',
  //   component: page('settings/index.vue'),
  //   children: [
  //     { path: '', redirect: { name: 'settings.profile' } },
  //     { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
  //     { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
  //   ] 
  // },
  { path: '/:username', name: 'profile', component: page('Profile.vue') },
]

export function createRouter () {
  return new Router({
    routes,
    scrollBehavior,
    mode: 'history'
  })
}
