<lang src="./lang.yml"></lang>

<template>
  <div class="panel-body">
    <el-col :md="5" :xs="24">
      <h2>{{ $t('info.changepassword.description') }}</h2>
      <p>{{ $t('info.changepassword.extraDescription') }}</p>
    </el-col>
    <el-col :md="19" :xs="24">
      <el-col :md="10">
        <el-form autoComplete="on" label-position="left" class="changepassword-form" :model="form" :rules="rules" ref="changePasswordForm">
          <el-form-item prop="oldpassword" :label="$t('label.oldPassword')">
            <el-input type="password" size="small" v-model="form.oldpassword"></el-input>
          </el-form-item>
          <el-form-item prop="newpassword" :label="$t('label.newPassword')">
            <el-input type="password" size="small" v-model="form.newpassword"></el-input>
          </el-form-item>
          <el-form-item prop="repeatnewpassword" :label="$t('label.repeatNewPassword')">
            <el-input type="password" size="small" v-model="form.repeatnewpassword"></el-input>
          </el-form-item>
          <el-form-item style="margin-top: 30px">
            <el-button type="primary" :loading="loading" @click.native.prevent="onSubmit"> {{ $t('label.changePassword') }}
            </el-button>
            <el-button @click="onReset">{{ $t('default.reset') }}</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-col>
  </div>
</template>

<script lang="ts">
import { Vue, Component } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';

@Component
export default class ChangePasswordForm extends Vue {
  @Action('users/change_password') changePasswordAction;

  loading: boolean = false;
  form: object = {
    oldpassword: '',
    newpassword: '',
    repeatnewpassword: ''
  };

  $refs: {
    changePasswordForm: HTMLFormElement
  }

  get rules() {
    return {
      oldpassword: [
        {
          required: true,
          message: this.$t('msg.oldPasswordIsRequired'),
          trigger: 'blur'
        }
      ],
      newpassword: [
        {
          required: true,
          message: this.$t('msg.newPasswordIsRequired'),
          trigger: 'blur'
        }
      ],
      repeatnewpassword: [
        {
          required: true,
          message: this.$t('msg.repeatNewPasswordIsRequired'),
          trigger: 'blur'
        }
      ]
    };
  }

  onSubmit() {
    this.$refs.changePasswordForm.validate(async valid => {
      if (valid) {
        this.loading = true;

        await this.changePasswordAction({ formData: this.form })
          .then(res => {
            this.loading = false;

            // flash message
            this.$message({
              showClose: true,
              message: this.$t('msg.changePasswordSuccess').toString(),
              type: 'success',
              duration: 3 * 1000
            })
          })
          .catch(err => {
            this.loading = false;
          });
      } else {
        return false;
      }
    });
  }

  onReset() { this.$refs.changePasswordForm.resetFields(); }
}
</script>
