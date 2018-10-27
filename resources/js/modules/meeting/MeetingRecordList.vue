<template>
  <div class="main">
    <div :class="sponsored ? 'main-container bt50' : 'main-container'">
      <div v-if="meetingRecordList.length > 0">
        <div class="meeting-list">
          <!--todo 待参加会议提醒-->
          <div v-for="(meetingItem, meetingIndex) in meetingRecordList" :key="meetingIndex" class="meeting-item" @click="toInfo(meetingItem.id)">
            <div class="meeting-title">
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
      <van-tabbar-item>
        <span>按钮</span>
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
        sponsored: false
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
          if (res.status) {
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
          } else {
            _this.$toast(res.msg)
          }
        }).catch(err => {

        })
      },
      getPermission() {
        this.axios.get('/permission/' + this.meeting_type_id).then(res => {
          this.sponsored = res.data.sponsored
        }).catch(error => {})
      },
      back () {
        this.$router.back()
      },
      toForm() {
        this.$router.push({path: '/meeting_record/create/' + this.meeting_type_id})
      },
      toInfo(id) {
        this.$router.push({path: '/meeting_record/info/' + id})
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
    height: 150px;
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
</style>