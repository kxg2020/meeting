<template>
  <div class="file-list">
    <div v-for="(file, fileIndex) in files" :key="fileIndex" class="file-item">
      <div @click="viewFile(file)" style="display: flex;flex-direction: row;width: 100%">
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
        <div class="file-name">{{file.file_name}}</div>
      </div>
      <div class="file-btn" @click="removeFile(fileIndex)" v-if="showRemove">
        <i class="fa fa-window-close-o"></i>
      </div>
    </div>
    <van-popup v-model="showPreview" position="right" :overlay="false" class="prefile-popup">
      <div class="prefile-view" v-if="showFile">
        <template v-if="getFileType(showFile.file_name) == 'image'">
          <img :src="showFile.file_url" alt="">
        </template>
        <template v-else-if="getFileType(showFile.file_name) == 'video'">
          <video :src="showFile.file_url" controls="controls" width="100%"></video>
        </template>
        <template v-else>
          <div style="text-align: center;width: 100%;">
            此文件不支持预览
            <a target="_blank" :href="showFile.file_url">点击下载</a>
          </div>
        </template>
      </div>
      <div style="height: 100px;display: flex;justify-content: center;align-items: center">
        <van-button class="prefile-btn" plain type="danger" @click="hidePreview">关闭预览</van-button>
      </div>
    </van-popup>
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
      },
      preview: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        showPreview: false,
        showFile: null
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
            extensions: ["txt", "doc", "xls", "ppt", "docx", "xlsx", "pptx", "pdf"]
          }
        ]
        let ext = this.getFileExt(fileName)
        let type = "other"
        for (let fileExt of fileExtMap) {
          if (fileExt.extensions.includes(ext)) {
            type = fileExt.name
          }
        }
        console.log(type)
        return type
      },
      getFileExt(filename) {
        return filename.substr(filename.lastIndexOf(".") + 1).toLowerCase()
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
      },
      viewFile(file) {
        if (!this.preview) return
        this.showFile = file
        this.showPreview = true
      },
      hidePreview() {
        this.showPreview = false
        this.showFile = null
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

  .prefile-popup {
    width: 100%;
    height: 100%;
  }
  .prefile-btn{
    margin: 10px;
    display: flex;
    justify-content: center;
  }
  .prefile-view{
    width: 100%;
    height: calc(100% - 100px);
    display: flex;
    align-items: center;
  }
  .prefile-view img, .prefile-view video{
    width: 100%;
  }
  iframe{
    width: 100%;
    height: 100%;
    overflow: scroll;
  }
</style>