<lang src="./lang.yml"></lang>

<template>
  <el-form ref="form" label-position="top">
    <el-form-item prop="keyword">
      <el-input size="small" :placeholder="$t('form.search')"
        v-model="form.keyword"
        @keyup.enter.native="onFilter"
        clearable>
        <el-button slot="append" @click="onFilter"><i class="el-icon-fa-search"></i></el-button>
      </el-input>
    </el-form-item>
    <el-form-item prop="status" :label="$t('form.status')">
      <el-select clearable size="small" v-model="form.status" :placeholder="$t('default.all')">
        <el-option v-for="item in formSource.statusList" :key="item.value" :label="item.label" :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" size="small" @click="onFilter">{{ $t('default.filter') }}</el-button>
      <el-button size="small" @click="onReset">{{ $t('default.reset') }}</el-button>
    </el-form-item>
  </el-form>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
const querystring = require('querystring');

@Component
export default class FilterBar extends Vue {
  @Action('scripts/get_form_source') formsourceAction;
  @State(state => state.scripts.query) query;
  @State(state => state.scripts.formSource) formSource;
  @Watch('$route')
  onPageChange() { this.initData() }

  searchInputWidth: number = 100;
  form: object = {};

  onFilter() {
    this.query.page = 1;
    const pageUrl = `?${querystring.stringify(this.form)}&${querystring.stringify(this.query)}`;

    return this.$router.push(pageUrl);
  }

  onReset() { return this.$router.push('/admin/script'); }

  created() { return this.initData(); }

  async initData() {
    this.form = {
      keyword: this.$route.query.keyword || '',
      status: this.$route.query.status || ''
    };

    return await this.formsourceAction();
  }
}
</script>
