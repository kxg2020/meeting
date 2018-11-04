<template>
  <div class="main-container">
    <!--
      <van-search
        v-model="searchValue"
        placeholder="请输入搜索关键词"
        show-action
        @search="onSearch"
      >

        <div slot="action" @click="onSearch">搜索</div>
      </van-search>
      -->
      <div class="block meeting-type-list">
        <div class="meeting-type-item" v-for="(meetingTypeItem, mgIndex) in meetingTypeList" :key="mgIndex" @click="toMeetingList(meetingTypeItem.id)">
          <div class="meeting-type-item-wrap">
            <div class="meeting-type-img">
              <img :src="meetingTypeItem.img_url" alt="">
            </div>
            <div class="meeting-type-title">
              <span>{{meetingTypeItem.title}}</span>
            </div>
          </div>
        </div>
      </div>
      <van-panel class="notice-panel">
        <div slot="header" class="notice-header">
          <h2 style="display: inline-block">新闻公告</h2>
          <span @click="toNoticeList">更多 <i class="fa fa-angle-right"></i></span>
        </div>
        <div class="notice-body" @click="toNotice(1)" v-if="notice">
          <img :src="notice.images.length > 0 ? notice.images[0].file_url : '/static/images/notice_default.jpg'" alt="">
          <div class="notice-info">
            <div class="notice-info-title" style="height: 60px;">{{notice.title}}</div>
            <div class="notice-info-time">{{notice.create_time}}</div>
          </div>
        </div>
      </van-panel>
    </div>
</template>

<script>
  export default {
    name: "Index",
    data() {
      return {
        searchValue: '',
        meetingTypeList: [],
        permission_ids: [],
        notice: null
      }
    },
    created() {
      window.setTitle("首页")
      this.getMeetingTypeList()
      this.getNoticeLatest()
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
      getNoticeLatest() {
        let _this = this
        _this.axios.get('/notice/latest').then(res => {
          if (res.status && res.data) {
            this.notice = res.data
          }
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
    text-align: center;
    position: relative;
    width: 33.33333%;
    height: 0;
    padding: 33.33333% 0 0;
  }
  .meeting-type-item-wrap{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 1px solid #e5e5e5;
  }

  .meeting-type-img{
    width: 50%;
    height: 50%;
    margin: 15% auto 0;
  }
  .meeting-type-img img{
    width: 100%;
    height: 100%;
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