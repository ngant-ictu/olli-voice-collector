<lang src="./lang.yml"></lang>

<template>
  <el-dialog
    :visible.sync="validateFormState"
    :before-close="onClose"
    :lock-scroll="true"
    v-on:open="onOpen"
    v-on:close="onClosed"
    top="0">
    <template slot="title">
      <el-select
        v-model="filterby"
        multiple
        placeholder="Filter by status" style="width: 95%;"
        @change="onFilter">
        <el-option
          v-for="item in formSource.statusList"
          :key="item.value"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </template>
    <el-table :data="uservoices" border style="width: 100%" class="validated-item player">
      <el-table-column class-name="td-operation" width="130" label="Record Date">
        <template slot-scope="scope">
          <div class="item recordDate">
              <small> {{ scope.row.humandatecreated }} </small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="80" label="Duration">
        <template slot-scope="scope">
          <div class="item-duration">
            <validate-item
              :sources="scope.row.filepath"
              :voice="scope.row"
              :voicescript="scope.row.voicescript.data"
              :uid="userId"
              ></validate-item>

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
      <el-table-column class-name="td-operation" width="80" label="Status">
        <template slot-scope="scope">
          <div class="item satus">
            <small :class="scope.row.status.style">{{ scope.row.status.label }}</small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="140" label="">
        <template slot-scope="scope">
          <div class="group-buttons">
            <validate-item
            :sources="scope.row.filepath"
            :voice="scope.row"
            :voicescript="scope.row.voicescript.data"
            :uid="userId">
            </validate-item>
            <validate-btn
              :id="scope.row.id"
              :currentStatus="scope.row.status.value"
              @loadData="loadData(page)"></validate-btn>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <template slot="footer">
      <pagination-stay
        :totalItems="totalItems"
        :currentPage="page"
        :recordPerPage="recordPerPage"
        :handlePageChange="onPageChange"
        class="custom-pagination">
      </pagination-stay>
    </template>
  </el-dialog>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { Action, State } from "vuex-class";
import ValidateItem from "~/components/admin/voice/validate-item.vue";
import PaginationStay from "~/components/admin/pagination-stay.vue";
import { Progress } from "element-ui";
import VueFilter from "vue-filter";
import ValidateBtn from '~/components/admin/voice/validate-btn.vue';

@Component({
  components: {
    ValidateItem,
    ValidateBtn,
    PaginationStay
  }
})
export default class ValidateForm extends Vue {
  @Action("uservoices/get_form_source") formsourceAction;
  @Action("uservoices/get_all") getAction;
  @State(state => state.uservoices.formSource)
  formSource;
  @State(state => state.uservoices.data)
  uservoices;
  @State(state => state.uservoices.totalItems)
  totalItems;
  @State(state => state.uservoices.recordPerPage)
  recordPerPage;
  @State(state => state.uservoices.page)
  page;

  @Prop() userId: number;
  @Prop() validateFormState: boolean;
  @Prop() onClose;

  loading: boolean = false;
  filterby: any = [];


  onFilter() {
    return this.loadData(1);
  }

  onPageChange(page) {
    return this.loadData(page);
  }

  onOpen() {
    return this.loadData(1);
  }

  onClosed() {
    return (this.filterby = []);
  }

  created() {
    return this.initData();
  }

  async initData() {
    return await this.formsourceAction();
  }

  async loadData(page) {
    return await this.getAction({
      query: {
        uid: this.userId,
        page: page,
        status: this.filterby
      }
    });
  }
}
</script>

<style  lang="scss">
div.el-dialog {
  width: 60%;
}
.player {
  background-color: whitesmoke;
}
.validated-item {
  .group-buttons {
    display: flex;
    display: -webkit-flex;

    .second-duration {
      display: none;
    }
    .el-button.circle {
      border-radius: 50% !important;
      padding: 7px !important;
      width: 30px;
      height: 30px;
      &.audio-player {
        padding: 0 !important;
        font-size: 0;
        position: relative;
        margin-right: 10px;
        &.Play:before {
          font-family: FontAwesome;
          content: "\f04b";
          font-size: 11px;
        }
        &.Pause:before {
          font-family: FontAwesome;
          content: "\f04c";
          font-size: 13px;
        }
      }
    }
  }
  .voice_text {
    line-height: 14px;
    small {
      word-break: break-word;
    }
  }
  .el-button--success.is-disabled {
    opacity: 0.5;
  }
  .progress {
    margin-bottom: 15px;
  }
  .voice_text {
    display: block;
    font-size: 14px;
  }

  .activeStatus {
    opacity: 0.5;
    pointer-events: none;
  }
  .success {
    color: rgb(103, 194, 58);
  }
  .warning {
    color: orange;
  }
  .danger {
    color: #f56c6c;
  }
  .item-duration .el-button {
    display: none;
  }
}
</style>
