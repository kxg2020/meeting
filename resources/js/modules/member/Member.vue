<template>
  <div class="main">
    <div class="main-container">
      <h1>Member</h1>
    </div>
    <Tabbar :active="2"></Tabbar>
  </div>
</template>

<script>
  import Tabbar from '../../components/Tabbar'

  export default {
    name: "Member",
    data() {
      return {

      }
    },
    components: {
      Tabbar
    },
    created() {
      window.setTitle("个人中心")
    },
    methods: {

    }
  }
</script>

<style scoped>

</style>