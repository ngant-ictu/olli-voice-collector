<lang src="./lang.yml"></lang>

<template>
  <el-dialog
    :visible.sync="editFormState"
    :before-close="onClose"
    :lock-scroll="true"
    v-on:open="onOpen"
    v-on:close="onClosed"
    width="40%">
    <el-row>
      <el-col :md="24" :xs="24">
        <el-col :md="24">
          <el-form autoComplete="on" label-position="left" :model="form" :rules="rules" ref="editForm">
            <el-form-item prop="name" :label="$t('label.name')">
              <el-input type="text" size="small" v-model="form.name" clearable></el-input>
            </el-form-item>
            <el-form-item prop="requiredpoint" :label="$t('label.requiredpoint')">
              <el-input type="number" size="small" v-model="form.requiredpoint"></el-input>
            </el-form-item>
            <el-form-item prop="isused" :label="$t('label.isused')">
              <el-select size="small" v-model="form.isused" :placeholder="$t('label.selectUsedStatus')" style="width: 100%" :loading="loading">
                <el-option v-for="item in formSource.isusedList" :key="item.label" :label="item.label" :value="item.value">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item :label="$t('label.info')">
              <el-row v-if="form.stocks.length > 0" v-for="(item, index) in form.stocks" :key="index">
                <el-col :md="24">
                  <el-input type="text" size="small" v-model="form.stocks[index].value" clearable>
                    <template slot="prepend">{{ item.label }}</template>
                    <template slot="append" v-if="item.unit !== ''"><code>{{ item.unit }}</code></template>
                  </el-input>
                </el-col>
              </el-row>
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
  @Action('gifts/get_form_source') formsourceAction;
  @Action('gifts/get') getAction;
  @Action('gifts/update') updateAction;
  @State(state => state.gifts.formSource) formSource;

  @Prop() itemId: number;
  @Prop() editFormState: boolean;
  @Prop() onClose;

  loading: boolean = false;
  form: any = {};

  $refs: {
    editForm: HTMLFormElement
  }

  get rules() {
    return {
      name: [
        {
          required: true,
          message: this.$t('msg.nameIsRequired'),
          trigger: 'blur'
        }
      ],
      isused: [
        {
          required: true,
          message: this.$t('msg.isusedIsRequired'),
          trigger: 'change'
        }
      ]
    };
  }

  async onOpen() {
    return await this.getAction({ id: this.itemId })
      .then(res => {
        this.form = {
          name: res.data.name,
          requiredpoint: res.data.requiredpoint,
          isused: res.data.isused.value,
          stocks: []
        };

        if (res.data.stocks.data.length > 0) {
          res.data.stocks.data.map(item => {
            this.form.stocks.push({
              key: item.id,
              label: item.attribute.data.name,
              value: item.value,
              unit: item.attribute.data.unit
            });
          })
        }
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
              message: this.$t('msg.updateGiftSuccess').toString(),
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
          name: res.data.name,
          requiredpoint: res.data.requiredpoint,
          isused: res.data.isused.value,
          stocks: []
        };

        if (res.data.stocks.data.length > 0) {
          res.data.stocks.data.map(item => {
            this.form.stocks.push({
              key: item.id,
              label: item.attribute.data.name,
              value: item.value,
              unit: item.attribute.data.unit
            });
          })
        }
      });
  }

  created() { return this.initData(); }

  async initData() { return await this.formsourceAction() }
}
</script>

<style>
.el-input-group__prepend {
  width: 100px;
}
</style>
