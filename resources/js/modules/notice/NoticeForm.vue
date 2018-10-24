<template>
  <div class="main">
    <div class="main-container">
      <van-cell-group>
        <van-field
          v-model="notice.title"
          required
          clearable
          label="公告标题"
          placeholder="请输入公告标题"
        />
        <van-field
          v-model="notice.content"
          type="textarea"
          required
          clearable
          autosize
          label="公告内容"
          placeholder="请输入公告内容"
          class="notice-content"
        />
        <div class="upload-list">
          <FileList :files="notice.images" @remove="index => removeImage(index)"></FileList>
          <Upload :multiple="true" accept="image/*"
                  @success="data => uploadImageSuccess(data)"></Upload>
        </div>
      </van-cell-group>
      <div class="submit">
        <el-button class="submit-btn" type="primary" plain @click="submit" :loading="submitLoading">创建</el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import FileList from '../../components/FileList'
  import Upload from '../../components/Upload'
  export default {
    name: "NoticeForm",
    data() {
      return {
        notice: {
          title: '',
          content: '',
          images: [],
        },
        submitLoading: false
      }
    },
    components: {
      FileList,
      Upload
    },
    created() {

    },
    methods: {
      removeImage(index) {
        this.notice.images.splice(index, 1)
      },
      uploadImageSuccess(data) {
        this.notice.images.push(data)
      },
      submit() {
        this.submitLoading = true
        if (this.notice.title < 1) {
          this.submitLoading = false
          this.$toast("公告标题不能为空")
          return
        }
        if (this.notice.content < 1) {
          this.submitLoading = false
          this.$toast("公告内容不能为空")
          return
        }
        this.axios.post('/notice/create', this.notice).then(res => {
          this.$toast(res.msg)
          this.submitLoading = false
          setTimeout(() => {
            if (res.status) {
              this.$router.back()
            }
          }, 2000)
        }).catch(error => {
          this.submitLoading = false
        })
      }
    }
  }
</script>

<style scoped>
  .upload-list {
    margin-left: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
    padding-top: 15px;
  }
</style>