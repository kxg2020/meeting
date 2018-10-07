<template>
  <div class="main">
    <van-nav-bar
      :title="meetingType.title"
      left-text="返回"
      left-arrow
      @click-left="back()"
    />
    <div class="main-container main-container-bottom">
      <template v-if="loadding">
        <div style="display: flex;justify-content: center;">
          <van-loading type="spinner"/>
        </div>
      </template>
      <template v-else>
        <div v-if="meetingRecordList.length > 0" class="meeting-list">
          <div v-for="(meetingItem, meetingIndex) in meetingRecordList" :key="meetingIndex" class="meeting-item">
            <div class="meeting-title">
              {{meetingItem.title}}
            </div>
            <div class="meeting-noti">
              <span>主持人</span>
              <span>{{meetingItem.create_user_id}}</span>
            </div>
            <div class="meeting-noti">
              <span>开始时间</span>
              <span>{{meetingItem.create_time}}</span>
            </div>
            <div class="meeting-noti">
              <span>结束时间</span>
              <span>{{meetingItem.create_time}}</span>
            </div>
          </div>
        </div>
        <div v-else>
          <h1>todo Empty</h1>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
  export default {
    name: "MeetingRecordList",
    data () {
      return {
        meetingType: {
          id: '',
          name: ''
        },
        meetingRecordList: null,
        loadding: false
    }
    },
    created() {
      if (this.$route.params.type_id != undefined) {
        this.getMeetingList(this.$route.params.type_id)
      } else {
        this.$router.replace('/')
      }
    },
    methods: {
      getMeetingList (type_id) {
        let _this = this
        _this.loadding = true
        _this.axios.get('/meeting_record_list/' + type_id).then(res => {
          _this.meetingRecordList = res.data.meeting_records
          _this.meetingType = res.data.meeting_type
          _this.loadding = false
        }).catch(err => {

        })
      },
      back () {
        this.$router.back()
      }
    }
  }
</script>

<style scoped>
  .meeting-item {
    margin: 15px;
    height: 150px;
    border: 1px solid #f1f4f8;
    border-radius: 5px;
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