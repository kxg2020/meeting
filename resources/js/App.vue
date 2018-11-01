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
    }
  }
</script>

<style scoped>

</style>