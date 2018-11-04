<template>
  <div class="main-container">
    <div>
      <div class="member">
        <img class="member-avatar" :src="user_info.avatar" alt="">
        <div class="member-name">{{user_info.name}} <span class="member-position">{{user_info.position}}</span></div>
      </div>
      <van-cell-group class="user-group">
        <van-cell icon="chat" title="公告" is-link to="/notices"/>
      </van-cell-group>
      <van-cell-group>
        <van-cell icon="delete" title="清除缓存" is-link @click="clearCache"/>
      </van-cell-group>
    </div>
  </div>
</template>

<script>
  export default {
    name: "Member",
    data() {
      return {
        user_info: window.user_info
      }
    },
    created() {
      window.setTitle("个人中心")
    },
    methods: {
      clearCache() {
        this.axios.get('/clear/cache').then(res => {
          this.$toast('清除成功')
        })
      }
    }
  }
</script>

<style scoped>
  .main-container {
    margin-bottom: 50px;
  }

  .member {
    display: flex;
    flex-direction: row;
  }

  .member-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 20px 10px 20px 20px;
  }

  .member-name {
    color: #1d1d1d;
    margin-top: 50px;
    font-size: 20px;
    font-weight: 800;
  }
  .member-position {
    font-size: 16px;
    font-weight: 100;
  }
</style>