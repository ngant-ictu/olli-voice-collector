<lang src="./lang.yml"></lang>

<template>
  <el-dialog
    :visible.sync="validateFormState"
    :before-close="onClose"
    :lock-scroll="true"
    v-on:open="onOpen"
    >
    <el-row>
      <el-col :md="24" :xs="24" v-for="(voice, index) in uservoices" :key="index">
        <howler-player :sources="[voice.filepath]" :voicescript="voice.voicescript.data"></howler-player>
      </el-col>
    </el-row>
  </el-dialog>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import HowlerPlayer from '~/components/admin/howler-player.vue';

@Component({
  components: {
    HowlerPlayer
  }
})
export default class ValidateForm extends Vue {
  @Action('uservoices/get_form_source') formsourceAction;
  @Action('uservoices/get_all') getAction;
  // @Action('users/update') updateAction;
  @State(state => state.uservoices.formSource) formSource;
  @State(state => state.uservoices.data) uservoices;

  @Prop() userId: number;
  @Prop() validateFormState: boolean;
  @Prop() onClose;

  loading: boolean = false;

  async onOpen() {
    return await this.getAction({ query: {
      uid: this.userId
    } });
  }

  created() { return this.initData(); }

  async initData() { return await this.formsourceAction() }
}
</script>
