<lang src="./lang.yml"></lang>

<template>
  <section>
    <el-table :data="voices" border style="width: 100%" class="voices-item">

      <el-table-column class-name="td-operation" width="" label="User">
        <template slot-scope="scope">
          <div class="fullname">
            <p> <small>{{ scope.row.user.data.fullname !== '' ? scope.row.user.data.fullname : '-/-'}}</small></p>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="" label="Phone">
        <template slot-scope="scope">
          <div class=" item numberphone">
            <small><code type="danger">{{ scope.row.user.data.mobilenumber }}</code></small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="100" label="Total">
        <template slot-scope="scope">
          <div class="item Total">
            <small>{{ scope.row.items.data.length }}</small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="100" label="Pending">
        <template slot-scope="scope">
          <div class="item pending">
            <small>{{ totalPending(scope.row) > 0 ? totalPending(scope.row) : 0 }}</small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="100" label="Approved">
        <template slot-scope="scope">
          <div class="item approved">
           <small>{{ totalApproved(scope.row) > 0 ? totalApproved(scope.row) : 0 }}</small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="100" label="Rejected">
        <template slot-scope="scope">
          <div class="item rejected">
            <small>{{ totalRejected(scope.row) > 0 ? totalRejected(scope.row) : 0 }}</small>

          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="100" label="Status">
        <template slot-scope="scope">
          <div class="item status">
            <small v-if="totalPending(scope.row) === 0" style="color: #67C23A"> Done</small>
            <small v-if="totalPending(scope.row) > 0" style="color: orange"> Pending</small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="" label="Pending Date">
        <template slot-scope="scope">
          <div class="item pendingdate">
            <small>{{ scope.row.user.data.humandatecreated }}</small>
          </div>
        </template>
      </el-table-column>

      <el-table-column class-name="td-operation" width="" label="Validated by">
        <template slot-scope="scope">
          <div v-if="getValidateUser(scope.row).length > 0">
            <div class="avatar" v-for="(voiceitem, index) in getValidateUser(scope.row)" :key="index">
              <el-tooltip class="item" effect="dark" :content="voiceitem.validatedby.fullname">
                <small>{{ voiceitem.validatedby.fullname }}</small>
              </el-tooltip>

            </div>
          </div>

        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="" >
        <template slot-scope="scope">
          <div v-if="getValidateUser(scope.row).length > 0">
            <div class="avatar" v-for="(voiceitem, index) in getValidateUser(scope.row)" :key="index">
              <small
                v-if="authUser.sub.id === voiceitem.validatedby.id"
                @click="onShowValidateForm(scope.row)" class="primary" plain>
                Continue
              </small>
            </div>
          </div>
          <div v-else>
            <small class="primary" plain
              @click="onShowValidateForm(scope.row)">
              Validate
            </small>
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
import { Action, State } from "vuex-class";
import ValidateForm from "~/components/admin/voice/validate-form.vue";

@Component({
  components: {
    ValidateForm
  }
})
export default class AdminVoiceItems extends Vue {
  @Prop() voices: any[];
  @Action("voices/get_all") listAction;
  @State(state => state.authUser)
  authUser;
  @Prop() props: ["sources", "voice", "voicescript", "uid"];

  visible: boolean = false;
  userId: number = 0;

  totalPending(row) {
    return row.items.data.filter(voice => voice.status.value === "5").length;
  }

  totalApproved(row) {
    return row.items.data.filter(voice => voice.status.value === "1").length;
  }

  totalRejected(row) {
    return row.items.data.filter(voice => voice.status.value === "3").length;
  }

  getValidateUser(row) {
    let flags = {};
    return row.items.data.filter(voice => {
      if (flags[voice.validatedby]) return;

      if (voice.validatedby === 0) return false;

      flags[voice.validatedby] = true;
      return voice;
    });
  }

  onShowValidateForm(row) {
    this.visible = !this.visible;
    this.userId = row.uid;
  }

  onHideValidateForm() {
    this.visible = false;
  }
}
</script>

<style lang="scss">
.voices-item {
  &.el-table--enable-row-transition .el-table__body td {
    padding: 2px 0;
  }
  .avatar {
    float: left;
    margin-right: 10px;
    display: inline-block;
    img {
      border-radius: 30px !important;
    }
  }
  .fullname {
    float: left;
    display: inline-block;

    p {
      margin: 0 auto;
    }
  }

  .numberphone {
    code {
      color: #409eff;
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
  .primary {
    color: #409eff;
    cursor: pointer;
  }
}
</style>
