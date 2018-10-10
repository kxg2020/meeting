<template>
  <div>
    <label :for="id" class="upload-btn">
      <i :class="icon"></i>
    </label>
    <template v-if="multiple">
      <input :id="id" type="file" ref="input" name="file" :accept="accept" @change="handleChange" class="upload-input" :multiple = multiple>
    </template>
    <template>
      <input :id="id" type="file" ref="input" name="file" @change="handleChange" class="upload-input">
    </template>
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
      },
      accept: {
        type: String,
        default: "image/*, audio/*, video/*, application/*"
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
        for (let index = 0; index < files.length; index++) {
          let formData = new FormData()
          formData.append('file', files[index])
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