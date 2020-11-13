import axios from 'axios'
// state
export const state = () => ({
    news_data: '',
    news: '',
    media: null,
    forum: null,
    topic: null,
})

// getters
export const getters = {
    news_data: state => state.news_data,
    news: state => state.news,
    media: state => state.media,
    forum: state => state.forum,
    topic: state => state.topic,
}

// mutations
export const mutations = {
    FETCH_NEWS_DATA (state, data) {
        state.news_data = data
    },

    FETCH_NEWS (state, data) {
        state.news = data
    },

    FETCH_MEDIA(state, data) {
        state.media = data
    },

    FETCH_FORUM(state, data) {
        state.forum = data
    },

    FETCH_TOPIC(state, data) {
        state.topic = data
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
    },

    async fetchMedia({commit}, id) {
        try {
            let uri = `/media/show/${id}`;
            const { data } = await axios.post(uri);
            commit ('FETCH_MEDIA', data);
        } catch (e) {
            console.log(e);
        }
    },

    async fetchForum({commit}, id) {
        try {
            let uri = `/marijuana-forums/${id}`;
            const { data } = await axios.get(uri);
            commit ('FETCH_FORUM', data);
        } catch (e) {
            console.log(e);
        }
    },

    async fetchTopic({commit}, id) {
        try {
            let uri = `/topic/detail/${id}`;
            const { data } = await axios.get(uri);
            commit ('FETCH_TOPIC', data);
        } catch (e) {
            console.log(e);
        }
    }
}
