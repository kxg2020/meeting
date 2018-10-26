<template>
  <div class="main">
    <div :class="sponsored ? 'main-container bt50' : 'main-container'">
      <div v-if="meetingRecordList.length > 0">
        <div class="meeting-list">
          <!--todo 待参加会议提醒-->
          <div v-for="(meetingItem, meetingIndex) in meetingRecordList" :key="meetingIndex" class="meeting-item">
            <div class="meeting-title" @click="toInfo(meetingItem.id)">
              {{meetingItem.title}}
            </div>
            <div class="meeting-noti">
              <span>主持人</span>
              <span>{{meetingItem.create_user_name}}</span>
            </div>
            <div class="meeting-noti">
              <span>开始时间</span>
              <span>{{meetingItem.start_time}}</span>
            </div>
            <div class="meeting-noti">
              <span>结束时间</span>
              <span>{{meetingItem.end_time}}</span>
            </div>
            <div class="meeting-edit">
              <el-button type="danger" size="mini" plain v-if="deleted" @click="deleteMeeting(meetingItem.id)">删除</el-button>
              <el-button type="primary" size="mini" plain @click="toInfo(meetingItem.id)">查看</el-button>
            </div>
          </div>
        </div>
        <div v-if="hasMore"  @click="getMeetingList" class="bottom-noti link">
          <span>查看更多</span>
        </div>
        <div v-else class="bottom-noti">
          <span>没有更多内容了</span>
        </div>
      </div>
      <div v-else>
        <Empty></Empty>
      </div>
    </div>
    <van-tabbar v-if="sponsored">
      <van-tabbar-item @click="toForm">
        <span>发起会议</span>
        <i slot="icon" slot-scope="props" class="fa fa-edit"></i>
      </van-tabbar-item>
    </van-tabbar>
  </div>
</template>

<script>
  import Empty from '../../components/Empty'
  export default {
    name: "MeetingRecordList",
    data () {
      return {
        meeting_type_id: 0,
        meetingType: {
          id: '',
          name: ''
        },
        page_size: 5,
        page: 1,
        total: 0,
        hasMore: false,
        meetingRecordList: [],
        sponsored: false,
        deleted: false,
      }
    },
    components: {
      Empty
    },
    created() {
      if (this.$route.params.type_id != undefined) {
        this.meeting_type_id = this.$route.params.type_id
        this.getMeetingList()
      } else {
        this.$router.replace('/')
      }
      this.getPermission()
    },
    methods: {
      getMeetingList () {
        let _this = this
        let type_id = this.meeting_type_id
        _this.axios.get('/meetingRecord/' + type_id + '/' + this.page + '/' + this.page_size).then(res => {
          _this.meetingRecordList = _this.meetingRecordList.concat(res.data.meetingRecords)
          _this.meetingType = res.data.meetingType
          _this.total = res.data.total
          if (Math.ceil(_this.total / _this.page_size) > _this.page){
            _this.hasMore = true
          } else {
            _this.hasMore = false
          }
          _this.page++
          window.setTitle(_this.meetingType.title)
        }).catch(err => {

        })
      },
      getPermission() {
        this.axios.get('/permission/' + this.meeting_type_id).then(res => {
          this.sponsored = res.data.sponsored
          this.deleted = res.data.delete
        }).catch(error => {})
      },
      back () {
        this.$router.back()
      },
      toForm() {
        if (!this.sponsored) {
          this.$toast('没有权限')
          return
        }
        this.$router.push({path: '/meeting_record/create/' + this.meeting_type_id})
      },
      toInfo(id) {
        this.$router.push({path: '/meeting_record/info/' + id})
      },
      deleteMeeting(id) {
        this.$dialog.confirm({
          title: '提示',
          message: '是否删除此会议'
        }).then(() => {
          this.axios.get('/meetingRecord/delete/' + id).then(res => {
            if (res.status) {
              this.$toast("删除成功")
              setTimeout(() => {
                this.page = 1
                this.total = 0
                this.hasMore = false
                this.meetingRecordList = []
                this.getMeetingList()
              }, 2000)
            } else {
              this.$toast(res.msg)
            }
          })
        }).catch(() => {
        })
      }
    }
  }
</script>

<style scoped>
  .main-container{

  }
  .bt50{
    position: fixed;
    width: 100%;
    height: calc(100% - 50px);
    bottom: 50px;
    overflow: scroll;
  }
  .meeting-item {
    margin: 15px;
    border: 1px solid #f1f4f8;
    border-radius: 5px;
    background-color: #FFFFFF;
  }

  .meeting-title {
    color: #333;
    line-height: 2em;
    font-size: 1.2em;
    text-align: center;
    margin: .5em 0;
  }
  .meeting-noti{
    margin: .5em 0;
    color: #666;
  }
  .meeting-noti span:nth-child(1){
    display: inline-block;
    text-align: center;
    width: 40%;
  }
  .meeting-noti span:nth-child(2){
    width: 60%;
  }
  .meeting-edit{
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    padding: 10px 0;
  }
  .meeting-edit button{
    margin-right: 20px;
  }
</style>