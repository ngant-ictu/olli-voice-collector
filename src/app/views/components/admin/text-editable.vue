<template>
  <div class="edit-row">
    <i v-show="iconShow" class="el-icon-edit"></i>
    <el-row v-show="!isEdit">
      <div @click="enableEditMode" @mouseover="showIcon" @mouseleave="hideIcon" class="edit-area">{{ calc }}</div>
    </el-row>
    <el-row v-show="isEdit" class="editable-input">
        <el-input
          ref="myinput"
          v-model="form.value"
          size="small"
          @keyup.enter.native="handleEdit">
          <i class="el-icon-close el-input__icon" slot="suffix" @click="disableEditMode" v-if="!loading"></i>
          <i class="el-icon-fa-spinner el-icon-fa-spin el-input__icon" slot="suffix" v-else></i>
          </el-input>
    </el-row>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";

@Component
export default class TextEditable extends Vue {
  @Prop() id;
  @Prop() data;
  @Prop() store;
  @Prop() field;

  isEdit: boolean = false;
  visible: boolean = false;
  loading: boolean = false;
  form: any = {
    value: ""
  };
  iconShow: boolean = false;
  $refs: {
    myinput: HTMLFormElement;
  };

  updateError: ({ message: string }) => void;

  get calc() {
    if (this.form.value !== "" && this.$props.data !== this.form.value) {
      return this.form.value;
    } else {
      return this.$props.data;
    }
  }
  showIcon() {
    this.iconShow = true;
  }

  hideIcon() {
    this.iconShow = false;
  }
  enableEditMode() {
    this.isEdit = true;
    this.form.value = this.$props.data;

    //Focus on selected input
    const self = this;
    setTimeout(function() {
      self.$refs.myinput.$el.getElementsByTagName("input")[0].focus();
    }, 1);
  }
  disableEditMode() {
    this.isEdit = false;
    this.visible = false;
    this.form.value = this.$props.data;
  }

  async handleEdit() {
    this.loading = true;

   await this.$store.dispatch(`${this.store}/update_field`,{
     id: this.id,
     field: this.field,
     value: this.form.value
   });

   this.loading = false,
   this.isEdit = false;
  }
}
</script>

<style scoped lang="scss">
.el-icon-edit {
  display: inline-block;
  float: right;
  margin-right: 10px;
  padding-top: 5px;
  color: #95a5a6;
}
.el-col.editable-input {
  position: absolute;
  top: 6px;
  left: 10px;
  right: 0;
  z-index: 10;
  display: inline-block;
}

</style>
