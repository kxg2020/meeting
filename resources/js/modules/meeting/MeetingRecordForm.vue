<template>
  <div class="main">
    <van-nav-bar
      title="发布会议"
      left-text="返回"
      left-arrow
      @click-left="back()"
    />
    <div class="main-container main-container-center">
      <van-cell-group>
        <van-field
          v-model="model.title"
          required
          clearable
          label="主题"
          placeholder="请输入会议主题"
        />
        <van-cell title="开始时间" @click="showStartTimeDialog">
          <span v-if="model.start_time">{{model.start_time}}</span>
          <span class="placeholder-text" v-else>请选择</span>
        </van-cell>
        <van-cell title="结束时间" @click="showEndTimeDialog">
          <span v-if="model.end_time">{{model.end_time}}</span>
          <span class="placeholder-text" v-else>请选择</span>
        </van-cell>
      </van-cell-group>
      <div v-for="(issue, issueIndex) in model.issue_list" :key="issueIndex">
        <h2 class="cell-group-title">议题{{issueIndex + 1}}</h2>
        <van-cell-group>
          <van-field
            v-model="issue.title"
            required
            clearable
            label="议题标题"
            placeholder="请输入议题标题"
          />
          <van-field
            v-model="issue.content"
            type="textarea"
            clearable
            autosize
            label="议题内容"
            placeholder="请输入议题内容"
            class="issue-content"
          />
          <div class="upload-list">
            <div class="upload-item" v-for="(image, imageIndex) in issue.images" :key="issueIndex + '-' + imageIndex">
              <img :src="image" alt="">
            </div>
            <div class="upload-item upload-btn">
              <i class="fa fa-camera"></i>
            </div>
          </div>
          <div class="issue-file-list">
            <div class="cell-title">
              附件
            </div>
            <div v-for="(file, fileIndex) in issue.files" :key="issueIndex + '-' + fileIndex" class="file-item">
              <div>{{file.name}}</div>
            </div>
            <div class="upload-item upload-btn">
              <i class="fa fa-paperclip"></i>
            </div>
          </div>
        </van-cell-group>
      </div>
      <van-cell-group>
        <van-cell title="增加议题" icon="add-o" style="color: #38f;" @click="addIssue"/>
      </van-cell-group>
    </div>
    <div></div>
    <van-popup v-model="showStartTime" position="bottom">
      <van-datetime-picker
        type="datetime"
        :min-date="new Date()"
        @confirm="startTimeConfirm"
        @cancel="hideTimeDialog"
      />
    </van-popup>
    <van-popup v-model="showEndTime" position="bottom">
      <van-datetime-picker
        type="datetime"
        :min-date="new Date()"
        @confirm="endTimeConfirm"
        @cancel="hideTimeDialog"
      />
    </van-popup>
  </div>
</template>

<script>
  export default {
    name: "MeetingRecordForm",
    data() {
      return {
        model: {
          title: '',
          start_time: '',
          end_time: '',
          issue_list: []
        },
        issueTemplate: {
          title: '',
          content: '',
          images: [],
          files: []
        },
        voteTemplate: {
          title: '',
          items: []
        },
        showStartTime: false,
        showEndTime: false
      }
    },
    created() {
      this.addIssue()
    },
    methods: {
      showStartTimeDialog() {
        this.showStartTime = true
      },
      showEndTimeDialog() {
        this.showEndTime = true
      },
      startTimeConfirm(value) {
        this.model.start_time = value.Format('yyyy-MM-dd hh:mm:ss')
        this.hideTimeDialog()
      },
      endTimeConfirm(value) {
        this.model.end_time = value.Format('yyyy-MM-dd hh:mm:ss')
        this.hideTimeDialog()
      },
      hideTimeDialog(){
        this.showStartTime = false
        this.showEndTime = false
      },
      addIssue() {
        this.model.issue_list.push(this.issueTemplate)
      },
      back () {
        this.$router.back()
      }
    }
  }
</script>

<style scoped>
  .van-cell-group{
    margin-bottom: 10px;
  }
  .cell-group-title{
    margin: 0;
    font-weight: 400;
    font-size: 14px;
    color: rgba(69,90,100,.6);
    padding: 20px 15px 15px;
  }
  .placeholder-text{
    color: #757575;
  }
  .upload-btn{
    display: inline-block;
    width: 50px;
    height: 50px;
    text-align: center;
    font-size: 25px;
    color: #ccc;
    border: 1px dashed #ccc;
    line-height: 50px;
    border-radius: 5px;
  }
  .upload-list{
    margin-left: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
  }
  .issue-file-list{
    margin-left: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
  }
  .cell-title{
    margin: 0;
    font-weight: 400;
    color: #333;
    font-size: 14px;
    line-height: 24px;
    padding: 10px 0 15px;
  }
  .file-item {

  }
</style>
<style>
  .van-cell .van-cell__title {
    max-width: 90px;
  }
  .van-cell__value{
    text-align: left;
  }
  .issue-content::after{
    border-bottom: unset;
  }
</style>