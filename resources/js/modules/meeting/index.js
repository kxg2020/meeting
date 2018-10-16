import MeetingRecordList from './MeetingRecordList.vue'
import MeetingRecordForm from './MeetingRecordForm.vue'
import MeetingRecordInfo from './MettingRecordInfo.vue'
import MeetingRecordIssue from './MeetingRecordIssue.vue'
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
  {
    name: 'MeetingRecordIssue',
    path: '/meeting_record/issue/:id(\\d+)',
    component: MeetingRecordIssue
  },
]