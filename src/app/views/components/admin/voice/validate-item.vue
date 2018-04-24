
<template>
  <div>
    <div class="player">
      <el-button type="text" @click="togglePlayback">{{ playing ? 'Pause' : 'Play' }}</el-button> &nbsp;
      <small>Duration: {{ duration }} seconds</small>
      &nbsp;
      <el-tag size="mini" :type="voice.status.style">{{ voice.status.label }}</el-tag>
      <span class="action">
        <el-button icon="el-icon-fa-check" class="circle" type="success"
          @click="onValidate(voice.id, 1)"
          :loading="loading"
          v-show="voice.status.value != 1">
        </el-button>
      </span>
      <small class="voice_text">
        {{ voicescript.text }}
      </small>
    </div>
    <el-progress
      :show-text="false"
      :percentage="Math.floor((progress * 100))"
      class="progress"
      :stroke-width="5"></el-progress>
  </div>
</template>

<script lang="js">
import VueHowler from 'vue-howler';

export default {
  mixins: [VueHowler],
  props: ['sources', 'voice', 'voicescript', 'uid'],

  data() {
    return {
      loading: false
    }
  },

  methods: {
    async onValidate(vid, statusValue) {
      this.loading = true

      await this.$store
        .dispatch('voices/validate', {
          id: vid,
          formData: { status: statusValue }
        })
        .then(async () => {
          this.loading = false

          return await this.$store.dispatch('uservoices/get_all', { query: {
            uid: this.$props.uid
          } })
        })
        .catch(err => {
          this.loading = false
        })
    }
  }
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
