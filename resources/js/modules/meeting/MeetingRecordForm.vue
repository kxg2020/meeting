<template>
  <div class="main">
    <div class="main-container">
      <div v-if="model.meeting_image_url" class="meeting-image" >
        <img :src="model.meeting_image_url"/>
      </div>
      <van-cell-group>
        <van-cell class="form-item" title="会议主题">
          <div @click="showCountDialog">
            {{model.title}}
          </div>
        </van-cell>
        <van-cell class="form-item" title="开始时间">
          <DateTimePacker :min-date="new Date()" @change="value => changeStartTime(value)"></DateTimePacker>
        </van-cell>
        <van-cell class="form-item" title="结束时间">
          <DateTimePacker :min-date="minDate" @change="value => changeEndTime(value)"></DateTimePacker>
        </van-cell>
      </van-cell-group>
      <div v-for="(issue, issueIndex) in model.issue_list" :key="issueIndex">
        <div class="cell-group-title">
          <span>议题{{(issueIndex + 1).ConvertToChinese()}}（{{issue.political_name}}）</span>
          <i class="fa fa-times-rectangle" @click="removeIssue(issueIndex)"></i>
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
            <FileList :files="issue.images" @remove="index => removeIssueImage(issueIndex, index)"></FileList>
            <Upload :multiple="true" accept="image/*"
                    @success="data => uploadIssueImageSuccess(issueIndex, data)"></Upload>
          </div>
          <div class="form-item issue-file-list">
            <div class="cell-title">
              附件
            </div>
            <FileList :files="issue.files" @remove="index => removeIssueFile(issueIndex, index)"></FileList>
            <Upload icon="fa fa-paperclip" :multiple="true"
                    @success="data => uploadIssueFileSuccess(issueIndex, data)"></Upload>
          </div>
          <van-cell :title="issue.political_short_name == 'bj' ? '发起表决' : '发起投票'"
                    v-if="issue.political_short_name != 'yz'">
            <div slot="right-icon" @click="addVote(issueIndex)">
              <i class="fa fa-plus-square"></i>
            </div>
          </van-cell>
          <div class="form-item issue-vote-list">
            <div v-for="(vote, voteIndex) in issue.votes" :key="voteIndex">
              <div class="cell-group-title">
                <span>{{issue.political_short_name == 'bj' ? '表决' : '投票'}}{{(voteIndex + 1).ConvertToChinese()}}</span>
                <i class="fa fa-minus-square" @click="removeVote(issueIndex, voteIndex)"></i>
              </div>
              <van-field
                v-model="vote.title"
                required
                clearable
                :label="issue.political_short_name == 'bj' ? '表决标题' : '投票标题'"
                :placeholder="issue.political_short_name == 'bj' ? '请输入表决标题' : '请输入投票标题'"
              >
                <div slot="button" @click="removeVote(issueIndex, voteIndex)">
                  <i class="fa fa-minus-square"></i>
                </div>
              </van-field>
              <van-cell :title="issue.political_short_name == 'bj' ? '表决选项' : '投票选项'">
                <template v-if="issue.political_short_name == 'tp'">
                  <div class="vote-item" v-for="(voteItem, voteItemIndex) in vote.items">
                    <el-tag
                      class="vote-item-tag"
                      :key="issueIndex + voteIndex + voteItemIndex"
                      closable
                      :disable-transitions="false"
                      @close="handleCloseVoteItem(issueIndex, voteIndex, voteItemIndex)">
                      {{voteItem.value}}
                    </el-tag>
                    <div class="vote-file-list">
                      <FileList :files="voteItem.files"
                                @remove="index => remoVoteFile(issueIndex, voteIndex, voteItemIndex, index)"></FileList>
                      <Upload icon="fa fa-paperclip" accept="image/*, audio/*, video/*, application/*" :multiple="true"
                              @success="data => uploadVoteFileSuccess(issueIndex, voteIndex, voteItemIndex, data)"></Upload>
                    </div>
                  </div>
                  <el-input
                    class="vote-item-tag input-vote-item-tag"
                    v-if="vote.inputVisible"
                    v-model="vote.inputValue"
                    :ref="'saveVoteInput' + issueIndex + voteIndex"
                    size="small"
                    @keyup.enter.native="handleInputConfirmVoteItem(issueIndex, voteIndex)"
                    @blur="handleInputConfirmVoteItem(issueIndex, voteIndex)"
                  >
                  </el-input>
                  <el-button v-else class="vote-item-tag btn-vote-item-tag" size="small"
                             @click="showInputVoteItem(issueIndex, voteIndex)">+ 投票选项
                  </el-button>
                </template>
                <template v-if="issue.political_short_name == 'bj'">
                  <el-tag
                    class="vote-item-tag just-tag"
                    :key="issueIndex + voteIndex + voteItemIndex"
                    v-for="(voteItem, voteItemIndex) in vote.items"
                    :disable-transitions="false">
                    {{voteItem.value}}
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
      <van-cell-group>
        <!--<van-cell class="form-item" title="参会组织">-->
        <!--<span @click="showUserInvitationDialog">{{model.user_invitation_name}}</span>-->
        <!--</van-cell>-->
        <van-cell class="form-item" title="参会组织">
          {{model.meeting_name}}
        </van-cell>
      </van-cell-group>
      <div class="submit">
        <el-button class="submit-btn" type="primary" plain @click="submit" :loading="submitLoading">创建</el-button>
      </div>
    </div>
    <!-- 第N次 -->
    <van-popup v-model="showCount" position="bottom">
      <van-picker :show-toolbar="true" :columns="countList" @change="countChange" @confirm="hideCountDialog"
                  @cancel="hideCountDialog"/>
    </van-popup>
    <!-- 参会组织 -->
    <van-popup v-model="showUserInvitation" position="bottom">
      <van-picker :show-toolbar="true" value-key="name" :columns="userInvitationList" @change="userInvitationChange"
                  @confirm="hideuUerInvitationDialog" @cancel="hideuUerInvitationDialog"/>
    </van-popup>
    <!-- 议题类型 -->
    <van-dialog
      v-model="politicalDialogShow"
      show-cancel-button
      @confirm="createIssue"
      @cancel="hidePoliticalDialog"
    >
      <div class="van-dialog__header">请选择议题类型</div>
      <div class="issue-select">
        <el-radio v-model="politicalSelect" :label="politicalIndex" v-for="(political, politicalIndex) in politicalList"
                  :key="politicalIndex">
          {{political.name}}
        </el-radio>
      </div>
    </van-dialog>
  </div>
