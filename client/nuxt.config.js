require('dotenv').config()
const { join } = require('path')
const { copySync, removeSync } = require('fs-extra')
const webpack = require("webpack");

module.exports = {
  // mode: 'spa', // Comment this for SSR

  server: {
    port: process.env.CLIENT_PORT, // default: 3000
  },

  srcDir: __dirname,

  env: {
    apiUrl: process.env.API_URL || process.env.APP_URL + '/api',
    serverUrl: process.env.APP_URL,
    appName: process.env.APP_NAME || '420Portal',
  },

  head: {
    title: process.env.APP_NAME,
    titleTemplate: '%s',
    meta: [
      { charset: 'UTF-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: 'Share Your Marijuana Pictures & Weed Videos for Fun. Cannabis Pictures and Videos.' },
      { hid: 'title', name: 'title', content: 'Marijuana Pictures - Weed Videos - Cannabis Images' },
      { hid: 'keywords', name: 'keywords', content: 'marijuana, weed, cannabis, images, pictures, videos' }
    ],
    link: [
      { rel: 'icon', type: 'image/png', href: '/favicon.png' }
    ],
  },

  loading: { color: '#EFA720' },

  router: {
    middleware: ['check-auth']
  },

  css: [
    { src: '~assets/sass/app.scss', lang: 'scss' }
  ],

  plugins: [
    '~components/global',
    '~plugins/vform',
    '~plugins/axios',
    '~plugins/fontawesome',
    // '~plugins/nuxt-client-init', // Comment this for SSR
    '~plugins/vuesax',
    { src: '~plugins/client', mode: 'client' },
    { src: '~plugins/bootstrap', mode: 'client' },
    { src: '~plugins/infinite-loading', mode: 'client' }
  ],

  modules: [
    '@nuxtjs/router',
    '@nuxtjs/device'
  ],

  build: {
    extractCSS: true,
    /**
     * add external plugins
     */
    vendor: ["jquery", "bootstrap"],
    plugins: [
      new webpack.ProvidePlugin({
        $: "jquery",
        jquery: "jquery",
      })
    ],
  },

  hooks: {
    generate: {
      done (generator) {
        // Copy dist files to public/_nuxt
        if (generator.nuxt.options.dev === false && generator.nuxt.options.mode === 'spa') {
          const publicDir = join(generator.nuxt.options.rootDir, 'public', '_nuxt')
          removeSync(publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '_nuxt'), publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '200.html'), join(publicDir, 'index.html'))
          removeSync(generator.nuxt.options.generate.dir)
        }
      }
    }
  }
}
