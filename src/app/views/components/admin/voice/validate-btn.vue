<template>
  <div>
    <el-button icon="el-icon-fa-check" class="circle" type="success" :loading="loading"
      @click="onValidate(id, 1)"
      :disabled="currentStatus === '1' ? true : false">
    </el-button>
    <el-button icon="el-icon-fa-times" class="circle" type="danger" :loading="loading"
      @click="onValidate(id, 3)"
      :disabled="currentStatus === '3' ? true : false">
    </el-button>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { Action, State } from "vuex-class";

@Component
export default class ValidateBtn extends Vue {
  @Action("voices/validate") validateAction;
  @Prop() id: number;
  @Prop() currentStatus: string;

  loading: boolean = false;

  async onValidate(vid, statusValue) {
    this.loading = true;

    // validate
    const newUservoices = await this.validateAction({
      id: vid,
      formData: { status: statusValue }
    });

    this.loading = false;

    // reload uservoices state
    await this.$emit('loadData');
  }
}
</script>
