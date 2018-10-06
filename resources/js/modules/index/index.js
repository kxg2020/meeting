export default [
  {
    path: '/',
    redirect: '/index'
  },
  {
    name: 'Index',
    path: '/index',
    component: () => import('./Index.vue')
  },
]