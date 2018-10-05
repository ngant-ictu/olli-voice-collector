
<template>
  <div>
    <el-button type="primary" class="circle audio-player" :class="playing ? 'Pause' : 'Play'" @click="togglePlayback">
        {{ playing ? 'Pause' : 'Play' }}
  </el-button>
  <small class="second-duration">{{ setDuration() }}</small>
</div>
</template>

<script lang="js">
 import VueHowler from 'vue-howler';
 import VueFilter from 'vue-filter';
 import Vue from 'vue';
var VueTruncate = require('vue-truncate-filter');
Vue.use(VueTruncate)
export default {

  mixins: [VueHowler],
  props: ['sources', 'voice', 'voicescript', 'uid'],

  methods: {
    setDuration() {
      let seconds = this.duration
      return seconds.toPrecision(3);

    }
  }


}
</script>

<style  lang="scss">
</style>
