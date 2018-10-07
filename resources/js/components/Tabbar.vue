<template>
  <van-tabbar v-model="active">
    <van-tabbar-item icon="wap-home">首页</van-tabbar-item>
    <van-tabbar-item icon="chat">消息</van-tabbar-item>
    <van-tabbar-item icon="contact">个人中心</van-tabbar-item>
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
    }
  }
</script>

<style scoped>

</style>