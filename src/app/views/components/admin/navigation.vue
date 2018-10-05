<lang>
---

en:
  dashboard: Dashboard
  user: User
  voice: Voice
  script: Script
  gift: Gift
  type: Type & Attributes
vi:
  dashboard: Tổng quan
  user: Thành viên
  voice: Thu âm
  script: Script
  gift: Quà tặng
  type: Loại & thuộc tính
</lang>

<template>
  <el-menu mode="horizontal" unique-opened :default-active="$route.path" router>
    <el-menu-item index="/admin">
      {{ $t('dashboard') }}
    </el-menu-item>
    <el-submenu index="/admin/user">
      <template slot="title">{{ $t('user') }}</template>
      <el-menu-item index="/admin/user">
        <i class="el-icon-fa-navicon"></i>
        {{ $t('default.list') }}
      </el-menu-item>
      <el-menu-item index="/admin/user/add">
        <i class="el-icon-fa-plus"></i>
        {{ $t('default.add') }}
      </el-menu-item>
    </el-submenu>
    <el-submenu index="/admin/voice">
      <template slot="title">{{ $t('voice') }}</template>
      <el-menu-item index="/admin/voice">
        <i class="el-icon-fa-navicon"></i>
        {{ $t('default.list') }}
      </el-menu-item>
      <el-menu-item index="/admin/script">
        <i class="el-icon-fa-text-width"></i>
        {{ $t('script') }}
      </el-menu-item>
    </el-submenu>
    <el-submenu index="/admin/gift">
      <template slot="title">{{ $t('gift') }}</template>
      <el-menu-item index="/admin/gift">
        <i class="el-icon-fa-navicon"></i>
        {{ $t('default.list') }}
      </el-menu-item>
      <el-menu-item index="/admin/gift/type">
        <i class="el-icon-fa-navicon"></i>
        {{ $t('type') }}
      </el-menu-item>
    </el-submenu>
    <el-dropdown class="avatar-container" v-if="currentUser">
      <div class="avatar-wrapper">
        <span class="user-name">
          {{ currentUser.sub.fullname }}
        </span>
        <img v-if="currentUser.sub.avatar !== ''" :src="currentUser.sub.avatar" width="30" height="30" class="user-avatar">
        <img v-else src="/img/default_avatar.png" width="30" height="30" class="user-avatar">
        <i class="el-icon-caret-bottom"></i>
      </div>
      <el-dropdown-menu class="user-dropdown" slot="dropdown">
        <el-dropdown-item>
          <nuxt-link to="/admin/user/changepassword">
            {{ $t('default.changePassword') }}
          </nuxt-link>
        </el-dropdown-item>
        <el-dropdown-item divided>
          <nuxt-link to="/admin/user/logout">
            <span style="display:block;">{{ $t('default.logout') }}</span>
          </nuxt-link>
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
    <div class="locale-container">
      <el-dropdown @command="onChangeLocale">
        <span class="el-dropdown-link">
          <span v-if="$i18n.locale === 'vi'">
            <span class="flag-icon flag-icon-vn"></span> Tiếng Việt
          </span>
          <span v-else>
            <span class="flag-icon flag-icon-us"></span> English
          </span>
          <i class="el-icon-arrow-down el-icon--right"></i>
        </span>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item command="en">
            <span class="flag-icon flag-icon-us"></span> English
          </el-dropdown-item>
          <el-dropdown-item command="vi">
            <span class="flag-icon flag-icon-vn"></span> Tiếng Việt
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </div>
  </el-menu>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator'
import { Getter } from 'vuex-class'
import { setLocale } from '~/helper/locale';

@Component
export default class Navigation extends Vue {
  @Getter('loggedUser') currentUser

  onChangeLocale(locale) {
    setLocale(locale);
    return window.location.reload();
  }
}
</script>

<style lang="scss">
.el-menu--horizontal {
  height: 45px;

  .el-menu-item {
    height: 45px;
    line-height: 45px;
  }

  .el-submenu {
    .el-menu {
      top: 50px;
    }
    .el-submenu__title {
      height: 45px;
      line-height: 45px;
    }
  }

  .avatar-container {
    height: 43px;
    display: inline-block;
    float: right;
    margin-right: 40px;
    .avatar-wrapper {
      cursor: pointer;
      margin-top: 7px;
      position: relative;
      float: left;
      clear: both;

      .user-name {
        float: left;
        padding-top: 7px;
        padding-right: 10px;
      }
      .user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 15px;
      }
      .el-icon-caret-bottom {
        position: absolute;
        right: -20px;
        top: 10px;
        font-size: 12px;
      }
    }
  }

  .locale-container {
    cursor: pointer;
    float: right;
    margin-left: 15px;
    margin-right: 15px;
    margin-top: 14px;
    font-size: 14px;
    border-right: 1px solid #ecf0f1;
    padding-right: 15px;
  }
}
</style>
