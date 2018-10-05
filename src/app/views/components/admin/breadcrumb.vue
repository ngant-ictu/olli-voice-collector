<template>
  <el-breadcrumb separator="/">
    <el-breadcrumb-item v-for="item, index in data" :to="item.link" :key="index">
      {{ item.name }}
      <span v-if="index === data.length - 1 && totalItems > 0">
        &lpar;{{ totalItems }}&rpar;
      </span>
    </el-breadcrumb-item>
  </el-breadcrumb>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';
import { State } from 'vuex-class';

@Component
export default class Breadcrumb extends Vue {
  @Prop() data: any[];
  @Prop() totalItems: number;
}
</script>

<style lang="scss">
.el-breadcrumb {
  font-size: 16px;
  height: 50px;
  line-height: 50px;
  background-color: #ecf0f1;
  padding-left: 20px;
  padding-right: 20px;
  margin-left: 50px;
}
</style>
