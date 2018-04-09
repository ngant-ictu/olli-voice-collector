<!-- <lang src="./lang.yml"></lang> -->

<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon"><i class="el-icon-fa-gift"></i></div>
      <breadcrumb :data="[
        { name: $t('page.index.title'), link: '/admin/gift' },
        { name: $t('default.list'), link: '' }
      ]" >
      </breadcrumb>
      <div class="top-right-toolbar">
        <!-- <pagination :totalItems="totalItems" :currentPage="query.page" :recordPerPage="recordPerPage"></pagination> -->
      </div>
    </el-col>
    <el-col :span="24">
      <div class="filter-container">
        <!-- <filter-bar></filter-bar> -->
      </div>
      <div class="panel-body">
        <el-button size="small" type="primary" @click="onShowDefineForm">
          Define
        </el-button>
        <el-button size="small" type="success" @click="onShowAddForm">
          Create
        </el-button>
        <!-- <admin-script-items :scripts="scripts"></admin-script-items> -->
      </div>
    </el-col>
    <define-type-form :defineFormState="defineFormVisible" :onClose="onHideDefineForm"></define-type-form>
    <add-form :addFormState="addFormVisible" :onClose="onHideAddForm"></add-form>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
// import Pagination from '~/components/admin/pagination.vue';
// import AdminScriptItems from '~/components/admin/script/items.vue';
// import FilterBar from '~/components/admin/script/filter-bar.vue';
import DefineTypeForm from '~/components/admin/gift/define-type-form.vue';
import AddForm from '~/components/admin/gift/add-form.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    // Pagination,
    // AdminScriptItems,
    DefineTypeForm,
    AddForm
  }
})
export default class AdminScriptPage extends Vue {
  // @Action('scripts/get_all') listAction;
  // @State(state => state.scripts.data) scripts;
  // @State(state => state.scripts.totalItems) totalItems;
  // @State(state => state.scripts.recordPerPage) recordPerPage;
  // @State(state => state.scripts.query) query;
  // @Watch('$route')
  // onPageChange() { this.initData() }

  loading: boolean = false;
  defineFormVisible: boolean = false;
  addFormVisible: boolean = false;

  //
  // head() {
  //   return {
  //     title: this.$t('page.index.title'),
  //     meta: [
  //       { hid: 'description', name: 'description', content: this.$t('title') }
  //     ]
  //   };
  // }
  //
  // created() { this.initData(); }
  //
  // async initData() {
  //   this.loading = true;
  //
  //   return await this.listAction({ query: this.$route.query })
  //     .then(() => {
  //       this.loading = false;
  //     })
  //     .catch(e => {
  //       this.loading = false;
  //     });
  // }

  onShowDefineForm() {
    this.defineFormVisible = !this.defineFormVisible;
  }

  onHideDefineForm() { this.defineFormVisible = false; }

  onShowAddForm() {
    this.addFormVisible = !this.addFormVisible;
  }

  onHideAddForm() { this.addFormVisible = false; }
}
</script>
