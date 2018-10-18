<template>
  <div class="main">
    <div class="main-container">
      <div>
        <div class="member">
          <img class="member-avatar" src="https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg" alt="">
          <div class="member-name">WHNGQDCMHDXXF</div>
        </div>
        <van-cell-group class="user-group">
          <van-cell icon="records" title="菜单" is-link/>
        </van-cell-group>
        <van-cell-group>
          <van-cell icon="exchange" title="菜单1" is-link/>
          <van-cell icon="gold-coin" title="菜单2" is-link/>
          <van-cell icon="gift" title="菜单3" is-link/>
        </van-cell-group>
      </div>
    </div>
    <Tabbar :active="2"></Tabbar>
  </div>
</template>

<script>
  import Tabbar from '../../components/Tabbar'

  export default {
    name: "Member",
    data() {
      return {}
    },
    components: {
      Tabbar
    },
    created() {
      window.setTitle("个人中心")
    },
    methods: {}
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
  }
</style>