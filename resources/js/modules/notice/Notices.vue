<template>
  <div class="main">
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
    </div>
    <Tabbar :active="1"></Tabbar>
  </div>
</template>

<script>
  import Tabbar from '../../components/Tabbar'
  import Empty from '../../components/Empty'
  export default {
    name: "Notice",
    data() {
      return {
        notice_list: [],
        hasMore: true,
      }
    },
    components: {
      Tabbar,
      Empty
    },
    created() {
      window.setTitle("新闻公告")
      this.loadMore()
    },
    methods: {
      loadMore() {
        this.notice_list = this.notice_list.concat([
          {
            id: 1,
            title: '公告标题哈哈哈',
            thumb: 'https://meeting.it9g.com/static/uploads/2018-10-13/zhangtao/071211270918e826d6019690.jpg',
            intro: '公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略',
            create_time: '2018-10-18'
          },
          {
            id: 1,
            title: '公告标题哈哈哈',
            thumb: 'https://meeting.it9g.com/static/uploads/2018-10-13/zhangtao/071211270918e826d6019690.jpg',
            intro: '公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略',
            create_time: '2018-10-18'
          },
          {
            id: 1,
            title: '公告标题哈哈哈',
            thumb: 'https://meeting.it9g.com/static/uploads/2018-10-13/zhangtao/071211270918e826d6019690.jpg',
            intro: '公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略',
            create_time: '2018-10-18'
          },
          {
            id: 1,
            title: '公告标题哈哈哈',
            thumb: 'https://meeting.it9g.com/static/uploads/2018-10-13/zhangtao/071211270918e826d6019690.jpg',
            intro: '公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略',
            create_time: '2018-10-18'
          },
          {
            id: 1,
            title: '公告标题哈哈哈',
            thumb: 'https://meeting.it9g.com/static/uploads/2018-10-13/zhangtao/071211270918e826d6019690.jpg',
            intro: '公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略公告简略',
            create_time: '2018-10-18'
          },
        ])
        if (Math.random() > 0.7) this.hasMore = false
      },
      toInfo(id) {
        this.$router.push({path: '/notice/' + id})
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

</style>