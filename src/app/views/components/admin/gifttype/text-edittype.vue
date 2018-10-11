<template>
  <div class="edit-row">
    <i v-show="iconShow" class="el-icon-edit"></i>
    <el-row v-show="!isEdit">
      <div @click="enableEditMode" @mouseover="showIcon" @mouseleave="hideIcon" class="edit-area">{{ calc }}</div>
    </el-row>
    <el-row v-show="isEdit" class="editable-input">
      <div class="wrap-input-popover">
        <el-input
          ref="myinput"
          v-model="form.value"
          size="small"
          @keyup.enter.native="handleEdit">
          <i class="el-icon-fa-spinner el-icon-fa-spin el-input__icon" slot="suffix" v-if="loading"></i>
          <i class="el-icon-close el-input__icon" slot="suffix" @click="disableEditMode"  v-if="!loading"></i>
          </el-input>
        <el-popover
          placement="top"
          width="160"
          v-model="visible" v-if="!loading" class="show-popover">
          <p>Are you sure to delete this attribute?</p>
          <div style="text-align: right; margin: 0">
            <el-button size="mini" type="text" @click="visible = false">No</el-button>
            <el-button type="text" size="mini" @click="handleDelete" style="color: #ff0033;">Yes</el-button>
          </div>
          <i class="el-icon-delete el-input__icon" slot="reference"></i>
        </el-popover>
      </div>
    </el-row>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";

@Component
export default class TextEdittype extends Vue {
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

   await this.$store.dispatch(`gifttypes/update_attr_field`,{
      id: this.id,
      field: this.field,
      value: this.form.value
    });

    await this.$store.dispatch('gifttypes/get_all', { query: this.$route.query });

    this.loading = false,
    this.isEdit = false;
  }

  async handleDelete() {
    this.loading = true;
    await this.$store.dispatch(`gifttypes/delete_attr`,{
      id: this.id
    });

    await this.$store.dispatch('gifttypes/get_all', { query: this.$route.query });

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
.el-row.editable-input {
  position: absolute;
  top: 6px;
  left: 10px;
  right: 0;
  z-index: 10;
  display: inline-block;
}

.el-icon-close:hover,
.el-icon-delete:hover {
  color: #ff0033;
}


</style>
