<lang src="./lang.yml"></lang>

<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon"><i class="el-icon-fa-users"></i></div>
      <breadcrumb :data="[
        { name: $t('page.index.title'), link: '/admin/user' },
        { name: $t('default.list'), link: '' }
      ]" :totalItems="totalItems">
      </breadcrumb>
      <div class="top-right-toolbar">
        <nuxt-link to="/admin/user/add">
          <el-button type="text" icon="el-icon-plus">{{ $t('default.add') }}</el-button>
        </nuxt-link>
        <pagination :totalItems="totalItems" :currentPage="query.page" :recordPerPage="recordPerPage"></pagination>
      </div>
    </el-col>
    <el-col :span="24">
      <div class="filter-container">
        <filter-bar></filter-bar>
      </div>
      <div class="panel-body">
        <admin-user-items :users="users"></admin-user-items>
      </div>
    </el-col>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import Pagination from '~/components/admin/pagination.vue';
import AdminUserItems from '~/components/admin/user/items.vue';
import FilterBar from '~/components/admin/user/filter-bar.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    Pagination,
    AdminUserItems,
    FilterBar
  }
})
export default class AdminUserPage extends Vue {
  @Action('users/get_all') listAction;
  @State(state => state.users.data) users;
  @State(state => state.users.totalItems) totalItems;
  @State(state => state.users.recordPerPage) recordPerPage;
  @State(state => state.users.query) query;
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
