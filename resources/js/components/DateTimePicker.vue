<template>
  <div>
    <div class="datetime-box">
      <span @click="showDatePopup">{{date.Format('yyyy-MM-dd')}}</span>
      <span class="separator">/</span>
      <span @click="showTimePopup">{{time}}</span>
    </div>
    <!-- Date -->
    <van-popup v-model="showDate" position="bottom">
      <van-datetime-picker
        type="date"
        :min-date="minDate"
        @confirm="dateConfirm"
        @cancel="hideDatePopup"
      />
    </van-popup>
    <!-- Time -->
    <van-popup v-model="showTime" position="bottom">
      <van-picker :show-toolbar="true" :columns="dateLsit" @change="timeChange" @confirm="hideTimePopup" @cancel="hideTimePopup" />
    </van-popup>
  </div>
</template>

<script>
  export default {
    name: "DateTimePicker",
    props: {
      minDate:{
        type: Date,
      }
    },
    data () {
      return {
        date: new Date(),
        time: "08:00",
        showDate: false,
        showTime: false,
        dateLsit: [
          {
            values: ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'],
            defaultIndex: 8
          },
          {
            values: ['00','05','10','15','20','25','30','35','40','45','50','55'],
            defaultIndex: 0
          }
        ],
      }
    },
    created() {
      this.emitChange()
    },
    methods: {
      showDatePopup() {
        this.showDate = true
      },
      showTimePopup() {
        this.showTime = true
      },
      hideDatePopup() {
        this.showDate = false
      },
      hideTimePopup() {
        this.showTime = false
      },
      dateConfirm(value) {
        this.date = value
        this.emitChange()
        this.hideDatePopup()
      },
      timeChange(event, values) {
        let time = values.join(':')
        this.time = time
        this.emitChange()
      },
      emitChange() {
        let date = this.date.Format('yyyy-MM-dd') + ' ' + this.time
        date = date.substring(0,19)
        date = date.replace(/-/g,'/')
        let timestamp = ((new Date(date)).getTime()) / 1000
        this.$emit("change", timestamp)
      }
    }
  }
</script>

<style scoped>
  .separator{
    margin: 0 9px;
    font-weight: 700;
    color: #c0c4cc;
  }
</style>