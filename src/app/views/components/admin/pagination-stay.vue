<template>
  <div class="pagination" v-if="totalItems > 0">
    <el-button type="text" icon="el-icon-arrow-left" :disabled="previousPage === 0"
      @click="handlePageChange(previousPage)">
      {{ $t('default.previousPage') }}
    </el-button>
    <span class="text">{{ $t('default.page') }} {{ currentPage }} / {{ totalPage }}</span>
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
  @Prop() handlePageChange: void;

  get previousPage() { return this.currentPage -1; }
  get nextPage() { return this.currentPage + 1; }
  get totalPage() { return Math.ceil(this.totalItems / this.recordPerPage);}
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
  }
}
</style>
