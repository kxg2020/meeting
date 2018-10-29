<template>
  <div class="main-container">
    <div class="notice" v-if="notice">
      <h3 class="notice-title">{{notice.title}}</h3>
      <div class="create-time">
        {{notice.create_time}}
      </div>
      <div class="notice-content">
        {{notice.content}}
      </div>
      <div class="notice-images">
        <img class="notice-image" v-for="(image, imageIndex) in notice.images" :key="imageIndex" :src="image.file_url"
             alt="">
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "Notice",
    data () {
      return {
        notice: null
      }
    },
    created() {
      if (this.$route.params.id != undefined) {
        this.getNoticeInfo(this.$route.params.id)
      } else {
        this.$toast("参数错误")
        this.$router.back()
      }
      window.setTitle("会议详情")
    },
    methods: {
      getNoticeInfo(id) {
        let _this = this
        _this.axios.get('/notice/detail/' + id).then(res => {
          if (res.status) {
            _this.notice = res.data
          } else {
            _this.$toast(res.msg)
          }
        }).catch(err => {})
      }
    }
  }
</script>

<style scoped>
  .notice{
    margin: 15px;
    padding: 15px;
    background-color: #FFFFFF;
    border-radius: 5px;
  }
  .notice-title{
    padding-bottom: .7em;
    border-bottom: 1px solid #ddd;
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.75em;
    line-height: 2em;
  }
  .create-time{
    margin: .9em 0 .5em;
    font-size: 14px;
    padding-bottom: 15px;
    color: rgb(149, 149, 149);
  }
  .notice-content{
    color: #1d1d1d;
    font-size: 1em;
    line_height: 1.6em;
    margin: .9em 0 .5em;
  }
  .notice-image{
    width: 100%;
    height: auto;
    padding: 10px;
    box-sizing: border-box;
  }
</style>