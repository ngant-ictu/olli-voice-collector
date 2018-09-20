
<template>
  <div>
    <el-button type="primary" class="circle audio-player" :class="playing ? 'Pause' : 'Play'" @click="togglePlayback">
        {{ playing ? 'Pause' : 'Play' }}
  </el-button>
  <small class="second-duration">{{ duration }}</small>
</div>
</template>

<script lang="js">
 import VueHowler from 'vue-howler';

export default {
  mixins: [VueHowler],
  // props: ['sources', 'voice', 'voicescript', 'uid'],


}
</script>

<style  lang="scss">
</style>
