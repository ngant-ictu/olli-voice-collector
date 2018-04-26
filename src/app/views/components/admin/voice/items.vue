<lang src="./lang.yml"></lang>

<template>
  <section>
    <el-table :data="voices" style="width: 100%">
      <el-table-column>
        <template slot-scope="scope">
          <el-row>
            <el-col :md="4">
              <div class="avatar">
                <img v-if="scope.row.user.data.avatar !== ''" :src="scope.row.user.data.avatar" width="30" height="30">
                <img v-else src="/img/default_avatar.png" width="30" height="30">
              </div>
              <div class="fullname">
                <i v-if="scope.row.user.data.profile.data.gender.icon !== ''"
                  :style="`float: right; margin-left: 20px; color: ${scope.row.user.data.profile.data.gender.style}`"
                  :class="`el-icon-${scope.row.user.data.profile.data.gender.icon}`">
                </i>
                <code type="danger">{{ scope.row.user.data.mobilenumber }}</code>
                <p>
                  <small>{{ scope.row.user.data.fullname !== '' ? scope.row.user.data.fullname : '-/-'}}</small>
                </p>
              </div>
            </el-col>
            <el-col :md="20">
              <div class="info">
                <div class="item">
                  <strong>{{ scope.row.items.data.length }}</strong>
                  <span style="color: #409EFF"> Total</span>
                </div>

                <div class="item">
                  <el-badge :value="totalPending(scope.row) > 0 ? totalPending(scope.row) : null" class="item">
                    <el-tag type="warning">
                      Pending
                    </el-tag>
                  </el-badge>
                </div>

                <div class="item">
                  <strong>{{ totalApproved(scope.row) > 0 ? totalApproved(scope.row) : 0 }}</strong>
                  <span style="color: #67C23A"> Approved</span>
                </div>

                <div class="item">
                  <strong>{{ totalRejected(scope.row) > 0 ? totalRejected(scope.row) : 0 }}</strong>
                  <span style="color: #909399"> Rejected</span>
                </div>
              </div>
            </el-col>
          </el-row>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="150">
        <template slot-scope="scope">
          <div v-if="getValidateUser(scope.row).length > 0">
            <div class="avatar" v-for="(voiceitem, index) in getValidateUser(scope.row)" :key="index">
              <el-tooltip class="item" effect="dark" :content="voiceitem.validatedby.fullname">
                <img v-if="voiceitem.validatedby.avatar !== ''" :src="voiceitem.validatedby.avatar" width="30" height="30">
                <img v-else src="/img/default_avatar.png" width="30" height="30">
              </el-tooltip>
              <el-button size="mini" type="text"
                v-if="authUser.sub.id === voiceitem.validatedby.id"
                @click="onShowValidateForm(scope.row)"
                style="position: absolute; margin-left: 10px;">
                Continue
              </el-button>
            </div>
          </div>
          <div v-else>
            <el-button size="small" icon="el-icon-fa-flash"
              @click="onShowValidateForm(scope.row)">
              Validate
            </el-button>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <scroll-top :duration="1000" :timing="'ease'"></scroll-top>
    <validate-form :validateFormState="visible" :userId="userId" :onClose="onHideValidateForm"></validate-form>
  </section>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { Action, State } from 'vuex-class';
import ValidateForm from '~/components/admin/voice/validate-form.vue';

@Component({
  components: {
    ValidateForm
  }
})
export default class AdminVoiceItems extends Vue {
  @Prop() voices: any[];
  @Action('voices/get_all') listAction;
  @State(state => state.authUser) authUser;

  visible: boolean = false;
  userId: number = 0;

  totalPending(row) {
    return row.items.data.filter(voice => voice.status.value === '5').length
  }

  totalApproved(row) {
    return row.items.data.filter(voice => voice.status.value === '1').length
  }

  totalRejected(row) {
    return row.items.data.filter(voice => voice.status.value === '3').length
  }

  getValidateUser(row) {
    let flags = {};
    return row.items.data.filter(voice => {
      if (flags[voice.validatedby])
        return;

      if (voice.validatedby === 0)
        return false;

      flags[voice.validatedby] = true;
      return voice;
    });
  }

  onShowValidateForm(row) {
    this.visible = !this.visible;
    this.userId = row.uid;
  }

  onHideValidateForm() { this.visible = false; }
}
</script>

<style lang="scss" scoped>
.avatar {
  float: left;
  margin-right: 10px;
  padding-top: 10px;
  display: inline-block;
  img {
    border-radius: 30px !important;
  }
}
.fullname {
  float: left;
  display: inline-block;
  code {
    color: #409eff;
  }
  p {
    margin: 0 auto;
  }
}
.info {
  float: left;
  margin-left: 100px;
  margin-top: 10px;
  .item {
    display: inline-block;
    margin-left: 50px;
  }
}
</style>
