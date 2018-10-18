<template>
  <div class="main">
    <div class="main-container">
      <van-search
        v-model="searchValue"
        placeholder="请输入搜索关键词"
        show-action
        @search="onSearch"
      >
        <div slot="action" @click="onSearch">搜索</div>
      </van-search>
      <div class="block meeting-type-list">
        <div class="meeting-type-item" v-for="(meetingTypeItem, mgIndex) in meetingTypeList" :key="mgIndex" @click="toMeetingList(meetingTypeItem.id)">
          <div class="meeting-type-img">
            <img :src="meetingTypeItem.img_url" alt="">
          </div>
          <div class="meeting-type-title">
            <span>{{meetingTypeItem.title}}</span>
          </div>
        </div>
      </div>
      <van-panel class="notice-panel">
        <div slot="header" class="notice-header">
          <h2 style="display: inline-block">新闻公告</h2>
          <span @click="toNoticeList">更多 <i class="fa fa-angle-right"></i></span>
        </div>
        <div class="notice-body" @click="toNotice(1)">
          <img src="https://meeting.it9g.com/static/uploads/2018-10-13/zhangtao/071211270918e826d6019690.jpg" alt="">
          <div class="notice-info">
            <div class="notice-info-title" style="height: 60px;">公告标题呵呵呵呵</div>
            <div class="notice-info-time">2018-10-18</div>
          </div>
        </div>
      </van-panel>
    </div>
    <Tabbar :active="0"></Tabbar>
  </div>
</template>

<script>
  import Tabbar from '../../components/Tabbar'

  export default {
    name: "Index",
    data() {
      return {
        searchValue: '',
        meetingTypeList: [],
        permission_ids: []
      }
    },
    components: {
      Tabbar
    },
    created() {
      window.setTitle("首页")
      this.getMeetingTypeList()
      this.permission_ids = window.permission_ids
    },
    methods: {
      getMeetingTypeList() {
        let _this = this
        _this.axios.get('/meetingTypes').then(res => {
          this.meetingTypeList = res.data
        }).catch(error => {

        })
      },
      toMeetingList (group_id) {
        if (this.permission_ids.includes(group_id)) {
          this.$router.push({path: '/meeting_record/list/' + group_id})
        } else {
          this.$toast("没有访问权限")
        }
      },
      onSearch () {
        this.$toast(this.searchValue)
      },
      toNoticeList() {
        this.$router.push({path: '/notices'})
      },
      toNotice(id) {
        this.$router.push({path: '/notice/' + id})
      }
    }
  }
</script>

<style scoped>
  .main-container{
    margin-bottom: 50px;
  }

  .meeting-type-list {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    justify-content: start;
    margin: 14px 15px;
    padding: 10px 5px;
  }

  .meeting-type-item {
    box-sizing: border-box;
    width: 25%;
    padding: 10px;
    text-align: center;
  }

  .meeting-type-img img {
    width: 50px;
    height: 50px;
    border-radius: 5px;
  }

  .meeting-type-title {
    color: #333;
    line-height: 1.2em;
    height: 2.4em;
  }

  .notice-panel{
    padding: 10px 15px;
    margin-bottom: 14px;
  }

  .notice-header{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    margin-bottom: 15px;
  }

  .notice-header h2{
    display: inline-block;
    font-size: 18px;
    font-weight: 600;
    color: #353535;
  }

  .notice-header span{
    color: #9a9a9a;
    font-size: 14px;
    line-height: 24px;
  }
</style>