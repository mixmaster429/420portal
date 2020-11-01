import axios from 'axios'

// state
export const state = () => ({
  data: null,
})

// getters
export const getters = {
  data: state => state.data,
}

// mutations
export const mutations = {
  FETCH_STRAIN_DATA (state, data) {
    state.data = data
  },
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
}
