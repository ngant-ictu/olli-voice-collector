<lang src="./lang.yml"></lang>

<template>
  <el-upload
    action=""
    multiple
    :auto-upload="false"
    with-credentials
    :file-list="myFiles"
    :on-change="onChange"
    :on-remove="onRemove"
    >
    <el-button slot="trigger" size="mini" type="primary">
      {{ $t('label.selectFiles') }}
    </el-button>
    <el-button v-show="myFiles.length > 0" :loading="loading" style="margin-left: 10px;" size="mini" icon="el-icon-fa-upload" type="success" @click="onUpload">
      {{ $t('label.import') }}
    </el-button>
    <div class="el-upload__tip" slot="tip">
      {{ $t('msg.fileTypeAllowed') }}
    </div>
  </el-upload>
</template>

<script lang="ts">
import { Vue, Component } from 'nuxt-property-decorator';
import { Action } from 'vuex-class';

@Component
export default class ImportButton extends Vue {
  @Action('scripts/import') importAction;
  @Action('scripts/get_all') listAction;

  loading: boolean = false;
  myFiles: any[] = [];

  onChange(file, filelist) {
    this.myFiles = filelist;
  }

  onRemove(file, filelist) {
    this.myFiles = filelist;
  }

  async onUpload() {
    this.loading = true;

    await this.importAction({ formData: this.myFiles })
      .then(async res => {
        this.loading = false;

        this.$message({
          showClose: true,
          message: `${res.data.scriptsImported} ${this.$t('msg.importSuccess').toString()}`,
          type: 'success',
          duration: 3 * 1000
        });

        await this.listAction({ query: {} });
      })
      .catch(err => {
        this.loading = false;
      });
  }
}
</script>
