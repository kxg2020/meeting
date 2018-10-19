import Vue from 'vue'
import Router from 'vue-router'
import Root from '../modules/Root.vue'
import {rootIndex} from '../modules/index'
import Meeting from '../modules/meeting'
import {rootNotice} from '../modules/notice'
import {rootMember} from '../modules/member'
Vue.use(Router)

// import store from '../store'

const router = new Router({
  // mode: 'history',
  routes: [
    {
      path: '/',
      redirect: '/index',
      component: Root,
      children: [
        rootIndex,
        rootNotice,
        rootMember
      ]
    },
    ...Meeting,
  ]
})
// 路由监听
router.beforeEach((to, from, next) => {
  next()
})
export default router