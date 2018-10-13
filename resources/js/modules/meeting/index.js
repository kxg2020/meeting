import MeetingRecordList from './MeetingRecordList.vue'
import MeetingRecordForm from './MeetingRecordForm.vue'
import MeetingRecordInfo from './MettingRecordInfo.vue'
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
  {
    name: 'MeetingRecordInfo',
    path: '/meeting_record/info/:id(\\d+)',
    component: MeetingRecordInfo
  },
]