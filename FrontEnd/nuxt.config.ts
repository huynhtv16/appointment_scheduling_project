import { defineNuxtConfig } from 'nuxt/config'

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: false },
  modules: [
    '@nuxt/content',
    '@nuxt/test-utils',
    '@nuxt/eslint',
    '@nuxt/ui'
  ],
    runtimeConfig: {
    public: {
      apiBase: process.env.API_BASE || "http://127.0.0.1:8000/api"
    }
  },
  app: {
    head: {
      link: [
        { rel: 'stylesheet', href: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' },
        { rel: 'stylesheet', href: 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css' }
      ],
      script: [
        { src: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', tagPosition: 'bodyClose' }
      ]
    }
  },
  css: ['@/assets/css/main.css']
})
