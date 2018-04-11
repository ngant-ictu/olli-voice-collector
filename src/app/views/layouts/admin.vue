<template>
  <div>
    <el-row class="header">
      <el-col :span="24">
        <navigation></navigation>
      </el-col>
    </el-row>
    <el-row class="container">
      <el-col :span="24">
        <nuxt></nuxt>
      </el-col>
    </el-row>
    <el-row class="footer"></el-row>
  </div>
</template>

<script lang="ts">
import { Vue, Component } from 'nuxt-property-decorator'
import Navigation from '~/components/admin/navigation.vue'

@Component({
  components: {
    Navigation
  }
})
export default class AdminLayout extends Vue {
  head() {
    return {
      link: [
        { rel: 'stylesheet', href: 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.1.0/css/flag-icon.min.css' }
      ]
    };
  }
}
</script>

<style lang="scss">
@import '../assets/scss/_reset';

html,
body {
  margin: 0;
  padding: 0;
  height: 100%;
  font-family: Lato, Helvetica, sans-serif;
  color: #333447;
  line-height: 1.6em;
  font-weight: 500;
  font-size: 0.95rem;
  &:focus {
    outline: none;
  }
  a {
    text-decoration: none !important;
    color: cornflowerblue;
  }
  .el-tag {
    border-radius: 0 !important;
    &.position-label {
      display: flex;
      justify-content: center;
    }
  }

  .el-button,
  .el-input__inner,
  .el-input-group__append,
  .el-input-group__prepend {
    border-radius: 2px !important;
  }
  .el-input-group--append .el-input__inner,
  .el-input-group__prepend {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
  }
  .el-input-group--prepend .el-input__inner,
  .el-input-group__append {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
  }

  .filter-icon {
    position: absolute;
    left: 0;
    width: 50px;
    height: 50px;
    line-height: 50px;
    text-align: center;
    background-color: #eff2f7;
    padding-left: 12px;
  }

  .filter-container {
    padding: 17px 20px 0 20px;
    position: absolute;
    top: 0;
    bottom: 0;
    width: 193px;
    font-size: 13px;
    margin-top: 50px;
    -webkit-overflow-scrolling: touch;
    z-index: 10;
    display: block;
    color: #333;

    .el-form {
      .el-select {
        width: 100% !important;
      }
      .el-form-item__label {
        line-height: 0px;
      }
    }
  }

  .top-right-toolbar {
    display: inline-block;
    position: absolute;
    right: 20px;
    margin-top: -50px;
    height: 50px;
    line-height: 50px;
    z-index: 10;
    .el-button {
      font-size: 14px;
    }
  }

  .panel-body {
    padding: 20px;
    margin-left: 235px;
    border-left: 1px solid #eff2f7;
    min-height: 100vh;
    max-height: 100%;

    .no-content {
      display: block;
      position: fixed;
      top: 50%;
      left: 50%;
      color: #8f8f8f;
      text-align: center;
      i {
        &:before {
          font-size: 80px;
        }
      }
    }

    .loading {
      display: block;
      position: fixed;
      top: 50%;
      left: 50%;

      i {
        font-size: 20px;
      }
    }

    .vm-back-top-inner {
      background-color: rgba(0, 0, 0, 0.17) !important;
    }
  }

  .el-table {
  .el-button--mini {
    font-size: 11px !important;
  }
  .el-button-group.operation {
    .el-button {
      border: none !important;
    }
  }
  td {
    padding: 8px 0;

    &.el-table__expanded-cell {
      background-color: #f6f6fb;
    }
    .operation {
      float: right;
      a {
        float: left;
      }
      div {
        float: left;
      }
    }
  }
  th {
    font-size: 12px !important;
  }
  .el-button-group {
    &.operation {
        display: block;
      }
    }
  }
}

</style>
