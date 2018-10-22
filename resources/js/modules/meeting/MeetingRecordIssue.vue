<template>
  <div class="main">
    <div class="issue">
      <div class="block" v-if="issue">
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
      <div v-if="bjVotes.length > 0" class="block">
        <div class="item-header">
          <i class="fa fa-hand-paper-o"></i><span style="margin-left: 1em;">表决</span>
        </div>
        <div class="vote-item" v-for="(bj, bjIndex) in bjVotes" :key="'bj' + bjIndex">
          <div class="vote-title">{{bj.title}}</div>
          <el-radio-group v-model="bjVoteSelect[bjIndex]" size="mini">
            <el-radio :label="optionIndex" border v-for="(option, optionIndex) in bj.options"
                      :key="'bji' + optionIndex">{{option}}
            </el-radio>
          </el-radio-group>
        </div>
      </div>
      <div v-if="tpVotes.length > 0" class="block">
        <div class="item-header">
          <i class="fa fa-hand-paper-o"></i><span style="margin-left: 1em;">投票</span>
        </div>
        <div class="vote-item" v-for="(tp, tpIndex) in tpVotes" :key="'tp' + tpIndex">
          <div class="vote-title">{{tp.title}}</div>
          <div v-for="(option, optionIndex) in tp.options" class="vote-item-notice" :key="'tpf' + optionIndex" v-if="option.files.length > 0">
            选项{{(optionIndex + 1).ConvertToChinese()}} <span style="color: #409EFF;">{{option.choose_name}}</span>
            <div>
              <FileList :files="option.files" :show-remove="false" :show-download="true" :preview="true"></FileList>
            </div>
          </div>
          <el-radio-group v-model="tpVoteSelect[tpIndex]" size="mini">
            <el-radio :label="optionIndex" border v-for="(option, optionIndex) in tp.options"
                      :key="'tpi' + optionIndex">{{option.choose_name}}
            </el-radio>
          </el-radio-group>
        </div>
      </div>
      <div class="submit" v-if="tpVotes.length > 0 || bjVotes.length > 0">
        <el-button class="submit-btn" type="primary" plain @click="voteSubmit" :loading="submitLoading">提交投票</el-button>
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
        bjVotes: [],
        bjVoteSelect: [],
        tpVotes: [],
        tpVoteSelect: [],
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
          if (_this.issue.issue_short_name == 'yz') {

          }
          if (_this.issue.issue_short_name == 'bj') {
            let bjVotes = []
            for (let option of _this.issue.option) {
              let vote = {}
              vote.title = option.options[0].title
              vote.choose_id = option.options[0].choose_id
              vote.options = option.options[0].choose_name.split(',')
              bjVotes.push(vote)
              _this.bjVoteSelect.push(0)
            }
            _this.bjVotes = bjVotes
          }
          if (_this.issue.issue_short_name == 'tp') {
            let tpVotes = []
            for (let option of _this.issue.option) {
              let vote = {}
              let options = []
              vote.title = option.options[0].title
              for (let i in option.options) {
                options.push({
                  files: option.options[i].file,
                  choose_name: option.options[i].choose_name,
                  choose_id: option.options[i].choose_id
                })
              }
              vote.options = options
              tpVotes.push(vote)
              _this.tpVoteSelect.push(0)
            }
            _this.tpVotes = tpVotes
          }
        }).catch(err => {
        })
      },
      voteSubmit() {
        let _this = this
        let postData = {}
        postData.issue_id = _this.issue.issue_id
        postData.issue_short_name = _this.issue.issue_short_name
        if (_this.issue.issue_short_name == 'bj') {
          let votes = []
          for (let i in _this.bjVotes) {
            votes.push({
              choose_id: _this.bjVotes[i].choose_id,
              select_index: _this.bjVoteSelect[i]
            })
          }
          postData.votes = votes
        }
        if (_this.issue.issue_short_name == 'tp') {
          let votes = []
          for (let i in _this.tpVoteSelect) {
            votes.push(_this.tpVotes[i].options[_this.tpVoteSelect[i]].choose_id)
          }
          postData.votes = votes
        }
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