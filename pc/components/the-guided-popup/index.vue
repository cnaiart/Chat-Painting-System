<template>
    <ElDialog v-model="showPopup" width="450px">
        <template #header="{ titleId, titleClass }">
            <div class="my-header">
                <div :id="titleId" :class="titleClass" class="font-medium">
                    对话余额不足
                </div>
            </div>
        </template>
        <div class="min-h-[250px] text-tx-primary">
            <div>
                <div>你可以通过以下渠道获取对话条数：</div>
                <div
                    class="mt-[20px] flex items-center"
                    v-for="(item, index) in channelList"
                    :key="index"
                >
                    <div class="mr-[10px] font-medium">
                        {{ item.title }}
                    </div>
                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            round
                            @click="jump(item.path)"
                        >
                            {{ item.btnText }}
                        </el-button>
                    </div>
                </div>
            </div>
        </div>
    </ElDialog>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { useAppStore } from '~/stores/app'
import { getTaskMsg } from '@/api/recharge'
const showPopup = ref(false)
const open = () => {
    showPopup.value = true
}
const close = () => {
    showPopup.value = false
}
const router = useRouter()
const appStore = useAppStore()
const jump = (path: string) => {
    close()
    router.push(path)
}

const taskList = ref<any[]>()

const getTaskList = async () => {
    taskList.value = await getTaskMsg()
}

const channelList = computed(() => {
    const channelList = [
        {
            title: '免费获取任务奖励',
            btnText: '前往分享',
            path: '/recharge',
            show: taskList.value.length > 0
        },
        {
            title: '余额充值',
            btnText: '前往充值',
            path: '/openvip?type=recharge',
            show: appStore.getIsShowRecharge
        },
        {
            title: '开通会员',
            btnText: '开通会员',
            path: '/openvip',
            show: appStore.getIsShowVip
        }
    ]
    return channelList.filter((item) => item.show)
})

onMounted(() => {
    getTaskList()
})

defineExpose({
    open,
    close
})
</script>
