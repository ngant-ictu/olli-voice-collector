<template>
  <p>Logging off...</p>
</template>

<script lang="ts">
import { Vue, Component } from 'nuxt-property-decorator'
import { unsetToken } from '~/helper/auth'

@Component
export default class Logout extends Vue {
  mounted() {
    unsetToken()

    return this.$router.push('/admin/login');
  }
}
</script>
