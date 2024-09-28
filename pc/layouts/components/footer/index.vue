<template>
    <footer
        class="layout-footer flex flex-wrap justify-center text-center text-xs text-tx-secondary py-[10px]"
    >
        <!-- <NuxtLink> 关于我们 </NuxtLink>
            ｜ -->
        <NuxtLink
            :to="`/policy/${PolicyAgreementEnum.SERVICE}`"
            target="_blank"
            class="hover:underline"
        >
            用户协议
        </NuxtLink>
        ｜
        <NuxtLink
            :to="`/policy/${PolicyAgreementEnum.PRIVACY}`"
            target="_blank"
            class="hover:underline"
        >
            隐私政策
        </NuxtLink>
        ｜
        <NuxtLink
            :to="`/policy/${PolicyAgreementEnum.PAY}`"
            target="_blank"
            class="hover:underline mr-1"
        >
            用户支付协议
        </NuxtLink>
        <a
            class="mx-2 hover:underline flex items-center"
            :href="item.value"
            target="_blank"
            v-for="item in appStore.getCopyrightConfig"
            :key="item.key"
        >
            <img
                v-if="item.icon"
                :src="item.icon"
                class="h-[16px] mr-[4px]"
                alt=""
            />
            {{ item.key }}
        </a>
    </footer>
</template>
<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { PolicyAgreementEnum } from '@/enums/appEnums'
const appStore = useAppStore()
</script>

<style lang="scss" scoped></style>
