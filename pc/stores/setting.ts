import { defineStore } from 'pinia'
import { setTheme } from '@/utils/theme'
import { SETTING_KEY } from '@/enums/cacheEnums'
import defaultSetting from '~/config/setting'

export const useSettingStore = defineStore({
    id: 'setting',
    state: () => {
        const storageSetting = useCookie(SETTING_KEY)
        const state = {
            ...defaultSetting
        }
        let settingJson = {}
        try {
            settingJson = JSON.parse(storageSetting.value || '')
        } catch (error) {}

        isObject(settingJson) && Object.assign(state, storageSetting.value)
        return state
    },
    actions: {
        // 设置布局设置
        setSetting(data: Record<string, any>) {
            const oneWeek = 7 * 24 * 60 * 60 * 1000
            const storageSetting = useCookie(SETTING_KEY, {
                expires: new Date(Date.now() + oneWeek)
            })
            const { key, value } = data
            if (Reflect.has(this, key)) {
                //@ts-ignore
                this[key] = value
            }
            const settings: any = Object.assign({}, this.$state)
            delete settings.showDrawer
            storageSetting.value = JSON.stringify(settings)
        },
        // 设置主题色
        setTheme(isDark: boolean) {
            setTheme(
                {
                    primary: this.theme,
                    success: this.successTheme,
                    warning: this.warningTheme,
                    danger: this.dangerTheme,
                    error: this.errorTheme,
                    info: this.infoTheme
                },
                {},
                isDark
            )
        },
        resetTheme() {
            const storageSetting = useCookie(SETTING_KEY)
            for (const key in defaultSetting) {
                //@ts-ignore
                this[key] = defaultSetting[key]
            }
            storageSetting.value = ''
        }
    }
})
