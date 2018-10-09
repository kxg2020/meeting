<template>
  <div id="app">
    <router-view :key="key"></router-view>
    <Debug></Debug>
  </div>
</template>

<script>
  import Debug from './components/Debug'
  export default {
    name: "App",
    computed: {
      key() {
        return this.$route.name !== undefined ? this.$route.name + +new Date() : this.$route + +new Date()
      }
    },
    components: {
      Debug
    }
  }
</script>

<style scoped>

</style>