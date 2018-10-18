import Notices from './Notices.vue'
import Notice from './Notice.vue'
export default [
  {
    name: 'Notices',
    path: '/notices',
    component: Notices
  },
  {
    name: 'Notice',
    path: '/notice/:id(\\d+)',
    component: Notice
  },
]