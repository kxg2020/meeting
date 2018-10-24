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
          <!--
          <el-radio-group v-model="voteSelects[voteIndex]" size="mini" v-if="issue.edit">
            <el-radio :label="itemIndex" border
              v-for="(item, itemIndex) in vote.items" :key="'iv' + itemIndex">
              {{item.value}}
            </el-radio>
          </el-radio-group>
          -->
          <div class="vote-select">
            <div class="cell-group-title">
              <span>最多可选{{vote.select_count}}个</span>
            </div>
            <el-checkbox-group
              v-if="issue.edit"
              size="mini"
              v-model="voteSelects[voteIndex]"
              :max="parseInt(vote.select_count)"
              @change="value => {voteChange(voteIndex, value)}">
              <el-checkbox v-for="(item, itemIndex) in vote.items" :key="'iv' + itemIndex" :label="itemIndex" border>
                {{item.value}}
              </el-checkbox>
            </el-checkbox-group>
            <div class="vote-radios" v-else>
              <div v-for="(item, itemIndex) in vote.items" :key="itemIndex" class="vote-radio-item" :class="item.selected ? 'selected' : ''">
                <span>{{item.value}}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="submit" v-if="issue.edit">
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
            voteSelects.push([])
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
        for (let i in _this.voteSelects) {
          if (_this.voteSelects[i].length < 1) {
            _this.$toast(_this.issue.issue_short_name == "bj" ? "请选择表决选项" : "请选择投票选项")
            return
          }
        }
        _this.axios.post('/userVotes/create', postData).then(res => {
          if (res.status) {
            _this.$toast(res.msg)
            _this.getIssue(_this.issue.issue_id)
          } else {
            _this.$toast(res.msg)
          }
        }).catch(err => {})
        console.log(JSON.stringify(postData))
      },
      voteChange(voteIndex, value) {
        console.log(voteIndex, value)
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

  .vote-radio-item {
    display: inline-block;
    border: 1px solid #dcdfe6;
    box-sizing: border-box;
    font-size: 12px;
    padding: 6px 15px 0 15px;
    border-radius: 3px;
    height: 28px;
  }

  .selected{
    border-color: #409eff;
    color: #409eff;
  }

  .vote-radio-item+.vote-radio-item{
    margin-left: 10px;
  }

  .cell-group-title {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    font-weight: 400;
    font-size: 14px;
    color: rgba(69, 90, 100, .6);
    padding: 10px 0;
    border-top: 1px solid #eee;
  }
</style>