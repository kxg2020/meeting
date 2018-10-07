<template>
  <div class="main">
    <div class="main-container main-container-top">
      <van-search
        v-model="searchValue"
        placeholder="请输入搜索关键词"
        show-action
        @search="onSearch"
      >
        <div slot="action" @click="onSearch">搜索</div>
      </van-search>
      <div class="meeting-group-list">
        <div class="meeting-group-item" v-for="(meetingGroupItem, mgIndex) in meetingList" :key="mgIndex" @click="toMeetingList(meetingGroupItem.id)">
          <div class="meeting-group-img">
            <img :src="meetingGroupItem.img_url" alt="">
          </div>
          <div class="meeting-group-title">
            <span>{{meetingGroupItem.title}}</span>
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
        meetingList: [
          {
            id: 1,
            title: '党员大会',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
          },
          {
            id: 1,
            title: '党员大会',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
          },
          {
            id: 1,
            title: '党员大会',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
          },
          {
            id: 1,
            title: '党员大会',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
          },
          {
            id: 1,
            title: '党员大会',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg',
          },
        ]
      }
    },
    components: {
      Tabbar
    },
    created() {
      let _this = this
      _this.axios.get('/meeting/type').then(res => {
        console.log(res)
      }).catch(error => {

      })
    },
    methods: {
      toMeetingList (group_id) {
        this.$router.push({path: '/meeting_list/' + group_id})
      },
      onSearch () {
        this.$toast(this.searchValue)
      }
    }
  }
</script>

<style scoped>
  .meeting-group-list {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    justify-content: start;
  }

  .meeting-group-item {
    box-sizing: border-box;
    width: 25%;
    padding: 10px;
    text-align: center;
  }

  .meeting-group-img img {
    width: 50px;
    height: 50px;
    border-radius: 5px;
  }

  .meeting-group-title {
    color: #333;
    line-height: 1.2em;
  }
</style>