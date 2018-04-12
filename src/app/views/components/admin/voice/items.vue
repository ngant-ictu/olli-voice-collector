<lang src="./lang.yml"></lang>

<template>
  <section>
    <el-table :data="voices" style="width: 100%">
      <el-table-column :label="$t('label.uid')">
        <template slot-scope="scope">
          Total: {{ scope.row.items.data.length }}
          Pending: {{ totalPending(scope.row) }}
          Approved: {{ totalApproved(scope.row) }}
          Rejected: {{ totalRejected(scope.row) }}
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="130">
        <template slot-scope="scope">
          button
          <!-- <el-button-group class="operation">
            <el-button icon="el-icon-edit" size="mini" @click="onShowEditForm(scope.row.id)"></el-button>
            <delete-button :id="scope.row.id" store="voices"></delete-button>
          </el-button-group> -->
        </template>
      </el-table-column>
    </el-table>
    <scroll-top :duration="1000" :timing="'ease'"></scroll-top>
  </section>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { Action } from 'vuex-class';

@Component({
  components: {}
})
export default class AdminVoiceItems extends Vue {
  @Prop() voices: any[];
  @Action('voices/get_all') listAction;

  totalPending(row) {
    return row.items.data.filter(voice => voice.status.value === '5').length
  }

  totalApproved(row) {
    return row.items.data.filter(voice => voice.status.value === '1').length
  }

  totalRejected(row) {
    return row.items.data.filter(voice => voice.status.value === '3').length
  }


}
</script>

<style lang="scss">

</style>
