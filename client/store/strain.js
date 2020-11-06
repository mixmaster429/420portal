import axios from 'axios'
// state
export const state = () => ({
  data: null,
  modal_data: '',
})

// getters
export const getters = {
  data: state => state.data,
  modal_data: state => state.modal_data,
}

// mutations
export const mutations = {
  FETCH_STRAIN_DATA (state, data) {
    state.data = data
  },
  GET_MODAL_DATA (state, description) {
    state.modal_data = description
  }
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
}
