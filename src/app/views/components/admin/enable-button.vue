<template>
  <el-switch
    v-model="isEnable"
    active-color="#2ecc71"
    inactive-color="#bdc3c7"
    @change="onChange()">
  </el-switch>
</template>

<script lang="ts">
import { Vue, Component, Watch, Prop } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';

@Component({
  // notifications: {
  //   importSuccess: {
  //     icon: 'fas fa-exclamation-triangle',
  //     position: 'bottomCenter',
  //     title: 'Upload success',
  //     toastOnce: true,
  //     type: 'success'
  //   }
  // }
})
export default class EnableButton extends Vue {
  @Prop() id: number;
  @Prop() status: number;
  @Prop() store: string;

  isEnable: boolean = false;

  async onChange() {
    return await this.$store.dispatch(`${this.store}/change_status`, {
      id: this.id,
      value: this.isEnable
    });
  }

  created() {
    switch (this.status) {
      case 1:
        this.isEnable = true;
        break;
      case 3:
        this.isEnable = false;
        break;
    }
  }
}

</script>
