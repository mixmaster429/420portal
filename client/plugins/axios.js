import Vue from 'vue'
import axios from 'axios'
import swal from 'sweetalert2'

process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

export default ({ app, store, redirect }) => {
  axios.defaults.baseURL = process.env.apiUrl

  if (process.server) {
    return
  }

  // Request interceptor
  axios.interceptors.request.use((request) => {
    request.baseURL = process.env.apiUrl

    const token = store.getters['auth/token']

    if (token) {
      request.headers.common.Authorization = `Bearer ${token}`
    }

    return request
  })

  // Response interceptor
  axios.interceptors.response.use(response => response, (error) => {
    const { status } = error.response || {}

    if (status >= 500) {
      swal({
        type: 'error',
        title: 'Oops...',
        text: 'Something went wrong! Please try again.',
        reverseButtons: true,
        confirmButtonText: 'Ok',
        cancelButtonText: 'Cancel'
      })
    }

    if (status === 401 && store.getters['auth/check']) {
      swal({
        type: 'warning',
        title: 'Session Expired!',
        text: 'Please log in again to continue.',
        reverseButtons: true,
        confirmButtonText: 'Ok',
        cancelButtonText: 'Cancel'
      }).then(() => {
        store.commit('auth/LOGOUT')

        redirect({ name: 'login' })
      })
    }

    return Promise.reject(error)
  })
}
