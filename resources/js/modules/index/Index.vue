<template>
  <div class="main">
    <div class="main-container main-container-top">
      <div class="meeting-group-list">
        <div class="meeting-group-item" v-for="(meetingGroupItem, mgIndex) in meetingList"
             :style="'/*background-image: url(' + meetingGroupItem.img_url + ');*/'" :key="mgIndex" @click="toMeetingList(meetingGroupItem.id)">
          <div class="meeting-group-title">
            <i :class="meetingGroupItem.icon" aria-hidden="true"></i>
            <span>{{meetingGroupItem.title}}</span>
          </div>
          <div class="meeting-group-desc">
            {{meetingGroupItem.desc}}
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
        meetingList: [
          {
            id: 1,
            title: '党员大会',
            icon: 'fa fa-file-text-o',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg',
            desc: '会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息'
          },
          {
            id: 1,
            title: '党员大会',
            icon: 'fa fa-file-text-o',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg',
            desc: '会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息'
          },
          {
            id: 1,
            title: '党员大会',
            icon: 'fa fa-file-text-o',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg',
            desc: '会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息'
          },
          {
            id: 1,
            title: '党员大会',
            icon: 'fa fa-file-text-o',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg',
            desc: '会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息'
          },
          {
            id: 1,
            title: '党员大会',
            icon: 'fa fa-file-text-o',
            img_url: 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg',
            desc: '会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息会议分组描述信息'
          },
        ]
      }
    },
    components: {
      Tabbar
    },
    created() {

    },
    methods: {
      toMeetingList (group_id) {
        this.$router.push({path: '/meeting_list/' + group_id})
      }
    }
  }
</script>

<style scoped>
  .meeting-group-list {
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .meeting-group-item {
    margin: 15px;
    height: 150px;
    border: 1px solid #f1f4f8;
    border-radius: 5px;
    background-repeat: no-repeat;
    background-size: 100% 100%;
  }

  .meeting-group-title {
    color: #333;
    line-height: 2em;
    font-size: 1.2em;
  }

  .meeting-group-title i {
    margin-left: 1em;
    margin-right: .5em;
  }

  .meeting-group-desc {
    margin: 1em 2.5em;
    color: #666;
  }
</style>