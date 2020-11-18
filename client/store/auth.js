import axios from 'axios'
import Cookies from 'js-cookie'

// state
export const state = () => ({
  user: null,
  token: null,
  profile: null,
  map_location: null,
  message_count: 0,
  notification_count: 0,
  message_count: 0,
})

// getters
export const getters = {
  user: state => state.user,
  token: state => state.token,
  check: state => state.user !== null,
  profile: state => state.profile,
  map_location: state => state.map_location,
  message_count: state => state.message_count,
  notification_count: state => state.notification_count,
  message_count: state => state.message_count,
}

// mutations
export const mutations = {
  SET_TOKEN (state, token) {
    state.token = token
  },

  FETCH_USER_SUCCESS (state, user) {
    state.user = user
  },

  FETCH_USER_FAILURE (state) {
    state.token = null
  },

  LOGOUT (state) {
    state.user = null
    state.token = null
  },

  UPDATE_USER (state, { user }) {
    state.user = user
  },

  FETCH_PROFILE (state, data) {
    state.profile = data
  },

  FETCH_MAP_LOCATION (state, data) {
    state.map_location = data
  },

  SET_NOTIFICATION_COUNT(state, data) {
    state.notification_count = data
  },

  SET_MESSAGE_COUNT(state, data) {
    state.message_count = data
  }
}

// actions
export const actions = {
  saveToken ({ commit, dispatch }, { token, remember }) {
    commit('SET_TOKEN', token)

    Cookies.set('token', token, { expires: remember ? 365 : null })
  },

  async fetchUser ({ commit }) {
    try {
      const { data } = await axios.get('/user')
      commit('FETCH_USER_SUCCESS', data)
    } catch (e) {
      Cookies.remove('token')
      commit('FETCH_USER_FAILURE')
    }
  },

  updateUser ({ commit }, payload) {
    commit('UPDATE_USER', payload)
  },

  async logout ({ commit }) {
    try {
      await axios.post('/logout')
    } catch (e) { }

    Cookies.remove('token')

    commit('LOGOUT')

    window.location.reload();
  },

  async fetchOauthUrl (ctx, { provider }) {
    const { data } = await axios.post(`/oauth/${provider}`)

    return data.url
  },

  async fetchProfile ({ commit }, slug) {
    try {
      let url = `/get_profile/${slug}`;
      const { data } = await axios.get(url)
      if(data.status == 200) {
        commit('FETCH_PROFILE', data.profile)
      }      
    } catch (e) {
        console.log(e)
    }
  },

  async fetchMapLocation({commit}, location) {
    let url = `/medical-recreational-marijuana-dispensary-delivery`;
    let params = {state: location.state, city: location.city};
    const {data} = await axios.post(url, params);
    commit('FETCH_MAP_LOCATION', data)
  },

  async getUnreadNotification({ commit, state }) {
    if(state.user) {
      let user_id = state.user.id;
      let params = { id: user_id };
      const { data } = await axios.post('/notification/get_unreads', params);
      commit('SET_NOTIFICATION_COUNT', data);
    }
  },

  async getUnreadMessage({ commit, state }) {
    if(state.user) {
      let user_id = state.user.id;
      let params = { id: user_id };
      const { data } = await axios.post('/user/get_unreads', params);
      commit('SET_MESSAGE_COUNT', data);
    }
  }
}
