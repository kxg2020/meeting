<template>
  <div class="main">
    <div class="meeting-record-info" v-if="info">
      <h2 class="info-title">{{info.meetingTitle}}</h2>
      <div class="info-create-user">
        <span>-</span>
        <!--<img class="create-user-avatar" :src="info.create_user_avatar" alt="">-->
        <span class="create-user-name">{{info.create_user}}</span>
        <span>-</span>
      </div>
      <div class="info-time">
        <i class="fa fa-clock-o"></i>
        <span class="time">{{info.start_time}}</span>
        <span>-</span>
        <span class="time">{{info.end_time}}</span>
      </div>
      <div class="issue-list">
        <div class="issue-item" v-for="(issue, issueIndex) in info.issue" :key="issueIndex" @click="toIssueInfo(issue.issue_id)">
          <h3 class="issue-title"><span>第{{(issueIndex + 1).ConvertToChinese()}}项、</span>{{issue.title}}</h3>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import FileList from '../../components/FileList'
  export default {
    name: "MettingRecordInfo",
    data() {
      return {
        info: null,
        voteSelects: [

        ]
      }
    },
    components: {
      FileList
    },
    created() {
      if (this.$route.params.id != undefined) {
        this.getInfo(this.$route.params.id)
      } else {
        this.$toast("参数错误")
        this.$router.back()
      }
      window.setTitle("会议详情")
    },
    methods: {
      getInfo(id) {
        let _this = this
        _this.axios.get('/meetingRecord/info/' + id).then(res => {
          _this.info = res.data
        }).catch(err => {})
      },
      toIssueInfo(issue_id) {
        this.$router.push({path: '/meeting_record/issue/' + issue_id})
      }
    }
  }
</script>

<style scoped>
  .meeting-record-info {
    padding: 0 15px;
  }

  .info-title {
    padding-top: .7em;
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.5em;
    line-height: 2em;
    text-align: center;
  }

  .info-create-user {
    padding-bottom: .7em;
    font-size: 14px;
    line-height: 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    flex-direction: row;
    justify-content: center;
  }

  .info-create-user * {
    margin: 0 2px;
  }

  .info-create-user span {
    color: rgb(149, 149, 149);
  }

  .create-user-name {
    color: #2c3e50;
  }

  .create-user-avatar {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1px solid rgb(149, 149, 149);
  }

  .info-time {
    padding: .5em 0;
    font-size: 14px;
    color: rgb(149, 149, 149);
  }

  .info-time .time {
    color: #2c3e50;
  }

  .issue-title {
    margin-bottom: .6em;
    padding: 15px 0;
    color: #2c3e50;
    font-size: 1.2em;
    font-weight: 600;
    border-bottom: 1px solid #ddd;
  }

  .issue-title span {
    color: #565656;
  }

  .issue-content{
    line-height: 1.6em;
    color: #1d1d1d;
    vertical-align: baseline;
  }

  .issue-images{
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .issue-image{
    width: 80%;
    height: auto;
    padding: 15px 0;
  }
  .issue-files{

  }
  .item-header{
    padding: .5em 0;
    font-size: 14px;
  }

  .file-header i, .file-header span{
    color: rgb(149, 149, 149);
    margin: 0 .5em;
    display: inline-block;
  }
  .file-header span {
    font-size: 14px;
  }
  .vote-item {
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 5px;
  }
  .vote-item:nth-child(n+1) {
    margin-top: 15px;
  }
</style>