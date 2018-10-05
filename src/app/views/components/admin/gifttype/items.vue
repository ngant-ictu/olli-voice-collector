
<template>
  <section>
    <el-table :data="gifttypes"  style="width: 100%" row-key="id"
      @selection-change="onSelectionChange">
      <el-table-column type="selection"></el-table-column>
      <el-table-column :label="$t('label.name')">
        <template slot-scope="scope">
          <text-editable
            :data="scope.row.name"
            :id="scope.row.id"
            store="dhammas"
            field="name"
          > </text-editable>
        </template> 
      </el-table-column>
      <el-table-column class-name="td-operation" width="200">
        <template slot-scope="scope">
          <el-button-group class="operation">
            <!-- <el-button icon="el-icon-fa-pencil" size="mini" @click="onShowEditForm(scope.row.id)"></el-button> -->
            <delete-button :id="scope.row.id" store="gifttypes"></delete-button>
          </el-button-group>
        </template>
      </el-table-column>
      <!-- <text-edit-table></text-edit-table> -->
    </el-table>
    <div style="margin-top: 20px">
      <el-select v-model="bulkName" :placeholder="$t('default.selectAction')" size="small">
        <el-option v-for="item in bulkList" :key="item.value" :label="item.label" :value="item.value" size="small">
        </el-option>
      </el-select>
      <el-button style="margin-left: 10px" type="primary" size="small" @click="onBulkSubmit">{{ $t('default.submit') }}</el-button>
    </div>
    <scroll-top :duration="1000" :timing="'ease'"></scroll-top> 
  </section>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { Action } from "vuex-class";
import DeleteButton from "~/components/admin/delete-button.vue";
// import EditForm from "~/components/admin/gift/edit-form.vue";
import TextEditable from "~/components/admin/TextEditable.vue";

@Component({
  components: {
    DeleteButton,
    // EditForm,
    TextEditable
  }
})
export default class AdminGiftTypeItems extends Vue {
  @Prop() gifttypes: any[];
  @Action("gifttypes/bulk") bulkAction;
  @Action("gifttypes/get_all") listAction;

  visible: boolean = false;
  visibleClone: boolean = false;
  itemId: number = 0;
  bulkSelected: object[] = [];
  bulkName: string = "";

  get bulkList() {
    return [{ value: "delete", label: this.$t("label.delete") }];
  }

  onSelectionChange(item) {
    this.bulkSelected = item;
  }

  onBulkSubmit() {
    if (this.bulkSelected.length === 0) {
      this.$message({
        showClose: true,
        message: this.$t("default.msg.noItemSelected").toString(),
        type: "warning",
        duration: 2 * 1000
      });
    } else if (this.bulkName === "") {
      this.$message({
        showClose: true,
        message: this.$t("default.msg.noActionChosen").toString(),
        type: "warning",
        duration: 2 * 1000
      });
    } else {
      this.$confirm(
        this.$t("msg.confirmBulk").toString(),
        this.$t("default.warning").toString(),
        {
          confirmButtonText: this.$t("default.msg.confirm").toString(),
          cancelButtonText: this.$t("default.msg.cancel").toString(),
          type: "warning",
          dangerouslyUseHTMLString: true
        }
      ).then(async () => {
        await this.bulkAction({
          formData: {
            itemSelected: this.bulkSelected,
            actionSelected: this.bulkName
          }
        }).then(async () => {
          let queryParams = Object.assign({}, this.$route.query);

          await this.listAction({ query: queryParams }).then(() => {
            this.$message({
              showClose: true,
              message: `${this.bulkName.charAt(0).toUpperCase() +
                this.bulkName.slice(1)} ${this.$t(
                "default.msg.deleteSuccess"
              )}`,
              type: "success",
              duration: 2 * 1000
            });
          });
        });
      });
    }
  }
}
</script>

<style lang="scss">

</style>