</template>

<script>
  import Upload from '../../components/Upload'
  import FileList from '../../components/FileList'
  import DateTimePacker from '../../components/DateTimePicker'

  export default {
    name: "MeetingRecordForm",
    data() {
      return {
        model: {
          count: 1,
          meeting_type_id: 0,
          meeting_name: '',
          meeting_image_url: '',
          user_invitation_id: 0,
          user_invitation_name: '',
          title: '',
          start_time: '',
          end_time: '',
          issue_list: []
        },
        minDate: new Date(),
        politicalList: [],
        politicalSelect: 0,
        politicalDialogShow: false,
        userInvitationList: [],
        userInvitationSelect: 0,
        showUserInvitation: false,
        checkList: [],
        submitLoading: false,
        showCount: false,
        countList: []
      }
    },
    components: {
      Upload,
      FileList,
      DateTimePacker
    },
    created() {
      for (let i = 1; i <= 100; i++) {
        this.countList.push(i)
      }
      window.setTitle("创建会议")
      if (this.$route.params.type_id != undefined) {
        this.model.meeting_type_id = this.$route.params.type_id
      } else {
        this.$toast("参数错误")
        this.$router.back()
      }
      this.getPoliticalList()
      this.getUserInvitations()
    },
    methods: {
      getPoliticalList() {
        let _this = this
        _this.axios.get('/political').then(res => {
          _this.politicalList = res.data
        }).catch(error => {
        })
      },
      getUserInvitations() {
        let _this = this
        _this.axios.get('/user/invitation/' + this.model.meeting_type_id).then(res => {
          _this.model.meeting_name = res.data.meeting.name
          _this.model.meeting_image_url = res.data.meeting.img_url
          _this.model.department_id = res.data.meeting.department_id
          _this.model.title = "第" + _this.model.count +  "次" + _this.model.meeting_name
          _this.model.user_invitation_id = _this.model.meeting_type_id
          _this.model.user_invitation_name = _this.model.meeting_name
        }).catch(error => {
        })
      },
      changeStartTime(value) {
        this.model.start_time = value
        this.minDate = new Date(parseInt(this.model.start_time) * 1000)
      },
      changeEndTime(value) {
        this.model.end_time = value
      },
      addIssue() {
        this.politicalDialogShow = true
      },
      createIssue() {
        this.hidePoliticalDialog()
        this.$nextTick(() => {
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
        })
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
          items = [
            {
              value: '同意票',
              files: []
            },
            {
              value: '反对票',
              files: []
            },
            {
              value: '弃权票',
              files: []
            },
          ]
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
      handleInputConfirmVoteItem(issueIndex, voteIndex) {
        let inputValue = this.model.issue_list[issueIndex].votes[voteIndex].inputValue
        if (inputValue) {
          let exist = false
          for (let item of this.model.issue_list[issueIndex].votes[voteIndex].items) {
            if (item.value == inputValue) {
              exist = true
            }
          }
          if (!exist) this.model.issue_list[issueIndex].votes[voteIndex].items.push({
            value: inputValue,
            files: []
          })
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
      back() {
        this.$router.back()
      },
      //
      uploadIssueImageSuccess(issueIndex, data) {
        this.model.issue_list[issueIndex].images.push(data)
      },
      uploadIssueFileSuccess(issueIndex, data) {
        this.model.issue_list[issueIndex].files.push(data)
      },
      uploadVoteFileSuccess(issueIndex, voteIndex, voteItemIndex, data) {
        this.model.issue_list[issueIndex].votes[voteIndex].items[voteItemIndex].files.push(data)
      },
      //
      removeIssueImage(issueIndex, index) {
        console.log(issueIndex, index)
        this.model.issue_list[issueIndex].images.splice(index, 1)
      },
      removeIssueFile(issueIndex, index) {
        this.model.issue_list[issueIndex].files.splice(index, 1)
      },
      remoVoteFile(issueIndex, voteIndex, voteItemIndex, index) {
        this.model.issue_list[issueIndex].votes[voteIndex].items[voteItemIndex].files.splice(index, 1)
      },
      showUserInvitationDialog() {
        this.showUserInvitation = true
      },
      userInvitationChange(event, value) {
        this.model.user_invitation_id = value.id
        this.model.user_invitation_name = value.name
      },
      hideuUerInvitationDialog() {
        this.showUserInvitation = false
      },
      showCountDialog() {
        this.showCount = true
      },
      countChange(event, value) {
        this.model.count = value
        this.model.title = "第" + this.model.count + "次" + this.model.meeting_name
      },
      hideCountDialog() {
        this.showCount = false
      },
      submit() {
        this.submitLoading = true
        if (this.model.title.length < 1) {
          this.submitLoading = false
          this.$toast("会议主题不能为空")
          return
        }
        if (this.model.start_time >= this.model.end_time) {
          this.submitLoading = false
          this.$toast("请选择结束时间")
          return
        }
        if (this.model.issue_list.length < 1) {
          this.submitLoading = false
          this.$toast("请添加至少一个议题")
          return
        }
        for (let issue of this.model.issue_list) {
          if (issue.title.length < 1) {
            this.submitLoading = false
            this.$toast("议题标题不能为空")
            return
          }
          for (let vote of issue.votes) {
            if (vote.items.length < 1) {
              this.submitLoading = false
              this.$toast("投票选项不能为空")
              return
            }
          }
        }
        this.axios.post('/meetingRecord/create', this.model).then(res => {
          this.$toast(res.msg)
          this.submitLoading = false
          setTimeout(() => {
            if (res.status) {
              this.$router.back()
            }
          }, 2000)
        }).catch(error => {
          this.submitLoading = false
        })
      }
    }
  }
</script>

<style scoped>
  .van-cell-group {
    margin-bottom: 10px;
  }

  .cell-group-title {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    font-weight: 400;
    font-size: 14px;
    color: rgba(69, 90, 100, .6);
    padding: 20px 15px 15px;
  }

  .cell-group-title span, .cell-group-title i {
    display: inline-block;
  }

  .placeholder-text {
    color: #757575;
  }

  .upload-item {
    display: inline-block;
    width: 50px;
    height: 50px;
    color: #ccc;
    border: 1px dashed #ccc;
    line-height: 50px;
    border-radius: 5px;
  }

  .upload-item .image {
    width: 100%;
    height: 100%;
    border-radius: 5px;
  }

  .upload-list {
    margin-left: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
  }

  .issue-file-list {
    margin-left: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
  }

  .cell-title {
    margin: 0;
    font-weight: 400;
    color: #333;
    font-size: 14px;
    line-height: 24px;
    padding: 10px 0 15px;
  }

  .file-item {
  }

  .vote-item {
    padding: 10px;
    border: 1px solid #eee;
  }

  .vote-item:nth-child(n+1) {
    margin-top: 10px;
  }

  .vote-item-tag {
  }

  .input-vote-item-tag {
    max-width: 150px;
  }

  .input-vote-item-tag, .btn-vote-item-tag {
    margin-top: 10px;
  }

  .vote-file-list {
    margin-top: 10px;
  }

  .just-tag {
    margin-right: 10px;
    margin-bottom: 10px;
  }

  .issue-select {
    width: 100%;
    margin-top: 15px;
    margin-bottom: 15px;
    display: flex;
    flex-direction: row;
    justify-content: center;
  }

  .check-box {
    display: flex;
    flex-direction: column;
    text-align: left;
  }

  .meeting-image{
    display: flex;
    justify-content: center;
    padding: 10px 0;
    background-color: #ffffff;
  }
  .meeting-image img{
    width: 50px;
    height: 50px;
  }
</style>
<style>
  .form-item .van-cell__title {
    max-width: 90px;
  }

  .form-item .van-cell__value {
    text-align: left;
  }

  .issue-content::after {
    border-bottom: unset;
  }

  .van-cell--required {
    overflow: hidden;
  }

  .el-checkbox + .el-checkbox {
    margin-left: 0;
  }
</style>