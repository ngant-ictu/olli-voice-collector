<template>
  <div>
    <el-popover ref="myPopover" placement="left" v-model="visible">
      <p>{{ $t('default.msg.deleteConfirm') }}</p>
      <div style="text-align: right; margin: 0">
        <el-button size="mini" type="text" @click="onCancel()">
          {{ $t('default.msg.cancel') }}
        </el-button>
        <el-button size="mini" type="text" @click="onConfirm()" class="del-button">
          {{ $t('default.msg.confirm') }}
        </el-button>
      </div>
    </el-popover>
    <el-tooltip class="item" effect="dark" :content="$t('default.delete')" placement="top" :enterable="false">
      <el-button v-popover:myPopover icon="el-icon-fa-trash-o" size="mini" type="danger"></el-button>
    </el-tooltip>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";

@Component
export default class DeleteButton extends Vue {
  @Prop() id: number;
  @Prop() store: string;

  visible: boolean = false;

  onCancel() { this.visible = false; }

  async onConfirm() {
    this.visible = false;

    const res = await this.$store.dispatch(`${this.store}/delete`, {
      id: this.id
    });

    this.$message({
      showClose: true,
      message: `#${this.id} ${this.$t('msg.deleteSuccess').toString()}`,
      type: "success",
      duration: 3 * 1000
    });
  }
}
</script>

<style scoped>
.del-button {
  color: #f56c6c;
}
</style>
