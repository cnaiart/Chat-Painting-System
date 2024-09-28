import { setTheme } from '@/utils/theme'
import { defineStore } from 'pinia'
import { getdecorate } from '~/api'

interface ThemeStore {
    primaryColor: string
    minorColor: string
    btnColor: string
    vars: string
}
export const useThemeStore = defineStore({
    id: 'themeStore',
    state: (): ThemeStore => ({
        primaryColor: '',
        minorColor: '',
        btnColor: 'white',
        vars: ''
    }),
    actions: {
        async getTheme() {
            const data = await getdecorate({
                id: 9
            })
            const { primaryColor, minorColor, buttonColor } = JSON.parse(
                data.data
            )
            this.primaryColor = primaryColor
            this.minorColor = minorColor
            this.btnColor = buttonColor
            setTheme(
                {
                    primary: this.primaryColor
                },
                {
                    '--color-primary': this.primaryColor,
                    '--color-minor': this.minorColor,
                    '--color-btn-text': this.btnColor
                }
            )
        }
    }
})
