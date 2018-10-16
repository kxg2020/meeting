<template>
  <div class="main">
    <div class="issue-info" v-if="issue">
      <h3 class="issue-title">{{issue.title}}</h3>
      <div class="issue-content">
        {{issue.content}}
      </div>
      <div class="issue-images">
        <img class="issue-image" v-for="(image, imageIndex) in issue.images" :key="imageIndex" :src="image.file_url"
             alt="">
      </div>
      <div class="issue-files">
        <div class="item-header">
          <i class="fa fa-file"></i>附件<span>(点击文件下载到本地)</span>
        </div>
        <FileList :files="issue.files" :show-remove="false" :show-download="true" :preview="true"></FileList>
      </div>
      <div v-if="issue.votes.length > 0" class="issue-vote">
        <div class="item-header">
          <i class="fa fa-hand-paper-o"></i>{{issue.political_name}}
        </div>
        <div class="vote-list">
          <div class="vote-item" v-for="(vote, voteIndex) in issue.votes" :key="'v' + voteIndex">
            <div v-for="(voteValue, voteValueIndex) in vote.values" class="vote-item-notice" :key="'i' + voteValueIndex">
              选项{{(voteValueIndex + 1).ConvertToChinese()}}{{voteValue.value}}
              <div v-if="voteValue.files.length > 0">
                <div class="file-header">
                  <i class="fa fa-file"></i>附件
                </div>
                <FileList :files="voteValue.files" :show-remove="false" :show-download="true" :preview="true"></FileList>
              </div>
            </div>
            <el-radio-group v-model="voteSelects[voteIndex]" size="mini">
              <el-radio :label="voteValueIndex" border v-for="(voteValue, voteValueIndex) in vote.values"
                        :key="'r' + voteValueIndex">{{voteValue.value}}
              </el-radio>
            </el-radio-group>
          </div>
        </div>
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
        issue: null
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
          // _this.issue = res.data
          _this.issue = {
            id: 1,
            title: '关于xxx议题',
            political_name: "投票",
            content: '议题内容议题内容议题内容议题内容议题内容议题内容议题内容议题内容议题内容议' +
              '议题内容议题内容议题内容议题内容议题内容议题内容议题内容议题内容议题内容议题内容议' +
              '题内容议题内容议题内容议题内容议题内容议题内容议题内容议题内容议题内容议题内容议题' +
              '内容议题内容议题内容题内容议题内容',
            images: [
              {
                file_name: 'xxxx.png',
                file_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
              },
              {
                file_name: 'xxxx.png',
                file_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
              },
            ],
            files: [
              {
                file_name: 'liphp3bw52hptAX1FABEYjQ1u6I_.pdf',
                file_url: 'https://img.it9g.com/doc/liphp3bw52hptAX1FABEYjQ1u6I_.pdf'
              },
              {
                file_name: 'doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc',
                file_url: 'https://img.it9g.com/doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc'
              }
            ],
            votes: [
              {
                values: [
                  {
                    value: "张三",
                    files: [
                      {
                        file_name: '张三.png',
                        file_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
                      },
                      {
                        file_name: 'doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc',
                        file_url: 'https://img.it9g.com/doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc'
                      }
                    ]
                  },
                  {
                    value: "李四",
                    files: [
                      {
                        file_name: '李四.png',
                        file_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
                      },
                      {
                        file_name: 'doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc',
                        file_url: 'https://img.it9g.com/doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc'
                      }
                    ]
                  }
                ]
              },
              {
                values: [
                  {
                    value: "十万",
                    files: [
                      {
                        file_name: '十万.png',
                        file_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
                      },
                      {
                        file_name: '十万.doc',
                        file_url: 'https://img.it9g.com/doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc'
                      },
                    ]
                  },
                  {
                    value: "百万",
                    files: [
                      {
                        file_name: '百万.png',
                        file_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
                      },
                      {
                        file_name: '百万.doc',
                        file_url: 'https://img.it9g.com/doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc'
                      },
                    ]
                  }
                ]
              },
            ]
          }
          let voteSelects = {}
          for (let voteIndex in this.issue.votes) {
            voteSelects[voteIndex] = 0
          }
          this.voteSelects = voteSelects
        }).catch(err => {
        })
      }
    }
  }
</script>

<style scoped>

  .issue-info{
    padding: 15px;
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

  .vote-item:nth-child(n+1) {
    margin-top: 15px;
  }
</style>