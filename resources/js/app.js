import Vue from 'vue'
import router from './router'
import axios from 'axios'
import vueAxios from 'vue-axios'
import store from './store'
Vue.use(vueAxios, axios)

import Vant from 'vant'
Vue.use(Vant)

import { Tag, input, button } from 'element-ui'

Vue.use(Tag)
Vue.use(input)
Vue.use(button)

Vue.axios.defaults.baseURL = '/api'

Vue.axios.defaults.headers.common = {
  'token': window.token,
}

// 请求拦截
Vue.axios.interceptors.request.use(
  config => {
    // todo add config
    return config
  },
  error => {
    return Promise.reject(error.response.data)
  }
)


// 响应拦截
Vue.axios.interceptors.response.use(
  response => {
    return response.data
  },
  error => {
    if (error.response) {
      console.log(error.response)
    }
    return Promise.reject(error.response.data)
  }
)

require('./utils/date.js')

import App from './App.vue'
Vue.component('App', App)

new Vue({
  el: '#app',
  template: '<App/>',
  router,
  store,
  components: {App}
}).$mount('#app')