import axios from 'axios'
// state
export const state = () => ({
  modal_data: '',
})

// getters
export const getters = {
  modal_data: state => state.modal_data,
}

// mutations
export const mutations = {
    GET_BRAND_MODAL_DATA (state, description) {
    state.modal_data = description
  },
}

// actions
export const actions = {
  async getBrandModalData ({ commit }) {
    try {
      let url = `/brand/get_modal_data`;
      const { data } = await axios.get(url)
      commit('GET_BRAND_MODAL_DATA', data)
    } catch (e) {
        console.log(e)
    }
  },  
}
