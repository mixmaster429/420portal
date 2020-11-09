import axios from 'axios'
// state
export const state = () => ({
    news_data: '',
    news: '',
})

// getters
export const getters = {
    news_data: state => state.news_data,
    news: state => state.news,
}

// mutations
export const mutations = {
    FETCH_NEWS_DATA (state, data) {
        state.news_data = data
    },

    FETCH_NEWS (state, data) {
        state.news = data
    }
}

// actions
export const actions = {
    async fetchNewsData ({ commit }, category_id) {
        try {
            let url = `/marijuana-news`;
            let params = {category_id, category_id};
            const { data } = await axios.post(url, params);
            commit('FETCH_NEWS_DATA', data)
        } catch (e) {
            console.log(e)
        }
    },

    async fetchNews ({ commit }, slug) {
        try {
            let url = `/marijuana-news/${slug}`;
            const { data } = await axios.get(url);
            commit('FETCH_NEWS', data.post)
        } catch (e) {
            console.log(e)
        }
    }
}
