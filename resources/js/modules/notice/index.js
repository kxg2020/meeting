import Notices from './Notices.vue'
import Notice from './Notice.vue'
import NoticeForm from './NoticeForm.vue'
export default [
  {
    name: 'Notice',
    path: '/notice/:id(\\d+)',
    component: Notice
  },
  {
    name: 'NoticeForm',
    path: '/notice/create',
    component: NoticeForm
  },
]

export let rootNotice  = {
  name: 'Notices',
  path: '/notices',
  component: Notices
}