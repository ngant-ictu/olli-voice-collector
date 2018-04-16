<lang>

</lang>

<template>
  <div>
    <div class="player">
      <el-button type="text" @click="togglePlayback">{{ playing ? 'Pause' : 'Play' }}</el-button> &nbsp;
      <small>Duration: {{ duration }} seconds</small>
      <span class="action">
        <el-button icon="el-icon-fa-check" class="circle" type="success"></el-button>
        <el-button icon="el-icon-fa-times" class="circle" type="danger"></el-button>
      </span>
      <small class="voice_text">
        {{ voicescript.text }}
      </small>
    </div>
    <el-progress
      :show-text="false"
      :percentage="Math.floor((progress * 100))"
      color="#8e71c7"
      class="progress"
      :stroke-width="5"></el-progress>
  </div>
</template>

<script lang="js">
import VueHowler from 'vue-howler';

export default {
  mixins: [VueHowler],
  props: ['sources', 'voicescript'],
}
</script>

<style scoped lang="scss">
.player {
  background-color: whitesmoke;
  padding: 0 10px;
}
.action {
  float: right;
  margin-top: 20px;
  clear: both;
}
.progress {
  margin-bottom: 15px;
}
.voice_text {
  display: block;
  font-size: 14px;
}
.circle {
  border-radius: 50% !important;
  padding: 7px !important;
  width: 30px;
}
</style>
