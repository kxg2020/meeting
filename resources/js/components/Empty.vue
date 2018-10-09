<template>
  <div class="empty-box">
    <i :class="icon"></i>
    <div class="msg">{{msg}}</div>
    <template slot="-"></template>
  </div>
</template>

<script>
  export default {
    name: "Empty",
    props: {
      icon: {
        type: String,
        default: 'fa fa-envelope-open',
      },
      msg: {
        type: String,
        default: '没有内容'
      }
    }
  }
</script>

<style scoped>
  .empty-box{
    width: 100%;
    text-align: center;
  }
  .empty-box i{
    font-size: 50px;
    line-height: 150px;
    color: #c7bfbf;
  }
  .empty-box .msg{
    line-height: 2em;
  }
</style>