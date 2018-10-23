import Vue from 'vue'
import router from './router'
import axios from 'axios'
import vueAxios from 'vue-axios'
import store from './store'

Vue.use(vueAxios, axios)

import Vant from 'vant'

Vue.use(Vant)

import {Tag, Input, Button, Radio, RadioGroup, RadioButton, Checkbox, CheckboxButton, CheckboxGroup} from 'element-ui'

Vue.use(Tag)
Vue.use(Input)
Vue.use(Button)
Vue.use(Radio)
Vue.use(RadioGroup)
Vue.use(RadioButton)
Vue.use(Checkbox)
Vue.use(CheckboxButton)
Vue.use(CheckboxGroup)

Vue.axios.defaults.baseURL = '/api'

Vue.axios.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest',
  'token': window.token,
}

import { showFullScreenLoading, tryHideFullScreenLoading} from "./utils/httpLoading";

// 请求拦截
Vue.axios.interceptors.request.use(
  config => {
    showFullScreenLoading()
    return config
  },
  error => {
    return Promise.reject(error.response.data)
  }
)


// 响应拦截
Vue.axios.interceptors.response.use(
  response => {
    tryHideFullScreenLoading()
    return response.data
  },
  error => {
    tryHideFullScreenLoading()
    if (error.response) {
      console.log(error.response)
    }
    return Promise.reject(error.response.data)
  }
)

require('./utils/date.js')
require('./utils/function.js')
if (process.env.NODE_ENV == 'development') {
  const eruda = require('../../node_modules/eruda/eruda.min.js')
  eruda.init()
}

import App from './App.vue'

Vue.component('App', App)

new Vue({
  el: '#app',
  template: '<App/>',
  router,
  store,
  components: {App}
}).$mount('#app')