import Notices from './Notices.vue'
import Notice from './Notice.vue'
export default [
  {
    name: 'Notice',
    path: '/notice/:id(\\d+)',
    component: Notice
  },
]

export let rootNotice  = {
  name: 'Notices',
  path: '/notices',
  component: Notices
}