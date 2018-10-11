import MeetingRecordList from './MeetingRecordList.vue'
import MeetingRecordForm from './MeetingRecordForm.vue'
export default [
  {
    name: 'MeetingRecordList',
    path: '/meeting_record/list/:type_id(\\d+)',
    component: MeetingRecordList
  },
  {
    name: 'MeetingRecordCreate',
    path: '/meeting_record/create/:type_id(\\d+)',
    component: MeetingRecordForm
  },
]