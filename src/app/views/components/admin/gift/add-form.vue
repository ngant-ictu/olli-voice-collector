<lang src="./lang.yml"></lang>

<template>
  <el-dialog
    :visible.sync="addFormState"
    :before-close="onClose"
    :close-on-click-modal="false"
    v-on:close="onClosed"
    >
    <el-row>
      <el-col :md="24" :xs="24">
        <el-col :md="24">
          <el-form autoComplete="on" label-position="left" :model="form" :rules="rules" ref="addForm">
            <el-form-item prop="name" :label="$t('label.name')">
              <el-input type="text" size="small" v-model="form.name" autofocus></el-input>
            </el-form-item>
            <el-form-item prop="type" :label="$t('label.type')">
              <el-select size="small" v-model="form.type" :placeholder="$t('label.selectType')" :loading="loading" @change="onChangeType">
                <el-option v-for="item in formSource.typeList" :key="item.id" :label="item.name" :value="item.id">
                </el-option>
              </el-select>
            </el-form-item>
            <el-row v-for="(attr, index) in attrs" :key="index" :gutter="10" v-show="showAttrs">
              <el-col :md="12">
                <el-form-item>
                  <el-input size="small" :placeholder="attr.name" v-on:input="onInputName(attr['id'], $event)"></el-input>
                </el-form-item>
              </el-col>
              <el-col :md="4">
                <el-form-item>
                  <el-input size="small" :placeholder="attr.unit" v-on:input="onInputUnit(attr['id'], $event)"></el-input>
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
import { Action, State } from 'vuex-class';

@Component
export default class AddForm extends Vue {
  @Action('gifttypes/add') addAction;
  @Action('gifttypes/get_attrs') getattrsAction;
  @Action('gifts/get_form_source') formsourceAction;
  @State(state => state.gifts.formSource) formSource;
  @State(state => state.gifttypes.attrs) attrs;
  // @Prop() addFormState: boolean;
  addFormState = true; // for dev
  @Prop() onClose;

  loading: boolean = false;
  showAttrs = false;
  form: any = {
    name: '',
    type: null,
    attrs: null
  };

  $refs: {
    addForm: HTMLFormElement
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

  onInputName(attrId, value) {
    this.form.attrs[attrId].name = value;
  }

  onInputUnit(attrId, value) {
    this.form.attrs[attrId].unit = value;
  }

  onAddAttribute() {
    // this.form.attrs.push({
    //   key: this.form.attrs.length + 1,
    //   name: '',
    //   unit: '',
    //   order: this.form.attrs.length + 1
    // });
  }

  onRemoveAttribute(item) {
    // const index = this.form.attrs.indexOf(item);
    // if (index !== -1) {
    //   this.form.attrs.splice(index, 1);
    // }
  }

  onClosed() {
    this.$refs.addForm.resetFields();
  }

  onSubmit() {
    // this.$refs.defineForm.validate(async valid => {
    //   if (valid) {
    //     this.loading = true;
    //     await this.addAction({ formData: this.form })
    //       .then(res => {
    //         this.loading = false;
    //
    //         this.$message({
    //           showClose: true,
    //           message: this.$t('msg.addSuccess').toString(),
    //           type: 'success',
    //           duration: 3 * 1000
    //         })
    //
    //         // return this.onClose();
    //
    //       })
    //       .catch(err => {
    //         this.loading = false;
    //       });
    //   } else {
    //     return false;
    //   }
    // });
  }

  async onChangeType(typeId) {
    this.loading = true;

    await this.getattrsAction({ id: typeId })
      .then(async res => {
        this.loading = false;
        this.showAttrs = true;
        this.form.attrs = [];

        this.attrs.map(attr => {
          this.form.attrs[attr.id] = {
            key: attr.id,
            name: '',
            unit: ''
          };
        });
      })
      .catch(err => {
        this.loading = false;
      })
  }

  created() { return this.initData(); }

  async initData() { return await this.formsourceAction(); }
}
</script>
