<template>
  <div class="main">
    <router-view />
    <van-tabbar v-model="activeIndex" @change="change">
      <van-tabbar-item v-for="(navigator, index) in navigators" :icon="navigator.icon" :key="index">{{navigator.name}}</van-tabbar-item>
    </van-tabbar>
  </div>
</template>

<script>
  export default {
    name: "Root",
    data () {
      return {
        navigators: [
          {
            name: '首页',
            icon: 'wap-home',
            path: '/index'
          },
          {
            name: '新闻公告',
            icon: 'chat',
            path: '/notices'
          },
          {
            name: '个人中心',
            icon: 'contact',
            path: '/member'
          },
        ],
        activeIndex: 0
      }
    },
    created () {
      for (let index in this.navigators) {
        if (this.$route.path == this.navigators[index].path) {
          this.activeIndex = parseInt(index)
        }
      }
    },
    methods: {
      change(active) {
        this.activeIndex = active
        this.$router.push({path: this.navigators[active].path})
      }
    }
  }
</script>

<style scoped>

</style>