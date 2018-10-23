<template>
  <div class="main">
    <div class="issue" v-if="issue">
      <div class="block">
        <h3 class="issue-title">{{issue.issue_name}}</h3>
        <div class="issue-content">
          {{issue.content}}
        </div>
        <div class="issue-images">
          <img class="issue-image" v-for="(image, imageIndex) in issue.images" :key="imageIndex" :src="image.file_url"
               alt="">
        </div>
        <div class="issue-files">
          <FileList :files="issue.files" :show-remove="false" :show-download="true" :preview="true"></FileList>
        </div>
      </div>
      <div v-if="issue.option.length > 0" class="block">
        <div class="item-header">
          <i class="fa fa-hand-paper-o"></i><span style="margin-left: 1em;">{{issue.issue_short_name == 'bj' ? '表决' : '投票'}}</span>
        </div>
        <div class="vote-item" v-for="(vote, voteIndex) in issue.option" :key="'o' + voteIndex">
          <div class="vote-title">{{vote.title}}</div>
          <div v-for="(item, itemIndex) in vote.items" class="vote-item-notice" :key="'i' + itemIndex" v-if="item.files.length > 0">
            选项{{(itemIndex + 1).ConvertToChinese()}} <span style="color: #409EFF;">{{item.value}}</span>
            <div>
              <FileList :files="item.files" :show-remove="false" :show-download="true" :preview="true"></FileList>
            </div>
          </div>
          <el-radio-group v-model="voteSelects[voteIndex]" size="mini">
            <el-radio :label="itemIndex" border v-for="(item, itemIndex) in vote.items"
                      :key="'iv' + itemIndex">{{item.value}}
            </el-radio>
          </el-radio-group>
        </div>
      </div>
      <div class="submit">
        <el-button class="submit-btn" type="primary" plain @click="voteSubmit" :loading="submitLoading">
          {{issue.issue_short_name == 'yz' ? '已阅' : ''}}
          {{issue.issue_short_name == 'bj' ? '提交表决' : ''}}
          {{issue.issue_short_name == 'tp' ? '提交投票' : ''}}
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import FileList from '../../components/FileList'

  export default {
    name: "MeetingRecordIssue",
    data() {
      return {
        issue: null,
        voteSelects: [],
        submitLoading: false
      }
    },
    components: {
      FileList
    },
    created() {
      if (this.$route.params.id != undefined) {
        this.getIssue(this.$route.params.id)
      } else {
        this.$toast("参数错误")
        this.$router.back()
      }
      window.setTitle("议题详情")
    },
    methods: {
      getIssue(id) {
        let _this = this
        _this.axios.get('/meetingRecord/detail/' + id).then(res => {
          _this.issue = res.data
          let voteSelects = []
          for (let i in _this.issue.option) {
            voteSelects.push(0)
          }
          _this.voteSelects = voteSelects
        }).catch(err => {
        })
      },
      voteSubmit() {
        let _this = this
        let postData = {}
        postData.issue_id = _this.issue.issue_id
        postData.issue_short_name = _this.issue.issue_short_name
        postData.votes = _this.voteSelects
        _this.axios.post('/userVotes/create', postData).then(res => {
          if (res.status) {
            _this.$toast(res.msg)
            _this.getIssue(_this.issue.issue_id)
          } else {
            _this.$toast(res.msg)
          }
        }).catch(err => {})
        console.log(JSON.stringify(postData))
      }
    }
  }
</script>

<style scoped>
  .issue {
    padding: 0 15px;
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

  .issue-content {
    line-height: 1.6em;
    color: #1d1d1d;
    vertical-align: baseline;
  }

  .issue-images {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .issue-image {
    width: 80%;
    height: auto;
    padding: 15px 0;
  }

  .issue-files {

  }

  .item-header {
    padding: .5em 0;
    font-size: 14px;
  }

  .file-header i, .file-header span {
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

  .vote-title {
    padding: .5em 0;
    font-size: 14px;
  }

  .vote-item:nth-child(n+1) {
    margin-top: 15px;
  }

  .submit {
    padding: 10px 0;
  }
</style>