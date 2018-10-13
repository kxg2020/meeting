<template>
  <div class="file-list">
    <div v-for="(file, fileIndex) in files" :key="fileIndex" class="file-item">
      <template v-if="getFileType(file.file_name) == 'image'">
        <div class="file-thumb">
          <img :src="file.file_url" alt="" class="file-image">
        </div>
      </template>
      <template v-else-if="getFileType(file.file_name) == 'video'">
        <div class="file-thumb">
          <i class="fa fa-file-video-o"></i>
        </div>
      </template>
      <template v-else-if="getFileType(file.file_name) == 'doc'">
        <div class="file-thumb">
          <i class="fa fa-file-word-o"></i>
        </div>
      </template>
      <template v-else>
        <div class="file-thumb">
          <i class="fa fa-file"></i>
        </div>
      </template>
      <div class="file-name" v-if="showDownload" @click="downloadFile(file.file_url)">{{file.file_name}}</div>
      <div class="file-name" v-else>{{file.file_name}}</div>
      <div class="file-btn" @click="removeFile(fileIndex)" v-if="showRemove">
        <i class="fa fa-window-close-o"></i>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "FileList",
    props: {
      files: {
        type: Array,
      },
      showRemove: {
        type: Boolean,
        default: true
      },
      showDownload: {
        type: Boolean,
        default: false
      }
    },
    methods: {
      getFileType(fileName) {
        let fileExtMap = [
          {
            name: "image",
            extensions: ["bmp", "gif", "jpg", "jpeg", "pic", "png", "tif"]
          },
          {
            name: "video",
            extensions: ["wmv", "asf", "asx", "rm", " rmvb", "mp4", "mp3", "3gp", "mov", "m4v", "avi", "dat", "mkv", "flv", "vod"]
          },
          {
            name: "doc",
            extensions: ["txt", "doc", "xls", "ppt", "docx", "xlsx", "pptx"]
          }
        ]
        let ext = fileName.substr(fileName.lastIndexOf(".") + 1).toLowerCase()
        let type = "other"
        for (let fileExt of fileExtMap) {
          if (fileExt.extensions.includes(ext)) {
            type = fileExt.name
          }
        }
        console.log(type)
        return type
      },
      removeFile(index) {
        this.$dialog.confirm({
          title: '提示',
          message: '是否移除此文件'
        }).then(() => {
          this.$emit("remove", index)
        }).catch(() => {

        })
      },
      downloadFile(url) {
        this.$toast("todo download file")
      }
    }
  }
</script>

<style scoped>
  .file-list {

  }

  .file-item {
    display: flex;
    flex-direction: row;
    margin: 5px 0;
    line-height: 50px;
    color: rgba(69, 90, 100, .6);
  }

  .file-thumb {
    display: inline-block;
    width: 50px;
    height: 50px;
    text-align: center;
    font-size: 25px;
    color: #ccc;
    border: 1px solid #ccc;
    line-height: 50px;
    border-radius: 5px;
    margin-right: 5px;
  }

  .file-image {
    width: 100%;
    height: 100%;
    border-radius: 5px;
  }

  .file-thumb i {

  }

  .file-name {
    height: 50px;
    line-height: 25px;
    font-size: 14px;
    width: calc(100% - 110px);
    overflow: hidden;
    text-overflow: ellipsis;
    word-wrap: break-word;
  }

  .file-btn {
    width: 50px;
    height: 50px;
    text-align: center;
    color: red;
  }
</style>