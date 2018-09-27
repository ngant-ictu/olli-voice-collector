<template>
  <div>
    <span class="point">
      <small>Point</small>:
      <i class="el-icon-fa-spinner el-icon-fa-spin" v-if="loading"></i>
      <strong v-else>{{ point }}</strong>
    </span>
    <span class="tmp_point">
      <small>TMP Point</small>:
      <i class="el-icon-fa-spinner el-icon-fa-spin" v-if="loading"></i>
      <strong v-else>{{ tmppoint }}</strong>
    </span>
    <span class="record-times">
      <small>Record times</small>:
      <i class="el-icon-fa-spinner el-icon-fa-spin" v-if="loading"></i>
      <strong v-else>{{ recordtimes }}</strong>
    </span>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';
import { Action } from 'vuex-class';

@Component
export default class PointRecordItem extends Vue {
  @Prop() uid;
  @Action('users/get_point') getpointAction;

  loading: boolean = false;
  point: number = 0;
  recordtimes: number = 0;
  tmppoint: number = 0;

  async created() {
    this.loading = true;

    return await this.getpointAction({ id: this.uid })
      .then(res => {
        this.point = res.data.point;
        this.tmppoint = res.data.tmppoint;
        this.recordtimes = res.data.recordtimes;

        this.loading = false;
      })
      .catch(err => {
        this.loading = false;
      })
  }
}
</script>

<style scoped>
.point,
.record-times {
  display: block;
  width: 100%;
}
</style>
