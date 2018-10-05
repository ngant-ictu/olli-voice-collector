import { Message } from 'element-ui'

export default function ({ store, app: { $axios, redirect } }) {
  $axios.onRequest(config => {
    if (store.getters.loggedToken !== null) {
      config.headers = {
        Authorization: 'Bearer ' + store.getters.loggedToken
      }
    }

    return config
  })

  $axios.onResponse(response => {
    return response
  })

  $axios.onError(error => {
    Message({
      showClose: true,
      message: error.response.data.errors.message,
      type: 'error',
      duration: 2 * 1000
    })
  })
}
