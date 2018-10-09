<template>
  <div class="debug" v-if="debug">
    <div @click="showError = !showError" class="debug-btn">
      <i class="fa fa-warning"></i>
    </div>
    <div class="error-list" v-if="showError" @click="addError">
      <div v-for="(error, errorIndex) in errorList" :key="errorIndex" class="error-item">
        {{error}}
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "Debug",
    data() {
      return {
        debug: false,
        showError: false,
        errorList: []
      }
    },
    created() {
      let _this = this
      if (process.env.NODE_ENV == 'development') {
        _this.debug = true
        window.onerror = function (errorMsg, url, lineNumber, column, errorObj) {
          _this.errorList.push('Error: ' + errorMsg + ' Script: ' + url + ' Line: ' + lineNumber + ' Column: ' + column + ' StackTrace: ' + errorObj)
        }
      }
    }
  }
</script>

<style scoped>
  .debug-btn{
    position: fixed;
    right: 10px;
    bottom: 10px;
    width: 20px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border: 1px solid #eee;
    border-radius: 5px;
    z-index: 10001;
  }
  .error-list{
    width: 100%;
    height: 60%;
    position: fixed;
    bottom: 0;
    z-index: 10000;
    background-color: black;
    color: #FFFFFF;
    overflow: scroll;
  }
  .error-item{
    border-bottom: 1px solid #FFFFFF;
    margin-bottom: 10px;
  }
</style>