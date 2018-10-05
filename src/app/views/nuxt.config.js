// Load environment configuration with dotenv
const results = require('dotenv').config({
  path: `.${process.env.NODE_ENV}.env`
})
if (results.error) {
  throw results.error
}
const envConfig = results.parsed

const https = require('https')
const parseArgs = require('minimist')
const argv = parseArgs(process.argv.slice(2), {
  alias: {
    H: 'hostname',
    p: 'port'
  },
  string: ['H'],
  unknown: parameter => false
})

const port =
  argv.port ||
  process.env.PORT ||
  process.env.npm_package_config_nuxt_port ||
  '3000'
const host =
  argv.hostname ||
  process.env.HOST ||
  process.env.npm_package_config_nuxt_host ||
  'localhost'

module.exports = {
  mode: 'spa',
  env: envConfig,
  router: {
    middleware: ['init', 'i18n', 'check-auth' ]
  },
  head: {
    titleTemplate: '%s - Voice collector',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: 'OLLI Voice Collector' },
      { name: 'msapplication-config' }
    ],
    link: [
      { rel: 'apple-touch-icon', sizes: '180x180', href: '/apple-touch-icon.png' },
      { rel: 'icon', type: 'image/png', sizes: '32x32', href: '/favicon-32x32.png' },
      { rel: 'icon', type: 'image/png', sizes: '16x16', href: '/favicon-16x16.png' }
    ]
  },
  loading: { name: 'cube-grid', color: '#3B8070', background: '#333' },
  build: {
    analyze: true,
    vendor: [
      'axios',
      'gsap',
      'vuex-class',
      'nuxt-class-component',
      'nuxt-property-decorator',
      'element-ui'
    ],
    extend(config, ctx) {
      if (!ctx.isDev) {
        config.devtool = false
      }

      // inject `compilerModules` to vue-loader options
      config.module.rules.forEach(rule => {
        if (rule.loader === 'vue-loader') {
          rule.options.preLoaders = {
            lang: 'yaml-loader'
          }
          rule.options.loaders = {
            lang: '@kazupon/vue-i18n-loader'
          }
        }
      })
    }
  },
  modules: [
    '@nuxtjs/axios',
    ['@nuxtjs/google-analytics', {ua: 'UA-123'}],
    ['@nuxtjs/browserconfig', { TileColor: '#3f51b5' }],
    '~/modules/typescript.js'
  ],
  plugins: [
    { src: '~plugins/elementui.ts' },
    { src: '~plugins/fetch.ts' },
    { src: '~plugins/i18n.ts' },
    { src: '~plugins/scrolltop.ts' }
  ],
  // Options for axios fetch service
  axios: {
    baseURL: process.env.API_URL,
    timeout: 9000000,
    httpsAgent: new https.Agent({
      rejectUnauthorized: false
    }),
    withCredentials: false,
    headers: {'X-Requested-With': 'XMLHttpRequest'},
  }
}
