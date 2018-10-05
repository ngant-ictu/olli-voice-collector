<lang src="./lang.yml"></lang>

<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon" @click="onBack">
        <i class="el-icon-fa-angle-left"></i>
      </div>
      <breadcrumb :data="[
        { name: $t('page.index.title'), link: '/admin/user' },
        { name: $t('page.changepassword.title'), link: '' }
      ]">
      </breadcrumb>
    </el-col>
    <el-col :span="24">
      <change-password-form></change-password-form>
    </el-col>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component } from 'nuxt-property-decorator';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import ChangePasswordForm from '~/components/admin/user/change-password-form.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    ChangePasswordForm
  }
})
export default class AdminUserChangePassword extends Vue {
  head() {
    return {
      title: this.$t('page.changepassword.title'),
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: this.$t('page.changepassword.title')
        }
      ]
    };
  }

  loading: boolean = false;

  onBack() { return this.$router.go(-1); }
}

</script>

<style scoped>
.panel-body {
  margin-left: 0;
}

.filter-icon {
  cursor: pointer;
}
</style>
