import MeetingList from './MeetingList.vue'
export default [
  {
    name: 'MeetingList',
    path: '/meeting_list/:group_id(\\d+)',
    component: MeetingList
  },
]