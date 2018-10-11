<template>
  <div class="select-editable">
    <i v-show="iconShow" class="el-icon-edit"></i>
    <el-row v-show="isEdit == false">
      <div @click="enableEditMode" @mouseover="showIcon" @mouseleave="hideIcon">
        <el-tag :type="data.style" size="small">{{ data.label }}</el-tag>
      </div>
    </el-row>
    <div class="show-dropdown-edit">
      <el-row v-show="isEdit == true">
        <el-select v-model="form.value" size="mini">
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-row>
      <el-row type="flex" justify="end" style="margin-top: 5px; " v-show="isEdit == true">
        <el-button-group>
          <el-button size="mini" type="primary" @click="handleEdit" icon="el-icon-check"></el-button>
          <el-button size="mini" @click="isEdit = false" icon="el-icon-close"></el-button>
        </el-button-group>
      </el-row>
    </div>
  </div>

</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";

@Component
export default class SelectEditable extends Vue {
  @Prop() id;
  @Prop() data;
  @Prop() store;
  @Prop() field;
  @Prop() options;

  isEdit: boolean = false;
  loading: boolean = false;
  form: any = {
    value: ''
  };
  iconShow: boolean = false;

  showIcon() {
    this.iconShow = true;
  }
  hideIcon() {
    this.iconShow = false;
  }
  enableEditMode() {
    this.isEdit = true;
    this.form.value = this.$props.data.value
  }
  disableEditMode() {
    this.isEdit = false;
    this.form.value = this.$props.data.value
  }
  async handleEdit() {
    this.loading = true;
    await this.$store.dispatch(`${this.store}/update_filed`, {
      id: this.id,
      field: this.field,
      value: this.form.value
    });
    this.loading = false,
    this.isEdit = false;

  }

}
</script>

<style lang="scss" scoped>
  .el-icon-edit {
    display: inline-block;
    float: right;
    margin-right: -12px;
    padding-top: 5px;
    color: #ccc;
  }
  .el-button--mini {
    padding: 2px 5px;
  }
  .show-dropdown-edit {
    position: relative;
    top: 14px;
  }
</style>


