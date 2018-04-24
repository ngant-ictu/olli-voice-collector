<lang src="./lang.yml"></lang>

<template>
  <el-dialog
    :visible.sync="editFormState"
    :before-close="onClose"
    :lock-scroll="true"
    v-on:open="onOpen"
    v-on:close="onClosed">
    <el-row>
      <el-col :md="24" :xs="24">
        <el-col :md="24">
          <el-form autoComplete="on" label-position="left" :model="form" :rules="rules" ref="editForm">
            <el-form-item prop="fullname" :label="$t('label.name')">
              <el-input type="text" size="small" v-model="form.fullname"></el-input>
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
              <el-button type="primary" :loading="loading" @click.native.prevent="onSubmit"> {{ $t('default.update') }}
              </el-button>
              <el-button @click="onReset">{{ $t('default.reset') }}</el-button>
            </el-form-item>
          </el-form>
        </el-col>
      </el-col>
    </el-row>
  </el-dialog>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';

@Component
export default class EditForm extends Vue {
  @Action('users/get_form_source') formsourceAction;
  @Action('users/get') getAction;
  @Action('users/update') updateAction;
  @State(state => state.users.formSource) formSource;

  @Prop() itemId: number;
  @Prop() editFormState: boolean;
  @Prop() onClose;

  loading: boolean = false;
  form: object = {};

  $refs: {
    editForm: HTMLFormElement
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

  async onOpen() {
    return await this.getAction({ id: this.itemId })
      .then(res => {
        this.form = {
          fullname: res.data.fullname,
          groupid: res.data.groupid,
          status: res.data.status.value,
          verifytype: res.data.verifytype.value
        };
      });
  }

  onClosed() {
    this.$refs.editForm.clearValidate();
  }

  onSubmit() {
    this.$refs.editForm.validate(async valid => {
      if (valid) {
        this.loading = true;

        await this.updateAction({ id: this.itemId, formData: this.form })
          .then(res => {
            this.loading = false;

            this.$message({
              showClose: true,
              message: this.$t('msg.updateSuccess').toString(),
              type: 'success',
              duration: 3 * 1000
            })

            return this.onClose();

          })
          .catch(err => {
            this.loading = false;
          });
      } else {
        return false;
      }
    });
  }

  async onReset() {
    this.$refs.editForm.resetFields();
    await this.getAction({ id: this.itemId })
      .then(res => {
        this.form = {
          fullname: res.data.fullname,
          groupid: res.data.groupid,
          status: res.data.status.value,
          verifytype: res.data.verifytype.value
        };
      });
  }

  created() { return this.initData(); }

  async initData() { return await this.formsourceAction() }
}
</script>
