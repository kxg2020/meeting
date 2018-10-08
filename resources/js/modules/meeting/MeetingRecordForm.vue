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
        <van-cell class="form-item" title="开始时间" @click="showStartTimeDialog">
          <span v-if="model.start_time">{{model.start_time.Format('yyyy-MM-dd hh:mm:ss')}}</span>
          <span class="placeholder-text" v-else>请选择</span>
        </van-cell>
        <van-cell class="form-item" title="结束时间" @click="showEndTimeDialog">
          <span v-if="model.end_time">{{model.end_time.Format('yyyy-MM-dd hh:mm:ss')}}</span>
          <span class="placeholder-text" v-else>请选择</span>
        </van-cell>
      </van-cell-group>
      <div v-for="(issue, issueIndex) in model.issue_list" :key="issueIndex">
        <div class="cell-group-title">
          <span>{{issue.political_name}}</span>
          <i class="fa fa-minus-square" @click="removeIssue(issueIndex)"></i>
        </div>
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
          <div class="form-item issue-file-list">
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
          <van-cell title="投票" v-if="['bj', 'tp'].includes(issue.political_short_name)">
            <div slot="right-icon" @click="addVote(issueIndex)">
              <i class="fa fa-plus-square"></i>
            </div>
          </van-cell>
          <div class="form-item issue-vote-list">
            <div v-for="(vote, voteIndex) in issue.votes" :key="voteIndex">
              <van-field
                v-model="vote.title"
                required
                clearable
                label="投票标题"
                placeholder="请输入投票标题"
              >
                <div slot="button" @click="removeVote(issueIndex, voteIndex)">
                  <i class="fa fa-minus-square"></i>
                </div>
              </van-field>
              <van-cell title="投票选项">
                <template v-if="issue.political_short_name == 'tp'">
                  <el-tag
                    class="vote-item"
                    :key="issueIndex + voteIndex + voteItemIndex"
                    v-for="(voteItem, voteItemIndex) in vote.items"
                    closable
                    :disable-transitions="false"
                    @close="handleCloseVoteItem(issueIndex, voteIndex, voteItemIndex)">
                    {{voteItem}}
                  </el-tag>
                  <el-input
                    class="vote-item input-vote-item"
                    v-if="vote.inputVisible"
                    v-model="vote.inputValue"
                    :ref="'saveVoteInput' + issueIndex + voteIndex"
                    size="small"
                    @keyup.enter.native="handleInputConfirmVoteItem(issueIndex, voteIndex)"
                    @blur="handleInputConfirmVoteItem(issueIndex, voteIndex)"
                  >
                  </el-input>
                  <el-button v-else class="vote-item" size="small" @click="showInputVoteItem(issueIndex, voteIndex)">+ 投票选项</el-button>
                </template>
                <template v-if="issue.political_short_name == 'bj'">
                  <el-tag
                    class="vote-item"
                    :key="issueIndex + voteIndex + voteItemIndex"
                    v-for="(voteItem, voteItemIndex) in vote.items"
                    :disable-transitions="false">
                    {{voteItem}}
                  </el-tag>
                </template>
              </van-cell>
            </div>
          </div>
        </van-cell-group>
      </div>
      <van-cell-group>
        <van-cell title="增加议题" icon="add-o" style="color: #38f;" @click="addIssue"/>
      </van-cell-group>
    </div>
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
        :min-date="model.end_time"
        @confirm="endTimeConfirm"
        @cancel="hideTimeDialog"
      />
    </van-popup>
    <van-dialog
      v-model="politicalDialogShow"
      show-cancel-button
      @confirm="createIssue"
      @cancel="hidePoliticalDialog"
    >
      <div class="van-dialog__header">请选择议题类型</div>
      <div class="issue-select">
        <el-radio v-model="politicalSelect" :label="politicalIndex" v-for="(political, politicalIndex) in politicalList" :key="politicalIndex">{{political.name}}</el-radio>
      </div>
    </van-dialog>
  </div>
</template>

