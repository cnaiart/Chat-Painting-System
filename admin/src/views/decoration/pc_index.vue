<template>
    <div class="decoration-pages">
        <div class="flex flex-1 min-w-0 items-start overflow-x-auto overflow-y-hidden">
            <Preview
                class="flex flex-1 min-w-0 h-full"
                :page-data="pageData.data"
                v-model:index="selectWidgetIndex"
            />
            <div class="h-full py-4">
                <prop-setting :title="getSelectWidget.title">
                    <template #default>
                        <component
                            v-model:is-show="getSelectWidget.isShow"
                            v-model:prop="getSelectWidget.prop"
                            :is="widgets[getSelectWidget?.name]?.prop"
                        />
                    </template>
                </prop-setting>
            </div>
        </div>
        <footer-btns :fixed="true" v-perms="['decorate.page/save']">
            <el-button type="primary" @click="setData">保存</el-button>
        </footer-btns>
    </div>
</template>
<script lang="ts" setup name="decorationPc">
import { getDecoratePages, setDecoratePages } from '@/api/decoration'
import Preview from './component/pages/preview-pc.vue'
import PropSetting from './component/pages/prop-setting.vue'
import widgets from './component/widgets-pc'
import { getNonDuplicateID } from '@/utils/util'

const generatePageData = (widgetNames: string[]) => {
    return widgetNames.map((widgetName) => {
        const options = {
            id: getNonDuplicateID(),
            ...(widgets[widgetName]?.config() || {})
        }
        return options
    })
}

const pageData = ref({
    id: 11,
    type: 11,
    name: 'pc首页装修',
    data: [] as any[]
})
const selectWidgetIndex = ref(0)

const getSelectWidget = computed(() => {
    return pageData.value.data[selectWidgetIndex.value] || {}
})
const getData = async () => {
    const data = await getDecoratePages({ id: pageData.value.id })
    if (!data.data) {
        pageData.value.data = generatePageData(['title', 'intro', 'guide'])
        return
    }
    pageData.value = {
        ...data,
        data: JSON.parse(data.data)
    }
}

const setData = async () => {
    await setDecoratePages({
        ...pageData.value,
        data: JSON.stringify(pageData.value.data)
    })
    getData()
}
getData()
</script>
<style lang="scss" scoped>
.decoration-pages {
    position: absolute;
    inset: 0;
    @apply flex flex-col;
}
</style>
