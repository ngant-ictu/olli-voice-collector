<lang src="./lang.yml"></lang>

<template>
  <div class="panel-body">
    <el-col :md="5" :xs="24">
      <h2>{{ $t('info.add.description') }}</h2>
      <p>{{ $t('info.add.extraDescription') }}</p>
    </el-col>
    <el-col :md="19" :xs="24">
      <el-col :md="10">
        <el-form autoComplete="on" label-position="left" :model="form" :rules="rules" ref="addForm">
          <el-form-item prop="fullname" :label="$t('label.name')">
            <el-input type="text" size="small" v-model="form.fullname"></el-input>
          </el-form-item>
          <el-form-item prop="email" :label="$t('label.email')">
            <el-input type="text" size="small" v-model="form.email"></el-input>
          </el-form-item>
          <el-form-item prop="password" :label="$t('label.password')">
            <el-input type="password" size="small" v-model="form.password"></el-input>
          </el-form-item>
          <el-form-item prop="groupid" :label="$t('label.group')">
            <el-select size="small" v-model="form.groupid" :placeholder="$t('label.selectGroup')" style="width: 100%" :loading="loading">
              <el-option v-for="item in formSource.groupList" :key="item" :label="item" :value="item">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item prop="status" :label="$t('label.status')">
            <el-select size="small" v-model="form.status" :placeholder="$t('label.selectStatus')" style="width: 100%" :loading="loading">
              <el-option v-for="item in formSource.statusList" :key="item.label" :label="item.label" :value="item.value">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item prop="verifytype" :label="$t('label.verifyType')">
            <el-select size="small" v-model="form.verifytype" :placeholder="$t('label.selectVerifyType')" style="width: 100%" :loading="loading">
              <el-option v-for="item in formSource.verifyList" :key="item.label" :label="item.label" :value="item.value">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item style="margin-top: 30px">
            <el-button type="primary" :loading="loading" @click.native.prevent="onSubmit"> {{ $t('default.add') }}
            </el-button>
            <el-button @click="onReset">{{ $t('default.reset') }}</el-button>
          </el-form-item>
        </el-form>
      </el-col>
      <el-col :md="10" style="margin-left: 20px; background-color: whitesmoke;padding: 20px;">
        <h4>
          Firebase Phone Authentication Test
        </h4>
        <el-form autoComplete="on" label-position="left" :model="phoneForm" ref="phoneForm">
          <el-form-item prop="number" label="Phone number (include region code)">
            <el-input type="text" size="small" v-model="phoneForm.number" placeholder="ex: +84979999999, +84121222111" @keyup.enter.native="onSend"></el-input>
            <el-button type="warning" :loading="loading" @click.native.prevent="onSend" size="mini" id="test-signin-button"> Send</el-button>
          </el-form-item>
          <el-form-item prop="number" label="Input 6 digit code you received via mobile phone" v-show="showVerify">
            <el-input type="text" size="small" v-model="phoneForm.code" placeholder="ex: 123456" autofocus @keyup.enter.native="onVerify"></el-input>
            <el-button type="success" :loading="loading" @click.native.prevent="onVerify" size="mini"> Verify</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-col>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import { Auth } from '~/plugins/firebase';
declare global {
  interface Window {
    recaptchaVerifier: any;
  }
}


@Component
export default class AddForm extends Vue {
  @Action('users/get_form_source') formsourceAction;
  @Action('users/add') addAction;
  @State(state => state.users.formSource) formSource;
  @Watch('$route')
  onPageChange() { this.initData(); };

  loading: boolean = false;
  form: object = {
    email: '',
    password: '',
    fullname: '',
    groupid: '',
    status: '',
    verifytype: ''
  };
  phoneForm = {
    number: '+84',
    code: ''
  };
  showVerify: boolean = false;
  confirmationResult: any;

  $refs: {
    addForm: HTMLFormElement
  }

  get rules() {
    return {
      fullname: [
        {
          required: true,
          message: this.$t('msg.nameIsRequired'),
          trigger: 'blur'
        }
      ],
      email: [
        {
          required: true,
          message: this.$t('msg.emailIsRequired'),
          trigger: 'blur'
        },
        {
          type: 'email',
          message: this.$t('msg.emailInvalid'),
          trigger: 'blur,change'
        }
      ],
      password: [
        {
          required: true,
          message: this.$t('msg.passwordIsRequired'),
          trigger: 'blur'
        }
      ],
      groupid: [
        {
          required: true,
          message: this.$t('msg.groupIsRequired'),
          trigger: 'change'
        }
      ],
      status: [
        {
          required: true,
          message: this.$t('msg.statusIsRequired'),
          trigger: 'change'
        }
      ],
      verifytype: [
        {
          required: true,
          message: this.$t('msg.verifyTypeIsRequired'),
          trigger: 'change'
        }
      ]
    };
  }

  onSubmit() {
    this.$refs.addForm.validate(async valid => {
      if (valid) {
        this.loading = true;

        await this.addAction({ formData: this.form })
          .then(res => {
              this.loading = false;

              this.$message({
                showClose: true,
                message: this.$t('msg.addSuccess').toString(),
                type: 'success',
                duration: 3 * 1000
              })

              this.$refs.addForm.resetFields();
          })
          .catch(err => {
            this.loading = false;
          })
      } else {
        return false;
      }
    });
  }

  onReset() { return this.$refs.addForm.resetFields(); }

  created() { return this.initData(); }

  async initData() { return await this.formsourceAction() }

  // Test phone number verification with firebase
  mounted() {
    window.recaptchaVerifier = new Auth.RecaptchaVerifier('test-signin-button', {
      'size': 'invisible',
      'callback': function(response) {
        // reCAPTCHA solved, allow signInWithPhoneNumber.
        console.log('reCaptcha resolved OK');

      },
      'expired-callback': function() {
        // Response expired. Ask user to solve reCAPTCHA again.
        // ...
      }
    });
  }

  onSend() {
    const self = this;
    self.loading = true;

    const appVerifier = window.recaptchaVerifier;
    Auth().signInWithPhoneNumber(self.phoneForm.number, appVerifier)
    .then(function (confirmationResult) {
      // SMS sent. Prompt user to type the code from the message, then sign the
      // user in with confirmationResult.confirm(code).
      self.showVerify = true;
      self.loading = false;
      self.phoneForm.number = '';
      self.confirmationResult = confirmationResult;
    }).catch(function (error) {
      // Error; SMS not sent
      console.log(error)
      self.loading = false;
    });
  }

  onVerify() {
    const self = this;
    self.loading = true;

    self.confirmationResult.confirm(self.phoneForm.code).then(function (result) {
      self.loading = false;
      self.phoneForm.code = '';
      // User signed in successfully.
      const user = result.user
      // Get verify ID Token to used in backend
      user.getIdToken(true)
        .then(idtoken => {
          self.$alert(idtoken, 'JWT Token get from Firebase', {
            confirmButtonText: 'OK'
          });
          self.showVerify = false;
          console.log(idtoken)
        })
        .catch(err => {
          console.log(err)
        })
    }).catch(function (error) {
      // User couldn't sign in (bad verification code?)
      console.log(error)
      self.loading = false;
    });
  }
}
</script>
