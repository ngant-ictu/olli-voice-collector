
<template>
    <el-button type="primary" class="circle" @click="togglePlayback">
        {{ playing ? 'Pause' : 'Play' }}
        <i class="el-icon-caret-right"></i>
  </el-button>
</template>

<script lang="js">
 import VueHowler from 'vue-howler';

export default {
  mixins: [VueHowler],
  props: ['sources', 'voice', 'voicescript', 'uid'],

  data() {

    return {

    }
  },

  methods: {

  }
}
</script>

<style scoped lang="scss">
.player {
  background-color: whitesmoke;
  padding: 0 10px;
}
</style>
