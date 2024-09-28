<template>
    <div>
        <div class="w-[1200px] mx-auto flex pl-[30px] mt-[10px]">
            <NuxtLink :to="`/policy/${PolicyAgreementEnum.SERVICE}`">
                <div
                    class="bg-[#EEEEEE] mr-[20px] px-[25px] py-[10px]"
                    :class="{ active: route.params.type == 'service' }"
                >
                    用户服务协议
                </div>
            </NuxtLink>
            <NuxtLink :to="`/policy/${PolicyAgreementEnum.PRIVACY}`">
                <div
                    class="bg-[#EEEEEE] mr-[20px] px-[25px] py-[10px]"
                    :class="{ active: route.params.type == 'privacy' }"
                >
                    隐私协议
                </div>
            </NuxtLink>
            <NuxtLink :to="`/policy/${PolicyAgreementEnum.PAY}`">
                <div
                    class="bg-[#EEEEEE] px-[25px] py-[10px] mr-[20px]"
                    :class="{ active: route.params.type == 'pay' }"
                >
                    用户支付协议
                </div>
            </NuxtLink>
            <NuxtLink :to="`/policy/${PolicyAgreementEnum.DISTRIBUTION}`">
                <div
                    class="bg-[#EEEEEE] px-[25px] py-[10px]"
                    :class="{ active: route.params.type == 'distribution' }"
                >
                    用户分销协议
                </div>
            </NuxtLink>
        </div>
        <div
            class="bg-white render-html p-[30px] w-[1200px] mx-auto min-h-screen"
        >
            <h1 class="text-center">{{ data.title }}</h1>
            <div class="mx-auto richText" v-html="data.content"></div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { getPolicy } from '~~/api/app'
import { PolicyAgreementEnum } from '@/enums/appEnums'

const route = useRoute()
const { data } = await useAsyncData(() =>
    getPolicy({
        type: route.params.type
    })
)

definePageMeta({
    layout: 'blank'
})
</script>
<style lang="scss" scoped>
.active {
    background-color: var(--el-color-primary);
    color: white;
}
::v-deep .richText {
    img {
        display: inline-block;
    }
}
</style>
