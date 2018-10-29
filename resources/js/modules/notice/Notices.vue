<template>
  <div class="main-container">
    <div v-if="notice_list.length > 0">
      <div class="notice-list">
        <div v-for="(notice, index) in notice_list" :key="index" class="notice-item" @click="toInfo(notice.id)">
          <div class="notice-body">
            <img :src="notice.thumb" alt="">
            <div class="notice-info">
              <div class="notice-info-title">{{notice.title}}</div>
              <div class="notice-info-intro">{{notice.intro}}</div>
              <div class="notice-info-time">{{notice.create_time}}</div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="hasMore"  @click="loadMore" class="bottom-noti link">
        <span>查看更多</span>
      </div>
      <div v-else class="bottom-noti">
        <span>没有更多内容了</span>
      </div>
    </div>
    <div v-else>
      <Empty></Empty>
    </div>
    <div class="create-btn" @click="toForm">
      <i class="fa fa-edit"></i>
    </div>
  </div>
</template>

<script>
  import Empty from '../../components/Empty'
  export default {
    name: "Notice",
    data() {
      return {
        notice_list: [],
        page_size: 5,
        page: 1,
        total: 0,
        hasMore: false
      }
    },
    components: {
      Empty
    },
    created() {
      window.setTitle("新闻公告")
      this.loadMore()
    },
    methods: {
      loadMore() {
        let _this = this
        _this.axios.get('/notice/list', {params: {page: _this.page, page_size: _this.page_size}}).then(res => {
          if (res.status) {
            _this.notice_list = _this.notice_list.concat(res.data.notice_list)
            _this.total = res.data.total
            if (Math.ceil(_this.total / _this.page_size) > _this.page){
              _this.hasMore = true
            } else {
              _this.hasMore = false
            }
            _this.page++
          } else {
            _this.$toast(res.msg)
          }
        }).catch(err => {})
      },
      toInfo(id) {
        this.$router.push({path: '/notice/' + id})
      },
      toForm() {
        this.$router.push({path: '/notice/create'})
      }
    }
  }
</script>

<style scoped>
  .main-container{
    margin-bottom: 50px;
  }
  .notice-item{
    margin: 15px;
    background-color: #ffffff;
    border-radius: 5px;
    padding: 15px;
  }
  .create-btn{
    position: fixed;
    right: 20px;
    bottom: 70px;
    font-size: 20px;
    width: 40px;
    height: 40px;
    background-color: #000;
    text-align: center;
    line-height: 40px;
    border-radius: 10px;
    color: #ffffff;
    transition: opacity 0.3s;
    opacity: 0.3;
  }
</style>