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
    <el-row>
      <el-col :md="24" :xs="24" v-for="(voice, index) in uservoices" :key="index">
        <validate-item
          :sources="[voice.filepath]"
          :voice="voice"
          :voicescript="voice.voicescript.data"
          :uid="userId">
        </validate-item>
      </el-col>
    </el-row>
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
import { Vue, Component, Prop } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import ValidateItem from '~/components/admin/voice/validate-item.vue';
import PaginationStay from '~/components/admin/pagination-stay.vue';

@Component({
  components: {
    ValidateItem,
    PaginationStay
  }
})
export default class ValidateForm extends Vue {
  @Action('uservoices/get_form_source') formsourceAction;
  @Action('uservoices/get_all') getAction;
  @State(state => state.uservoices.formSource) formSource;
  @State(state => state.uservoices.data) uservoices;
  @State(state => state.uservoices.totalItems) totalItems;
  @State(state => state.uservoices.recordPerPage) recordPerPage;
  @State(state => state.uservoices.page) page;

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
    return this.filterby = [];
  }

  created() { return this.initData(); }

  async initData() { return await this.formsourceAction() }

  async loadData(page) {
    return await this.getAction({ query: {
      uid: this.userId,
      page: page,
      status: this.filterby
    } });
  }
}
</script>
