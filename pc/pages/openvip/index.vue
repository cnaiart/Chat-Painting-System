<template>
    <div>
        <NuxtLayout name="default">
            <div class="flex w-full h-full flex-col" v-if="tabLists.length">
                <div class="tab-lists">
                    <div class="flex mx-[-10px]">
                        <div
                            class="tab-item"
                            v-for="(item, index) in tabLists"
                            :key="index"
                            :class="{
                                'is-active': currentMode == item.mode
                            }"
                            @click="tabChange(item.mode)"
                        >
                            <span class="w-full">{{ item.name }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex-1 min-h-0">
                    <component v-if="currentItem" :is="currentItem.component" />
                </div>
            </div>
            <div
                class="w-full h-full bg-white rounded-[12px] flex items-center justify-center"
                v-else
            >
                <div class="text-xl">功能未开启!</div>
            </div>
        </NuxtLayout>
    </div>
</template>
<script setup lang="ts">
import { useAppStore } from '~/stores/app'
import Vip from './_components/vip.vue'
import Recharge from './_components/recharge.vue'
import RedeemCode from './_components/redeem-code.vue'
const appStore = useAppStore()
const router = useRouter()
const route = useRoute()

const tabLists = computed(() => {
    const tabs = [
        {
            name: '开通会员',
            show: appStore.getIsShowVip,
            mode: 'vip',
            component: markRaw(Vip)
        },
        {
            name: '余额充值',
            show: appStore.getIsShowRecharge,
            mode: 'recharge',
            component: markRaw(Recharge)
        },
        {
            name: '卡密兑换',
            show: appStore.getCardCodeConfig.is_open,
            mode: 'redeem_code',
            component: markRaw(RedeemCode)
        }
    ]
    return tabs.filter((item) => !!item.show)
})
const mode = route.query.mode as string
const currentMode = ref(mode)
const tabChange = (mode: string) => {
    currentMode.value = mode
    router.replace({
        path: '',
        query: {
            mode: mode
        }
    })
}
const currentItem = computed(() => {
    return tabLists.value.find((item) => item.mode === currentMode.value)
})
watch(
    tabLists,
    (value) => {
        if (!currentItem.value && value.length) {
            const [first] = value
            currentMode.value = first.mode
        }
    },
    {
        immediate: true
    }
)

watch(
    () => route.query.mode,
    (value: string) => {
        currentMode.value = value
        if (!currentItem.value) {
            const [first] = tabLists.value
            currentMode.value = first?.mode
        }
    }
)
definePageMeta({
    auth: true,
    layout: false
})
</script>
<style lang="scss" scoped>
.tab-lists {
    border-radius: 20px 20px 0 0;
    overflow: hidden;
    .tab-item {
        background-color: #e8e8e8;
        margin: 0 10px;
        display: flex;
        width: 33.3%;
        line-height: 50px;
        text-align: center;
        border-radius: 20px 20px 0 0;
        cursor: pointer;
        @apply text-2xl;
        &.is-active {
            @apply text-btn-text bg-primary;
        }
    }
}
</style>
