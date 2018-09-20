
<template>
    <el-button type="primary" class="circle audio-player" :class="playing ? 'Pause' : 'Play'" @click="togglePlayback">
        {{ playing ? 'Pause' : 'Play' }}

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
</style>
