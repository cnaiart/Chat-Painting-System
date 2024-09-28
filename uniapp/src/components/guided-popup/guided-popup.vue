<template>
    <u-popup v-model="showPopup" mode="center" closeable border-radius="14">
        <view class="w-[670rpx] min-h-[500rpx]">
            <view class="p-[28rpx] text-center font-medium text-xl">
                对话余额不足
            </view>
            <view
                class="border-t border-solid border-light border-0 px-[40rpx] py-[30rpx]"
            >
                <view>你可以通过以下渠道获取对话条数：</view>
                <view
                    class="mt-[40rpx] flex items-center"
                    v-for="(item, index) in channelList"
                    :key="index"
                >
                    <view class="mr-[20rpx] font-medium">
                        {{ item.title }}
                    </view>
                    <view class="ml-auto">
                        <u-button
                            type="primary"
                            shape="circle"
                            size="medium"
                            :customStyle="{
                                padding: '0 24rpx',
                                height: '56rpx'
                            }"
                            @click="jump(item.path)"
                        >
                            {{ item.btnText }}
                        </u-button>
                    </view>
                </view>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { useRouter } from 'uniapp-router-next'
// import { useRouter } from 'uniapp-router-next-zm'
import { useAppStore } from '@/stores/app'
import { getTask } from '@/api/task'
import { onLoad } from '@dcloudio/uni-app'
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
    router.navigateTo(path)
}
const taskList = ref([])

const getTaskList = async () => {
    taskList.value = await getTask()
    console.log(taskList.value)
}

const channelList = computed(() => {
    const data = [
        {
            title: '免费获取任务奖励',
            btnText: '前往分享',
            path: '/packages/pages/task_center/task_center',
            show: taskList.value.length > 0
        },
        {
            title: '余额充值',
            btnText: '前往充值',
            path: '/packages/pages/recharge/recharge',
            show: appStore.getIsShowRecharge
        },
        {
            title: '开通会员',
            btnText: '开通会员',
            path: '/packages/pages/open_vip/open_vip',
            show: appStore.getIsShowVip
        }
    ]
    return data.filter((item) => item.show)
})

onLoad(() => {
    getTaskList()
})

defineExpose({
    open,
    close
})
</script>
