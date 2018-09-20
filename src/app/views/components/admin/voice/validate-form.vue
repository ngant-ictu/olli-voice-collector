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
      <el-table-column class-name="td-operation" width="150" label="RecordDate">
        <template slot-scope="scope">
          <div class="item recordDate">
              <small> 13-09-2018, 11:54 </small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="70" label="Drt (sec)">
        <template slot-scope="scope">
          <div class="item duration">
            <small class="sec-duration">10.52 </small>
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
      <el-table-column class-name="td-operation" width="120" label="Status">
        <template slot-scope="scope">
          <div class="item satus">
            <small :class="scope.row.status.style">{{ scope.row.status.label }}</small>
          </div>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="160" label="">
        <template slot-scope="scope">
          <div class="item action">
              <validate-item
              :sources="scope.row.filepath"
              :voice="scope.row"
              :voicescript="scope.row.voicescript.data"
              :uid="userId"
              ></validate-item>

            <el-button icon="el-icon-fa-check" class="circle" type="success"
              @click="onValidate(scope.row.id, 1)"
              :disabled="scope.row.status.value === '1' ? true : false">
            </el-button>
            <el-button icon="el-icon-fa-times" class="circle" type="danger"
              @click="onValidate(scope.row.id, 3)"
              :disabled="scope.row.status.value === '3' ? true : false">
            </el-button>
          </div>
        </template>
      </el-table-column>



    </el-table>
    <template slot="footer">
      <pagination-stay
        :totalItems="totalItems"
        :currentPage="page"
        :recordPerPage="recordPerPage"
        :handlePageChange="onPageChange">
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

@Component({
  components: {
    ValidateItem,
    PaginationStay
  }
})
export default class ValidateForm extends Vue {
  @Action("uservoices/get_form_source") formsourceAction;
  @Action("uservoices/get_all") getAction;
  @Action("voices/validate") validateAction;
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

  async onValidate(vid, statusValue) {
    // validate
    const newUservoices = await this.validateAction({
      id: vid,
      formData: { status: statusValue }
    });

    // reload uservoices state
    await this.loadData(this.page);
  }
}
</script>

<style scoped lang="scss">
div.el-dialog {
  width: 60%;
}
.player {
  background-color: whitesmoke;
}
.validated-item {
  .action {
    display: flex;
    display: -webkit-flex;
  }

  .voice_text {
    height: 24px;
    line-height: 24px;
    small {
      overflow: hidden;
      height: 24px;
      line-height: 24px;
      text-overflow: ellipsis;
      display: -webkit-inline-box;
      -webkit-line-clamp: 1;
      -webkit-box-orient: vertical;
      margin: 0;
    }
  }
  .el-button--success.is-disabled {
    opacity: 0.5;
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
  &.audio-player {
    padding: 0 !important;
    font-size: 0;
    position: relative;
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
.activeStatus {
  opacity: 0.5;
  pointer-events: none;
}
.success {
  color: rgb(103, 194, 58);
}
.warning,
.danger {
  color: #f56c6c;
}
</style>
