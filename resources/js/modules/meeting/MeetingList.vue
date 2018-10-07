<template>
  <div class="main">
    <van-nav-bar
      :title="meetingGroup.name"
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
        <div v-if="meetingList.length > 0" class="meeting-list">
          <div v-for="(meetingItem, meetingIndex) in meetingList" :key="meetingIndex" class="meeting-item">
            <div class="meeting-title">
              {{meetingItem.title}}
            </div>
            <div class="meeting-noti">
              <span>主持人</span>
              <span>{{meetingItem.username}}</span>
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
        <div v-else>
          <h1>todo Empty</h1>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
  export default {
    name: "MeetingList",
    data () {
      return {
        meetingGroup: {
          name: '党员大会'
        },
        meetingList: null,
        loadding: false
    }
    },
    created() {
      if (this.$route.params.group_id != undefined) {
        this.getMeetingList(this.$route.params.group_id)
      } else {
        this.$router.replace('/')
      }
    },
    methods: {
      getMeetingList () {
        let _this = this
        _this.loadding = true
        setTimeout(() => {
          _this.meetingList = [
            {
              id: 1,
              title: '关于XXX会议',
              username: 'XXX',
              start_time: '2018-10-7 15:49:47',
              end_time: '2018-11-7 15:50:14',
              view_count: 20
            },
            {
              id: 1,
              title: '关于XXX会议',
              username: 'XXX',
              start_time: '2018-10-7 15:49:47',
              end_time: '2018-11-7 15:50:14',
              view_count: 20
            },
          ]
          _this.loadding = false
        }, 2000)
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