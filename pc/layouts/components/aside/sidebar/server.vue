<template>
    <!-- <div
            class="h-[58px] w-[64px] bg-white shadow rounded-lg fixed right-0 bottom-[300px] flex flex-col items-center justify-center"
            @click="showCoustomerServer"
        >
            <img src="@/assets/images/customer.png" class="w-[24px] h-[24px]" />
            <div class="text-[12px] mt-[6px]">客服</div>
        </div>  -->
    <el-popover placement="right-start" title="" :width="250" trigger="hover">
        <template #reference>
            <slot />
        </template>
        <div class="flex flex-col items-center" v-if="decorate">
            <el-image
                :src="getImageUrl(decorate[0].content.qrcode)"
                class="w-[140px] h-[140px] mb-[10px]"
            ></el-image>
            <div class="font-bold" v-if="decorate[0].content.title_status == 1">
                {{ decorate[0].content.title }}
            </div>
            <div
                class="mt-[10px] text-info text-[12px]"
                v-if="decorate[0].content.time_status == 1"
            >
                服务时间：{{ decorate[0].content.time }}
            </div>
            <div
                class="mt-[10px] text-info text-[12px]"
                v-if="decorate[0].content.mobile_status == 1"
            >
                联系电话：{{ decorate[0].content.mobile }}
            </div>
        </div>
    </el-popover>
</template>
<script lang="ts" setup>
import { getdecorate } from '@/api/index'
import { useAppStore } from '~~/stores/app'
const { getImageUrl } = useAppStore()

const decorate = useState(() => null)
const getServerData = async () => {
    const data = await getdecorate({ id: 3 })
    decorate.value = JSON.parse(data.data)
}

onBeforeMount(() => {
    if (!decorate.value) {
        getServerData()
    }
})
</script>
