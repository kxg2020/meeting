<template>
  <div id="app">
    <router-view :key="key"></router-view>
  </div>
</template>

<script>
  export default {
    name: "App",
    computed: {
      key() {
        return this.$route.name !== undefined ? this.$route.name + +new Date() : this.$route + +new Date()
      }
    },
    created() {
      if (window.route_path.length > 0) {
        let route_path = window.route_path
        window.route_path = ''
        this.$router.push({path: route_path})
      }
      if (window.msg != '') {
        this.$dialog.alert({
          title: '提示信息',
          message: window.msg
        })
      }
    }
  }
</script>

<style scoped>

</style>