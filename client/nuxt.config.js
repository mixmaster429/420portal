require('dotenv').config()
const { join } = require('path')
const { copySync, removeSync } = require('fs-extra')
const webpack = require("webpack");
import axios from 'axios';

module.exports = {
  // mode: 'spa', // Comment this for SSR

  generate: {
    minify: {
      collapseWhitespace: false
    }
  },

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
      { name: 'viewport', content: 'width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0' },
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
    '~plugins/moment',
    { src: '~plugins/client', mode: 'client' },
    { src: '~plugins/bootstrap', mode: 'client' },
    { src: '~plugins/infinite-loading', mode: 'client' },
    { src: '~plugins/vue2-google-maps', mode: 'client' },
    { src: '~plugins/file-upload', mode: 'client' }
  ],

  modules: [
    '@nuxtjs/router',
    '@nuxtjs/device',
    '@nuxtjs/toast',
    '@nuxtjs/sitemap',
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
        jQuery: "jquery",
      })
    ],
    transpile: [/^vue2-google-maps($|\/)/],
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
  },

  toast: {
    position: 'top-right',
    duration: 1000,
  },

  sitemap: {
    hostname: 'https://www.420portal.com',
    sitemaps: [
      {
        path: '/sitemap.xml',
        xmlNs: 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
        routes: async () => {
          const { data } = await axios.get(process.env.APP_URL + '/api/sitemap')
          return data;
        }
      },
      {
        path: '/sitemap/marijuana-strains.xml',
        xmlNs: 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
        routes: async () => {
          const { data } = await axios.get(process.env.APP_URL + '/api/sitemap/strains')
          return data;
        }
      },
      {
        path: '/sitemap/users.xml',
        xmlNs: 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
        routes: async () => {
          const { data } = await axios.get(process.env.APP_URL + '/api/sitemap/users')
          return data;
        }
      },
      {
        path: '/sitemap/companies.xml',
        xmlNs: 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
        routes: async () => {
          const { data } = await axios.get(process.env.APP_URL + '/api/sitemap/companies')
          return data;
        }
      },
      {
        path: '/sitemap/united-states.xml',
        xmlNs: 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
        routes: async () => {
          const { data } = await axios.get(process.env.APP_URL + '/api/sitemap/states')
          return data;
        }
      },
      {
        path: '/sitemap/forums.xml',
        xmlNs: 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
        routes: async () => {
          const { data } = await axios.get(process.env.APP_URL + '/api/sitemap/forums')
          return data;
        }
      },
      {
        path: '/sitemap/news.xml',
        xmlNs: 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
        routes: async () => {
          const { data } = await axios.get(process.env.APP_URL + '/api/sitemap/news')
          return data;
        }
      },
    ],
  },

  buildModules: [
    '@nuxtjs/google-analytics'
  ],
  googleAnalytics: {
    id: 'UA-142049334-2'
  }
}
