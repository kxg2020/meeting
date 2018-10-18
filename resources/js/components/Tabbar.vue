<template>
  <van-tabbar v-model="active" @change="change">
    <van-tabbar-item v-for="(navigator, index) in navigators" :icon="navigator.icon" :key="index">{{navigator.name}}</van-tabbar-item>
  </van-tabbar>
</template>

<script>
  export default {
    name: "Tabbar",
    props: {
      active: {
        type: Number,
        default: 0
      }
    },
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
        ]
      }
    },
    methods: {
      change(active) {
        this.$router.push({path: this.navigators[active].path})
      }
    }
  }
</script>

<style scoped>

</style>