<script>
  export default {
    name: "MeetingRecordForm",
    data() {
      return {
        model: {
          title: '',
          start_time: new Date(),
          end_time: new Date((new Date).getTime() + 1 * 24 * 60 * 60 * 1000),
          issue_list: []
        },
        showStartTime: false,
        showEndTime: false,
        politicalList: [],
        politicalSelect: 0,
        politicalDialogShow: false
      }
    },
    created() {
      this.getPoliticalList()
    },
    methods: {
      getPoliticalList() {
        let _this = this
        _this.axios.get('/political').then(res => {
          _this.politicalList = res.data
        }).catch(error => {

        })
      },
      showStartTimeDialog() {
        this.showStartTime = true
      },
      showEndTimeDialog() {
        this.showEndTime = true
      },
      startTimeConfirm(value) {
        this.model.start_time = value
        this.hideTimeDialog()
      },
      endTimeConfirm(value) {
        this.model.end_time = value
        this.hideTimeDialog()
      },
      hideTimeDialog(){
        this.showStartTime = false
        this.showEndTime = false
      },
      addIssue() {
        this.politicalDialogShow = true
      },
      createIssue() {
        this.model.issue_list.push({
          title: '',
          political_id: this.politicalList[this.politicalSelect].id,
          political_name: this.politicalList[this.politicalSelect].name,
          political_short_name: this.politicalList[this.politicalSelect].short_name,
          content: '',
          images: [],
          files: [],
          votes: []
        })
        this.hidePoliticalDialog()
      },
      hidePoliticalDialog() {
        this.politicalDialogShow = false
      },
      removeIssue(issueIndex) {
        this.$dialog.confirm({
          title: '提示',
          message: '是否移除此议题'
        }).then(() => {
          this.model.issue_list.splice(issueIndex, 1)
        }).catch(() => {

        })
      },
      addVote(issueIndex) {
        let items = []
        if (this.model.issue_list[issueIndex].political_short_name == 'tp') {
          items = []
        } else if (this.model.issue_list[issueIndex].political_short_name == 'bj') {
          items = ['同意票', '反对票', '弃权票']
        } else {
          this.$toast("此议题不支持投票")
          return
        }
        this.model.issue_list[issueIndex].votes.push({
          title: '',
          inputVisible: false,
          inputValue: '',
          items: items
        })
      },
      removeVote(issueIndex, voteIndex) {
        this.$dialog.confirm({
          title: '提示',
          message: '是否移除此投票'
        }).then(() => {
          this.model.issue_list[issueIndex].votes.splice(voteIndex, 1)
        }).catch(() => {

        })
      },
      handleCloseVoteItem(issueIndex, voteIndex, voteItemIndex) {
        this.model.issue_list[issueIndex].votes[voteIndex].items.splice(voteItemIndex, 1)
      },
      handleInputConfirmVoteItem(issueIndex, voteIndex){
        let inputValue = this.model.issue_list[issueIndex].votes[voteIndex].inputValue
        if (inputValue && !this.model.issue_list[issueIndex].votes[voteIndex].items.includes(inputValue)) {
          this.model.issue_list[issueIndex].votes[voteIndex].items.push(inputValue)
        }
        this.model.issue_list[issueIndex].votes[voteIndex].inputVisible = false
        this.model.issue_list[issueIndex].votes[voteIndex].inputValue = ''
      },
      showInputVoteItem(issueIndex, voteIndex) {
        this.model.issue_list[issueIndex].votes[voteIndex].inputVisible = true
        this.$nextTick(_ => {
          this.$refs['saveVoteInput' + issueIndex + voteIndex][0].$refs.input.focus()
        })
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
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    font-weight: 400;
    font-size: 14px;
    color: rgba(69,90,100,.6);
    padding: 20px 15px 15px;
  }
  .cell-group-title span, .cell-group-title i{
    display: inline-block;
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
  .input-vote-item{
    max-width: 150px;
  }
  .vote-item{
    margin-right: 10px;
    margin-bottom: 10px;
  }
  .issue-select{
    width: 100%;
    margin-top: 15px;
    margin-bottom: 15px;
    display: flex;
    flex-direction: row;
    justify-content: center;
  }
</style>
<style>
  .form-item .van-cell__title {
    max-width: 90px;
  }
  .form-item .van-cell__value{
    text-align: left;
  }
  .issue-content::after{
    border-bottom: unset;
  }
  .van-cell--required {
    overflow: hidden;
  }
</style>