import Vue from 'vue'
import router from './router'
import axios from 'axios'
import vueAxios from 'vue-axios'
import store from './store'
Vue.use(vueAxios, axios)

import App from './App.vue'
Vue.component('App', App)

new Vue({
  el: '#app',
  template: '<App/>',
  router,
  store,
  components: {App}
}).$mount('#app')