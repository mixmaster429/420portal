import axios from 'axios'
// state
export const state = () => ({
  data: null,
  modal_data: '',
  // default_media: null,
})

// getters
export const getters = {
  data: state => state.data,
  modal_data: state => state.modal_data,
  // default_media: state => state.default_media,
}

// mutations
export const mutations = {
  FETCH_STRAIN_DATA (state, data) {
    state.data = data
  },
  GET_MODAL_DATA (state, description) {
    state.modal_data = description
  },
  // FETCH_DEFAULT_MEDIA (state, data) {
  //   state.default_media = data;
  // }
}

// actions
export const actions = {
  async fetchStrainData ({ commit }, slug) {
    try {
      let url = `/get_strain/${slug}`;
      const { data } = await axios.get(url)
      commit('FETCH_STRAIN_DATA', data)
    } catch (e) {
        console.log(e)
    }
  },

  async getModalData ({ commit }) {
    try {
      let url = `/strain/get_modal_data`;
      const { data } = await axios.get(url)
      commit('GET_MODAL_DATA', data)
    } catch (e) {
        console.log(e)
    }
  },  

  // async fetchDefaultMedia({commit}, params) {
  //   try {
  //     let url = '/get_default_media';
  //     const { data } = await axios.post(url, params)
  //     commit('FETCH_DEFAULT_MEDIA', data)
  //   } catch (e) {
  //       console.log(e)
  //   }
  // }
}
