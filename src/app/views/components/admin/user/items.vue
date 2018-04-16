<lang src="./lang.yml"></lang>

<template>
  <section>
    <el-table :data="users" style="width: 100%" row-key="id"
      @selection-change="onSelectionChange">
      <el-table-column type="selection"></el-table-column>
      <el-table-column :label="$t('label.name')" width="150"
        :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <div class="avatar">
            <img v-if="scope.row.avatar !== ''" :src="scope.row.avatar" width="30" height="30">
            <img v-else src="/img/default_avatar.png" width="30" height="30">
          </div>
          <span class="fullname">{{ scope.row.fullname }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('label.email')" prop="email"
        :show-overflow-tooltip="true"></el-table-column>
      <el-table-column :label="$t('label.phone')" prop="mobilenumber" width="100"
        :show-overflow-tooltip="true"></el-table-column>
      <el-table-column :label="$t('label.group')">
        <template slot-scope="scope">
          <el-tag type="success" size="small">{{ scope.row.groupid }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('label.status')"
        :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <el-tag :type="scope.row.status.style" size="small">{{ scope.row.status.label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('label.activate')"
        :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <el-tag :type="scope.row.verify.style" size="small">{{ scope.row.verify.label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('label.verifyType')"
        :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <el-tag type="gray" size="small">{{ scope.row.verifytype.label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('label.dateCreated')" width="130">
        <template slot-scope="scope">
          <small>{{ scope.row.humandatecreated }}</small>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="130">
        <template slot-scope="scope">
          <el-button-group class="operation">
            <el-button icon="el-icon-edit" size="mini" @click="onShowEditForm(scope.row.id)"></el-button>
            <delete-button :id="scope.row.id" store="users"></delete-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <div style="margin-top: 20px">
      <el-select v-model="bulkName" :placeholder="$t('default.selectAction')" size="small">
        <el-option v-for="item in bulkList" :key="item.value" :label="item.label" :value="item.value" size="small">
        </el-option>
      </el-select>
      <el-button style="margin-left: 10px" type="primary" size="small" @click="onBulkSubmit">{{ $t('default.submit') }}</el-button>
    </div>
    <scroll-top :duration="1000" :timing="'ease'"></scroll-top>
    <edit-form :editFormState="visible" :itemId="itemId" :onClose="onHideEditForm"></edit-form>
  </section>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { Action } from 'vuex-class';
import DeleteButton from "~/components/admin/delete-button.vue";
import EditForm from '~/components/admin/user/edit-form.vue';

@Component({
  components: {
    DeleteButton,
    EditForm
  }
})
export default class AdminUserItems extends Vue {
  @Prop() users: any[];
  @Action('users/bulk') bulkAction;
  @Action('users/get_all') listAction;

  visible: boolean = false;
  itemId: number = 0;
  bulkSelected: object[] = [];
  bulkName: string = '';

  get bulkList() {
    return [
      { value: 'delete', label: this.$t('label.delete') },
      { value: 'enable', label: this.$t('label.enable') },
      { value: 'disable', label: this.$t('label.disable') }
    ];
  }

  onShowEditForm(id) {
    this.visible = !this.visible;
    this.itemId = id;
  }

  onHideEditForm() { this.visible = false; }

  onSelectionChange(item) { this.bulkSelected = item; }

  onBulkSubmit() {
    if (this.bulkSelected.length === 0) {
      this.$message({
        showClose: true,
        message: this.$t('default.msg.noItemSelected').toString(),
        type: 'warning',
        duration: 2 * 1000
      })
    } else if (this.bulkName === '') {
      this.$message({
        showClose: true,
        message: this.$t('default.msg.noActionChosen').toString(),
        type: 'warning',
        duration: 2 * 1000
      });
    } else {
      this.$confirm(this.$t('msg.confirmBulk').toString(), this.$t('default.warning').toString(), {
        confirmButtonText: this.$t('default.msg.confirm').toString(),
        cancelButtonText: this.$t('default.msg.cancel').toString(),
        type: 'warning',
        dangerouslyUseHTMLString: true
      })
      .then(async () => {
        await this.bulkAction({
            formData: {
              itemSelected: this.bulkSelected,
              actionSelected: this.bulkName
            }
          })
          .then(async () => {
            let queryParams = Object.assign({}, this.$route.query);

            await this.listAction({ query: queryParams })
              .then(() => {
                this.$message({
                  showClose: true,
                  message: `${this.bulkName.charAt(0).toUpperCase() + this.bulkName.slice(1)} ${this.$t('default.msg.deleteSuccess')}`,
                  type: 'success',
                  duration: 2 * 1000
                });
              })
          });
      });
    }
  }
}
</script>

<style lang="scss" scoped>
.avatar {
  margin-right: 10px;
  float: left;
  display: inline-block;
  img {
    border-radius: 30px !important;
  }
}
.fullname {
  line-height: 30px;
}
</style>
