<template>
    <div class="bg-white w-[330px] h-full flex flex-col">
        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div class="px-[15px] py-[15px]">
                    <div
                        class="border-b border-br-light border-solid pb-[12px] flex items-end justify-between"
                    >
                        <h3 class="text-xl font-medium">思维导图</h3>
                        <el-button link type="info" @click="emit('history')">
                            <template #icon>
                                <Icon name="el-icon-Clock" />
                            </template>
                            生成记录
                        </el-button>
                    </div>
                    <div class="mt-[10px]">
                        <div>
                            <h3 class="font-bold">
                                <span>帮我生成</span>
                                <span class="text-error">*</span>
                            </h3>

                            <!--    输入框    -->
                            <div class="mt-[10px] flex">
                                <l-textarea
                                    v-model="userInput"
                                    maxlength="1024"
                                    :rows="7"
                                    :clearable="true"
                                    :show-word-limit="false"
                                    :customStyle="{
                                        paddingBottom: '24px'
                                    }"
                                    placeholder="请输入简单描述，AI将智能输出markdown内容"
                                >
                                </l-textarea>
                            </div>
                        </div>
                        <div class="mt-[15px]">
                            <div class="flex">
                                <h3 class="font-bold mr-auto">
                                    <span>需求描述</span>
                                </h3>
                                <ElButton
                                    v-if="appStore.getMindMapConfig.is_example"
                                    link
                                    type="primary"
                                    @click="
                                        descInput =
                                            appStore.getMindMapConfig
                                                .example_content
                                    "
                                >
                                    试试示例
                                </ElButton>
                            </div>

                            <textarea
                                ref="textareaRef"
                                v-model="descInput"
                                class="bg-page h-[400px] w-full mt-[10px] rounded p-[10px] resize-none"
                            />
                        </div>
                    </div>
                </div>
            </ElScrollbar>
        </div>
        <div class="px-[15px] pb-[20px]">
            <!--模型选择-->
            <ModelPicker v-model="chatModel"></ModelPicker>
            <el-button
                size="large"
                type="primary"
                round
                class="w-full submit-btn !border-none"
                @click="handleCreate"
                :loading="isReceiving"
            >
                生成思维导图描述
            </el-button>
        </div>
        <TheGuidedPopup ref="guidedPopupRef" />
    </div>
</template>

<script setup lang="ts">
import { watchThrottled } from '@vueuse/core'
import { chatSendText } from '~/api/chat'
import ModelPicker from '~/components/chatting/model-picker.vue'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'

const emit = defineEmits<{
    (event: 'update', value: string): void
    (event: 'history'): void
}>()
const guidedPopupRef = shallowRef()
const appStore = useAppStore()
const userStore = useUserStore()
const userInput = ref('')
const descInput = ref('')
const textareaRef = shallowRef<HTMLTextAreaElement>(null)
const chatModel = ref('')

const isReceiving = ref(false)
const handleCreate = async () => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    if (!userInput.value) return feedback.msgError('请输入内容')
    if (isReceiving.value) return
    try {
        isReceiving.value = true

        await chatSendText(
            {
                model: chatModel.value,
                question: userInput.value,
                type: 4
            },
            {
                onstart() {
                    descInput.value = ''
                },
                onmessage(value) {
                    value
                        .trim()
                        .split('data:')
                        .forEach(async (text) => {
                            if (text !== '') {
                                try {
                                    const dataJson = JSON.parse(text)
                                    const { event, data, code } = dataJson

                                    if (event == 'error' && code === 101) {
                                        guidedPopupRef.value?.open()
                                    } else if (event == 'error') {
                                        feedback.msgError(data)
                                    }
                                    if (event == 'chat') {
                                        if (data) {
                                            descInput.value += data
                                        }
                                    }

                                    if (event === 'finish') {
                                        return
                                    }
                                } catch (error) {
                                    isReceiving.value = false
                                }
                            }
                        })
                },
                onclose() {
                    userStore.getUser()
                    isReceiving.value = false
                    scrollToBottom()
                }
            }
        )
    } catch (error) {
        isReceiving.value = false
    }
}

const scrollToBottom = () => {
    textareaRef.value?.scrollTo({
        top: textareaRef.value?.scrollHeight
    })
}
watchThrottled(
    descInput,
    (value) => {
        emit('update', value)
        isReceiving.value && scrollToBottom()
    },
    {
        throttle: 500
    }
)

watch(
    () => appStore.getMindMapConfig,
    (value) => {
        if (value?.is_example) {
            descInput.value = value?.example_content
        }
    },
    {
        immediate: true
    }
)

defineExpose({
    changDescInput(value: string) {
        descInput.value = value
    }
})
</script>
<style lang="scss" scoped>
.submit-btn {
    font-size: 16px;
    height: 52px !important;
    border-radius: 60px !important;
    background: linear-gradient(
        90deg,
        var(--gradient-1) 0%,
        var(--gradient-2) 100%
    );
}
</style>
