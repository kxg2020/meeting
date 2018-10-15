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
      <div class="meeting-type-list">
        <div class="meeting-type-item" v-for="(meetingTypeItem, mgIndex) in meetingTypeList" :key="mgIndex" @click="toMeetingList(meetingTypeItem.id)">
          <div class="meeting-type-img">
            <img :src="meetingTypeItem.img_url" alt="">
          </div>
          <div class="meeting-type-title">
            <span>{{meetingTypeItem.title}}</span>
          </div>
        </div>
      </div>
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
      }
    }
  }
</script>

<style scoped>
  .meeting-type-list {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    justify-content: start;
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
</style>