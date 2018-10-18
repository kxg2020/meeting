<template>
  <div class="main">
    <div class="main-container">
      <h1>Notice</h1>
    </div>
    <Tabbar :active="1"></Tabbar>
  </div>
</template>

<script>
  import Tabbar from '../../components/Tabbar'

  export default {
    name: "Notice",
    data() {
      return {

      }
    },
    components: {
      Tabbar
    },
    created() {
      window.setTitle("新闻公告")
    },
    methods: {

    }
  }
</script>

<style scoped>

</style>