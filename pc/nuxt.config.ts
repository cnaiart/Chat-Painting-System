// https://v3.nuxtjs.org/api/configuration/nuxt.config
import { URL, fileURLToPath } from 'node:url'
import { createSvgIconsPlugin } from 'vite-plugin-svg-icons'
import { getEnvConfig } from './nuxt/env'
const envConfig = getEnvConfig()
export default defineNuxtConfig({
    css: ['@/assets/styles/index.scss'],
    modules: ['@pinia/nuxt', '@nuxtjs/tailwindcss', '@element-plus/nuxt'],
    app: {
        baseURL: envConfig.baseUrl
    },
    runtimeConfig: {
        public: {
            ...envConfig
        }
    },
    ssr: !!envConfig.ssr,
    vite: {
        plugins: [
            createSvgIconsPlugin({
                iconDirs: [
                    fileURLToPath(new URL('./assets/icons', import.meta.url))
                ],
                symbolId: 'local-icon-[dir]-[name]'
            })
        ]
    }
})