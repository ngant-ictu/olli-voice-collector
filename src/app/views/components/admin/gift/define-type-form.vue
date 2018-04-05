<lang src="./lang.yml"></lang>

<template>
  <el-dialog
    :visible.sync="defineFormState"
    :before-close="onClose"
    :close-on-click-modal="false"
    v-on:close="onClosed"
    >
    <el-row>
      <el-col :md="24" :xs="24">
        <el-col :md="24">
          <el-form autoComplete="on" label-position="left" :model="form" :rules="rules" ref="defineForm">
            <el-form-item prop="name" :label="$t('label.name')">
              <el-input type="text" size="small" v-model="form.name" autofocus></el-input>
            </el-form-item>
            <el-row>
              <el-col :md="24">
                <p>
                  {{ $t('label.attributes') }}
                </p>
              </el-col>
            </el-row>
            <el-row v-for="(attr, index) in form.attrs" :key="index" :gutter="10">
              <el-col :md="12">
                <el-form-item
                  :prop="`attrs.${index}.name`"
                  :rules="{ required: true, message: $t('msg.nameIsRequired'), trigger: 'blur' }">
                  <el-input v-model="attr.name" size="small" :placeholder="$t('label.name')"></el-input>
                </el-form-item>
              </el-col>
              <el-col :md="4">
                <el-form-item :prop="`attrs.${index}.unit`">
                  <el-input v-model="attr.unit" size="small" :placeholder="$t('label.unit')"></el-input>
                </el-form-item>
              </el-col>
              <el-col :md="4">
                <el-form-item :prop="`attrs.${index}.order`">
                  <el-input v-model="attr.order" size="small" :placeholder="$t('label.order')"></el-input>
                </el-form-item>
              </el-col>
              <el-col :md="4">
                <el-form-item>
                  <el-button
                    @click="onRemoveAttribute(attr)"
                    icon="el-icon-fa-trash"
                    type="danger" size="mini"
                    :plain="true">
                  </el-button>
                </el-form-item>
              </el-col>
            </el-row>
            <el-form-item>
              <el-button @click="onAddAttribute" icon="el-icon-fa-plus" size="mini" type="success"> {{ $t('label.addAttribute') }}</el-button>
            </el-form-item>
            <el-form-item style="margin-top: 30px">
              <el-button type="primary" :loading="loading" @click.native.prevent="onSubmit"> {{ $t('default.add') }}
              </el-button>
              <el-button @click="onClose">{{ $t('default.cancel') }}</el-button>
            </el-form-item>
          </el-form>
        </el-col>
      </el-col>
    </el-row>
  </el-dialog>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';
import { Action } from 'vuex-class';

@Component
export default class DefineTypeForm extends Vue {
  @Action('gifttypes/add') addAction;
  @Prop() defineFormState: boolean;
  @Prop() onClose;

  loading: boolean = false;
  form: any = {
    name: '',
    attrs: [
      { key: 1, name: '', unit: '', order: 1 }
    ]
  };

  $refs: {
    defineForm: HTMLFormElement
  }

  get rules() {
    return {
      name: [
        {
          required: true,
          message: this.$t('msg.nameIsRequired'),
          trigger: 'blur'
        }
      ]
    };
  }

  onAddAttribute() {
    this.form.attrs.push({
      key: this.form.attrs.length + 1,
      name: '',
      unit: '',
      order: this.form.attrs.length + 1
    });
  }

  onRemoveAttribute(item) {
    const index = this.form.attrs.indexOf(item);
    if (index !== -1) {
      this.form.attrs.splice(index, 1);
    }
  }

  onClosed() {
    this.$refs.defineForm.resetFields();
  }

  onSubmit() {
    this.$refs.defineForm.validate(async valid => {
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

            // return this.onClose();

          })
          .catch(err => {
            this.loading = false;
          });
      } else {
        return false;
      }
    });
  }
  //
  // async onReset() {
  //   this.$refs.editForm.resetFields();
  //   await this.getAction({ id: this.itemId })
  //     .then(res => {
  //       this.form = {
  //         fullname: res.data.fullname,
  //         groupid: res.data.groupid,
  //         status: res.data.status.value,
  //         verifytype: res.data.verifytype.value
  //       };
  //     });
  // }
  //
  // created() { return this.initData(); }
  //
  // async initData() { return await this.formsourceAction() }
}
</script>
