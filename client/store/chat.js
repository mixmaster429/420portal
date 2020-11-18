import axios from 'axios'
// state
export const state = () => ({
    flatchat: false,
    chat_users: [],
})

// getters
export const getters = {
    flatchat: state => state.flatchat,
    chat_users: state => state.chat_users,
}

// mutations
export const mutations = {
    TOGGLE_FLATCHAT(state) {
        state.flatchat = !state.flatchat;
    },
    PUSH_CHAT_USER (state, user) {
        for (let i = 0; i < state.chat_users.length; i++) {
            if (state.chat_users[i]['to'] === user.to && state.chat_users[i]['from'] === user.from) return false;
        }
        state.chat_users.push(user);
    },
    REMOVE_CHAT_USER (state, user) {
        let index = state.chat_users.findIndex(chat_user => {
            return chat_user.from == user.from && chat_user.to == user.to
        });
        state.chat_users.splice(index, 1);
    },
}

// actions
export const actions = {
    toggleFlatchat({ commit }) {
        commit('TOGGLE_FLATCHAT');
    },
    openChatBox ({ commit }, user) {
        commit('PUSH_CHAT_USER', user);
    },
    closeChatBox({ commit }, user) {
        commit('REMOVE_CHAT_USER', user)
    }
}
