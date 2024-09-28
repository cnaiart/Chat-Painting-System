<template>
    <popup
        ref="popRef"
        width="auto"
        :click-modal-close="true"
        confirmButtonText=""
        cancelButtonText=""
    >
        <div class="h-[70vh] flex flex-col">
            <div class="flex-1 min-h-0">
                <ElScrollbar>
                    <div ref="posterRef" class="w-[440px] bg-[#f5f5f5]">
                        <div class="flex justify-center bg-[#F2F3F6]">
                            <!-- <el-image
                    fit="contain"
                    class="h-[440px]"
                    :src="imgContent.thumbnail"
                ></el-image> -->
                            <img
                                class="max-h-[440px] object-contain"
                                :src="imgContent.thumbnail || imgContent.image"
                                alt=""
                            />
                        </div>
                        <div class="px-[16px] mt-[12px]">
                            <div
                                class="title text-[16px] font-medium text-[#101010] line-clamp-2"
                            >
                                {{
                                    imgContent?.prompts_cn ||
                                    imgContent.original_prompts.prompt
                                }}
                            </div>
                            <div class="text-[#101010] mt-[10px] line-clamp-1">
                                {{ imgContent.original_prompts.prompt_en }}
                            </div>
                        </div>
                        <el-divider border-style="dashed" />
                        <div class="flex px-[16px] pt-[10px] pb-[30px]">
                            <div>
                                <div class="flex items-center">
                                    <!-- <el-image
                            class="w-[34px] h-[34px] rounded-full"
                            :src="imgContent.user_info.image"
                        ></el-image> -->
                                    <img
                                        class="w-[34px] h-[34px] rounded-full"
                                        :src="userStore.userInfo.avatar"
                                    />
                                    <div class="ml-2">
                                        {{ userStore.userInfo.nickname }}
                                    </div>
                                </div>
                                <div
                                    class="mt-[16px] font-medium text-[#101010] text-xl"
                                >
                                    {{ getWebsiteConfig.pc_title }}
                                </div>
                                <div class="mt-[10px] text-primary">
                                    {{ splitDomain(config.current_domain) }}
                                </div>
                            </div>
                            <div class="ml-auto p-[10px] bg-white rounded-lg">
                                <QrcodeVue
                                    :value="inviteLink"
                                    :size="100"
                                    :margin="1"
                                />
                            </div>
                        </div>
                    </div>
                </ElScrollbar>
            </div>
        </div>
        <template #footer>
            <div class="flex">
                <el-button
                    class="flex-1"
                    type="primary"
                    plain
                    size="large"
                    @click="copy(pcLink)"
                >
                    复制链接
                </el-button>
                <el-button
                    class="flex-1"
                    type="primary"
                    size="large"
                    @click="download"
                >
                    下载海报
                </el-button>
            </div>
        </template>
    </popup>
</template>

<script setup lang="ts">
import QrcodeVue from 'qrcode.vue'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'
import { splitDomain } from '@/utils/util'

const popRef = shallowRef()
//海报ref
const posterRef = shallowRef()
const userStore = useUserStore()
const { getWebsiteConfig, config } = useAppStore()
const imgContent: any = ref({})

const open = (option: any) => {
    popRef.value.open()
    imgContent.value = option
    console.log(option)
}

const inviteLink = computed(
    () => `${window.origin}/mobile?user_sn=${userStore.userInfo.sn}`
)

const { copy } = useCopy()
const pcLink = computed(
    () => `${window.origin}/pc?user_sn=${userStore.userInfo.sn}`
)

const downloadLoading = ref(false)
const download = async () => {
    try {
        downloadLoading.value = true
        await downloadHtml2Image(posterRef.value)
    } catch (error) {
        feedback.msgError('下载失败，请重试')
    } finally {
        downloadLoading.value = false
    }
}

defineExpose({
    open
})
</script>

<style scoped lang="scss"></style>
