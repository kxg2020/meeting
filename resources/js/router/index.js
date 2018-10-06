import Vue from 'vue'
import Router from 'vue-router'
import Index from '../modules/index'
Vue.use(Router)

// import store from '../store'

const router = new Router({
  mode: 'history',
  routes: [
    ...Index
  ]
})
// 路由监听
router.beforeEach((to, from, next) => {
  next()
})
export default router