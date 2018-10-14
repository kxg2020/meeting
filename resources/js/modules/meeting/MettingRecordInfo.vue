<template>
  <div class="main">
    <div class="meeting-record-info">
      <h2 class="info-title">{{info.title}}</h2>
      <div class="info-create-user">
        <span>-</span>
        <img class="create-user-avatar" :src="info.create_user_avatar" alt="">
        <span class="create-user-name">{{info.create_user_name}}</span>
        <span>-</span>
      </div>
      <div class="info-time">
        <i class="fa fa-clock-o"></i>
        <span class="time">{{info.start_time}}</span>
        <span>-</span>
        <span class="time">{{info.end_time}}</span>
      </div>
      <div class="issue-list">
        <div class="issue-item" v-for="(issue, issueIndex) in info.issue_list" :key="issueIndex">
          <h3 class="issue-title"><span>第{{(issueIndex + 1).ConvertToChinese()}}项、</span>{{issue.title}}</h3>
          <div class="issue-content">
            {{issue.content}}
          </div>
          <div class="issue-images">
            <img class="issue-image" v-for="(image, imageIndex) in issue.images" :key="issueIndex + '' + imageIndex" :src="image.file_url" alt="">
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
                <el-radio-group v-model="voteSelects[issueIndex + '-' + voteIndex]" size="mini">
                  <el-radio :label="voteValueIndex" border v-for="(voteValue, voteValueIndex) in vote.values" :key="'r' + voteValueIndex">{{voteValue.value}}</el-radio>
                </el-radio-group>
              </div>
            </div>
          </div>
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
        this.info = {
          id: 1,
          title: '第1次党员大会',
          start_time: '2018-10-30 08:00',
          end_time: '2018-10-30 12:00',
          create_user_name: 'xxx',
          create_user_avatar: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg',
          create_username: 'xx党员',
          issue_list: [
            {
              id: 1,
              title: '关于xxx议题',
              political_name: "阅知",
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
                  file_name: '1080/FgyEK7REr6E1tvbCh4KVzemLTRZ5.jpeg',
                  file_url: 'https://img.it9g.com/1080/FgyEK7REr6E1tvbCh4KVzemLTRZ5.jpeg'
                },
                {
                  file_name: 'liphp3bw52hptAX1FABEYjQ1u6I_.pdf',
                  file_url: 'https://img.it9g.com/doc/liphp3bw52hptAX1FABEYjQ1u6I_.pdf'
                },
                {
                  file_name: 'doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc',
                  file_url: 'https://img.it9g.com/doc/FsvAtDay7Ay6ZfKtPufnUjjI69Mn.doc'
                },
                {
                  file_name: 'other/llSwheM4_-zSYrVBhsMLIgn18Dqa.mp4',
                  file_url: 'https://img.it9g.com/other/llSwheM4_-zSYrVBhsMLIgn18Dqa.mp4'
                },
              ],
              votes: []
            },
            {
              id: 1,
              title: '关于xxx议题',
              political_name: "表决",
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
                      value: "通过",
                      files: []
                    },
                    {
                      value: "拒绝",
                      files: []
                    },
                  ]
                }
              ]
            },
            {
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
            },
          ],
        }
        let voteSelects = {}
        for (let issueIndex in this.info.issue_list) {
          for (let voteIndex in this.info.issue_list[issueIndex].votes) {
            voteSelects[issueIndex + '-' + voteIndex] = 0
          }
        }
        this.voteSelects = voteSelects
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