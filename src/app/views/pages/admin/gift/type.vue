<lang src="./lang.yml"></lang>

<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon"><i class="el-icon-fa-gift"></i></div>
      <breadcrumb :data="[
        { name: 'Gift Types', link: '/admin/gift/type' },
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
        <admin-gift-type-items :gifttypes="gifttypes"></admin-gift-type-items>
      </div>
    </el-col>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import Pagination from '~/components/admin/pagination.vue';
import AdminGiftTypeItems from '~/components/admin/gifttype/items.vue';
import FilterBar from '~/components/admin/gifttype/filter-bar.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    Pagination,
    FilterBar,
    AdminGiftTypeItems
  }
})
export default class AdminGiftTypePage extends Vue {
  @Action('gifttypes/get_all') listAction;
  @State(state => state.gifttypes.data) gifttypes;
  @State(state => state.gifttypes.totalItems) totalItems;
  @State(state => state.gifttypes.recordPerPage) recordPerPage;
  @State(state => state.gifttypes.query) query;
  @Watch('$route')
  onPageChange() { this.initData() }

  loading: boolean = false;

  head() {
    return {
      title: 'Gift Types',
      meta: [
        { hid: 'description', name: 'description', content: 'Gift Types' }
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
