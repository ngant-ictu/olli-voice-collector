
<template>
    <el-table :data="uservoices" border style="width: 100%" class="validated-item player">
      <el-table-column class-name="td-operation" width="" label="RecordDate">
        <template slot-scope="scope">
          <div class="item recordDate">
              <small> {{ scope.row.humandatecreated }}</small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="" label="Duration (sec)">
        <template slot-scope="scope">
          <div class="item duration">
            <span>5.5259183673469385 </span>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="" label="Script">
        <template slot-scope="scope">
          <div class="item voice_text">
            <small>{{ scope.row.voicescript.data.text }}</small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="" label="Status">
        <template slot-scope="scope">
          <div class="item satus">
            <el-tag size="mini" :type="scope.row.status.style">{{ scope.row.status.label }}</el-tag>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="" label="Status">
        <template slot-scope="scope">
          <div class="item action">
            <el-button type="primary" class="circle" @click="togglePlayback">
              <!-- {{ playing ? 'Pause' : 'Play' }} -->
              <i class="el-icon-caret-right"></i>
              </el-button>
            <el-button icon="el-icon-fa-check" class="circle" type="success"
              @click="onValidate(scope.row.id, 1)"
              :loading="loading"
              v-show="scope.row.status.value != 1">
            </el-button>
            <el-button icon="el-icon-fa-times" class="circle" type="danger"
              @click="onValidate(scope.row.id, 3)"
              :loading="loading"
              v-show="scope.row.status.value == 5 || scope.row.status.value == 1">
            </el-button>
          </div>
        </template>
      </el-table-column>
    </el-table>
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
.validated-item {
  display: flex;
  display: -webkit-flex;
  padding: 15px 10px;
  align-items: center;
  -webkit-align-items: center;
  .item {
    padding: 0 10px;
    &.recordDate {
      width: 121px;
    }
    &.duration {
      width: 142px;
      span {
        overflow: hidden;
        width: 23px;
        line-height: 10px;
        text-overflow: ellipsis;
        display: -webkit-inline-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        margin: 0;
      }
    }
    &.action {
      width: 135px;
    }
  }

  .voice_text {
    height: 24px;
    line-height: 24px;
    small {
      overflow: hidden;
      width: 230px;
      height: 24px;
      line-height: 24px;
      text-overflow: ellipsis;
      display: -webkit-inline-box;
      -webkit-line-clamp: 1;
      -webkit-box-orient: vertical;
      margin: 0;
    }
  }
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
