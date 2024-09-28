<template>
    <div>
        <NuxtLayout name="up-down">
            <div class="h-full" ref="pageWrapRef">
                <ElScrollbar>
                    <div
                        class="page-content flex flex-col"
                        :style="{
                            minHeight: `${wrapHeight}px`
                        }"
                    >
                        <div class="flex-1 min-h-0">
                            <div
                                v-for="(item, index) in pageData"
                                :key="item.id"
                            >
                                <component
                                    v-if="item.isShow"
                                    :is="widgets[item.name]"
                                    :prop="item.prop"
                                />
                            </div>
                        </div>

                        <LayoutFooter />
                    </div>
                </ElScrollbar>
            </div>
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import { getdecorate } from '@/api'
import widgets from './_components'
import LayoutFooter from '@/layouts/components/footer/index.vue'
import { useElementSize } from '@vueuse/core'
import { useUserStore } from '~/stores/user'

const userStore = useUserStore()
const route = useRoute()
const pageWrapRef = shallowRef()
const { data: pages } = await useAsyncData(() => getdecorate({ id: 11 }))
const pageData = computed(() => {
    try {
        return JSON.parse(pages.value.data)
    } catch (error) {
        console.error(error)
        return []
    }
})

const { height: wrapHeight } = useElementSize(pageWrapRef)
onMounted(async () => {
    if (route.query.cid || route.query.user_sn) {
        const cid: any = useCookie('cid')
        const user_sn: any = useCookie('user_sn')
        cid.value = route.query.cid
        user_sn.value = route.query.user_sn
        await nextTick()
        userStore.checkShare()
    }
    await nextTick()
})

definePageMeta({
    layout: false,
    showLogo: true
})
</script>

<style lang="scss" scoped>
.page-content {
    background: radial-gradient(
        farthest-side at 0 0,
        var(--el-color-primary-light-9) 30%,
        #fff 100%
    );
}
</style>
