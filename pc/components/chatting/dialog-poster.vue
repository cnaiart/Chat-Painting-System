<template>
    <ElDialog v-model="showPopup" title="生成海报" show-close width="400px">
        <div class="h-[70vh]">
            <ElScrollbar v-loading="loading">
                <div
                    ref="posterRef"
                    class="poster rounded-[10px] overflow-hidden pb-[10px]"
                    v-if="!loading"
                >
                    <div class="poster-bg flex flex-col">
                        <img
                            class="w-full"
                            :src="
                                drawOptions.default == 2
                                    ? appStore.getImageUrl(
                                          drawOptions.posterUrl
                                      )
                                    : drawOptions.poster == 1
                                    ? appStore.getImageUrl(
                                          drawOptions.defaultUrl1
                                      )
                                    : appStore.getImageUrl(
                                          drawOptions.defaultUrl2
                                      )
                            "
                            alt=""
                        />
                        <div
                            class="flex-1 min-h-0"
                            :style="{
                                background: drawOptions.bgColor
                            }"
                        ></div>
                    </div>

                    <div
                        class="w-full h-full poster-contain1 bg-[#BBBBBB]"
                        :style="{
                            color: drawOptions.textColor
                        }"
                    >
                        <div class="px-[20px] pt-[120px]">
                            <div
                                class="bg-white rounded-lg p-[15px] text-tx-primary"
                            >
                                <div class="text-[18px] font-bold">
                                    <span class="line-clamp-2 leading-[25px]">
                                        {{ posterData.title }}
                                    </span>
                                </div>
                                <span
                                    class="text-[14px]"
                                    :class="{
                                        'line-clamp-[1]':
                                            drawOptions.showContentType == 1
                                    }"
                                    :style="{
                                        '-webkit-line-clamp':
                                            drawOptions.contentNum
                                    }"
                                >
                                    <div
                                        v-for="(
                                            text, index
                                        ) in posterData.content"
                                        :key="index"
                                        class="mb-[15px]"
                                        :class="{
                                            ' pt-[15px] border-t border-solid border-br-light':
                                                index > 0
                                        }"
                                    >
                                        <chat-content
                                            :content="text"
                                            :use-markdown="true"
                                        />
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div class="px-[20px] pt-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img
                                        :src="userStore.userInfo.avatar"
                                        alt=""
                                        class="w-[60px] h-[60px] rounded-full"
                                    />
                                    <div class="ml-[10px] text-[16px]">
                                        <div>
                                            {{ userStore.userInfo.nickname }}
                                        </div>
                                        <div v-if="drawOptions.showData == 1">
                                            {{ drawOptions.data }}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <QrcodeVue
                                        :value="inviteLink"
                                        :size="100"
                                        :margin="1"
                                    />
                                    <div>长按识别二维码</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </ElScrollbar>
        </div>
        <div class="flex justify-end mt-[10px]">
            <el-button round @click="download" :loading="downloadLoading">
                <template #icon>
                    <Icon name="el-icon-Download" />
                </template>
                下载
            </el-button>
            <el-button round @click="copy(pcLink)">
                <template #icon>
                    <Icon name="el-icon-DocumentCopy" />
                </template>
                复制链接
            </el-button>
        </div>
    </ElDialog>
</template>
<script lang="ts" setup>
import { getdecorate } from '~/api'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'
import QrcodeVue from 'qrcode.vue'
const appStore = useAppStore()
const showPopup = ref(false)
const downloadLoading = ref(false)
const drawOptions = ref<any>({})
const loading = ref(false)
const userStore = useUserStore()
const posterRef = shallowRef<HTMLDivElement>()
const posterData = reactive({
    title: '',
    content: ''
})
const getData = async () => {
    loading.value = true
    try {
        const { data } = await getdecorate({ id: 6 })
        drawOptions.value = JSON.parse(data)[0]?.content || {}
    } finally {
        loading.value = false
    }
}

const inviteLink = computed(
    () => `${window.origin}/mobile?user_sn=${userStore.userInfo.sn}`
)
const { copy } = useCopy()
const pcLink = computed(
    () => `${window.origin}/?user_sn=${userStore.userInfo.sn}`
)

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
const open = (data) => {
    showPopup.value = true
    posterData.title = data.title
    posterData.content = data.content
}
getData()

defineExpose({
    open
})
</script>

<style lang="scss" scoped>
.poster {
    width: 360px;
    position: relative;
    overflow: hidden;
    &-bg {
        position: absolute;
        width: 100%;
        height: 100%;
    }
    background: transparent;
}

.poster-contain1 {
    position: relative;
    z-index: 1;
    background-color: transparent;
}
</style>
