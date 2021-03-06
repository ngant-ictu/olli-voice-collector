<lang src="./lang.yml"></lang>

<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon"><i class="el-icon-fa-gift"></i></div>
      <breadcrumb :data="[
        { name: $t('page.index.title'), link: '/admin/gift' },
        { name: $t('default.list'), link: '' }
      ]" :totalItems="totalItems">
      </breadcrumb>
      <div class="top-right-toolbar">
        <el-button size="mini" type="text" icon="el-icon-plus" @click="onShowAddForm">
          {{ $t('default.add') }}
        </el-button>
        <el-button size="mini" type="primary" @click="onShowDefineForm">
          {{ $t('label.define') }}
        </el-button>
        <pagination :totalItems="totalItems" :currentPage="query.page" :recordPerPage="recordPerPage"></pagination>
      </div>
    </el-col>
    <el-col :span="24">
      <div class="filter-container">
        <filter-bar></filter-bar>
      </div>
      <div class="panel-body">
        <admin-gift-items :gifts="gifts"></admin-gift-items>
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
import Pagination from '~/components/admin/pagination.vue';
import AdminGiftItems from '~/components/admin/gift/items.vue';
import FilterBar from '~/components/admin/gift/filter-bar.vue';
import DefineTypeForm from '~/components/admin/gift/define-type-form.vue';
import AddForm from '~/components/admin/gift/add-form.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    Pagination,
    FilterBar,
    AdminGiftItems,
    DefineTypeForm,
    AddForm
  }
})
export default class AdminGiftPage extends Vue {
  @Action('gifts/get_all') listAction;
  @State(state => state.gifts.data) gifts;
  @State(state => state.gifts.totalItems) totalItems;
  @State(state => state.gifts.recordPerPage) recordPerPage;
  @State(state => state.gifts.query) query;
  @Watch('$route')
  onPageChange() { this.initData() }

  loading: boolean = false;
  defineFormVisible: boolean = false;
  addFormVisible: boolean = false;

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
