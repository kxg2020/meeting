<template>
  <div>
    <label :for="id" class="upload-btn">
      <i :class="icon"></i>
    </label>
    <input :id="id" type="file" ref="input" name="file" @change="handleChange" class="upload-input" :multiple = multiple>
  </div>
</template>

<script>
  export default {
    name: "Upload",
    props: {
      icon: {
        type: String,
        default: 'fa fa-camera'
      },
      name: {
        type: String,
        default: 'file',
      },
      multiple: {
        type: Boolean,
        default: false
      }
    },
    data () {
      return {
        id: Math.random().toString(36).substring(2),
      }
    },
    methods: {
      handleChange(event) {
        let files = event.target.files
        if (files.length < 1) return
        for (let file of files) {
          let formData = new FormData()
          formData.append('file', file)
          this.axios.post('/upload', formData).then(res => {
            if (res.status) {
              this.$emit('success', res.data)
            } else {
              this.$toast(res.msg)
            }
          }).catch(err => {
            console.log(err)
          })
        }
      }
    }
  }
</script>

<style scoped>
  .upload-btn {
    display: inline-block;
    width: 50px;
    height: 50px;
    text-align: center;
    font-size: 25px;
    color: #ccc;
    border: 1px dashed #ccc;
    line-height: 50px;
    border-radius: 5px;
  }
  .upload-input{
    display: none;
  }
</style>