
<template>
    <el-button type="primary" class="circle audio-player" @click="togglePlayback">
        {{ playing ? 'Pause' : 'Play' }}

  </el-button>
</template>

<script lang="js">
 import VueHowler from 'vue-howler';

export default {
  mixins: [VueHowler],
  props: ['sources'],

  data() {
    console.log(this.sources);
    return {

    }
  },

  methods: {
    play() {
      play
    }
  }
}
</script>

<style scoped lang="scss">
.player {
  background-color: whitesmoke;
  padding: 0 10px;
}
</style>
