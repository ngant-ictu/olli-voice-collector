<template>
  <div class="pagination" v-if="totalItems > 0">
    <el-button type="text" icon="el-icon-arrow-left" :disabled="previousPage === 0"
      @click="handlePageChange(previousPage)">
      {{ $t('default.previousPage') }}
    </el-button>
    <el-input class="goto" size="mini" type="text" v-model="page" v-show="showGoto" @keyup.enter.native="goToPage" autofocus>
      <i
        class="el-icon-close el-input__icon"
        slot="suffix"
        @click="onToggle">
      </i>
    </el-input>
    <el-tooltip class="item" effect="dark" content="Click to type page number to go" placement="top">
      <span class="text" v-show="!showGoto" @click="onToggle">{{ $t('default.page') }} {{ currentPage }} / {{ totalPage }}</span>
    </el-tooltip>
    <el-button type="text" :disabled="nextPage > totalPage"
      @click="handlePageChange(nextPage)">
      {{ $t('default.nextPage') }} &nbsp;
      <i class="el-icon-arrow-right"></i>
    </el-button>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';
const querystring = require('querystring');

@Component
export default class PaginationStay extends Vue {
  @Prop() totalItems: number
  @Prop() currentPage: number;
  @Prop() recordPerPage: number;
  @Prop() handlePageChange;

  page: number = 1;
  showGoto: boolean = false;

  get previousPage() { return this.currentPage -1; }
  get nextPage() { return this.currentPage + 1; }
  get totalPage() { return Math.ceil(this.totalItems / this.recordPerPage);}

  onToggle() {
    return this.showGoto = !this.showGoto;
  }

  goToPage() {
    return this.handlePageChange(this.page);
  }

}
</script>

<style lang="scss" scoped>
.pagination {
  display: block;
  background-color: #fff;
  padding-left: 10px;
  padding-right: 10px;

  .text {
    margin-left: 10px;
    margin-right: 10px;
    color: #9a9898;
    font-size: 12px;
    cursor: pointer;
  }

  .goto {
    display: inline-block;
    width: 70px;
    margin-left: 10px;
    margin-right: 10px;
  }

  .el-icon-close {
    cursor: pointer;
  }
}
</style>
