<lang src="./lang.yml"></lang>

<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon"><i class="el-icon-fa-text-width"></i></div>
      <breadcrumb :data="[
        { name: $t('page.index.title'), link: '/admin/script' },
        { name: $t('default.list'), link: '' }
      ]" :totalItems="totalItems">
      </breadcrumb>
      <div class="top-right-toolbar">
        <pagination :totalItems="totalItems" :currentPage="query.page" :recordPerPage="recordPerPage"></pagination>
      </div>
    </el-col>
    <el-col :span="24">
      <div class="filter-container">
        <filter-bar></filter-bar>
      </div>
      <div class="panel-body">
        <import-button style="text-align: right;"></import-button>
        <admin-script-items :scripts="scripts"></admin-script-items>
      </div>
    </el-col>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import Pagination from '~/components/admin/pagination.vue';
import AdminScriptItems from '~/components/admin/script/items.vue';
import FilterBar from '~/components/admin/script/filter-bar.vue';
import ImportButton from '~/components/admin/script/import-button.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    Pagination,
    AdminScriptItems,
    FilterBar,
    ImportButton
  }
})
export default class AdminScriptPage extends Vue {
  @Action('scripts/get_all') listAction;
  @State(state => state.scripts.data) scripts;
  @State(state => state.scripts.totalItems) totalItems;
  @State(state => state.scripts.recordPerPage) recordPerPage;
  @State(state => state.scripts.query) query;
  @Watch('$route')
  onPageChange() { this.initData() }

  loading: boolean = false;

  head() {
    return {
      title: this.$t('page.index.title'),
      meta: [
        { hid: 'description', name: 'description', content: this.$t('title') }
      ]
    };
  }

  created() { this.initData(); }

  async initData() {
    this.loading = true;

    return await this.listAction({ query: this.$route.query })
      .then(() => {
        this.loading = false;
      })
      .catch(e => {
        this.loading = false;
      });
  }
}
</script>
