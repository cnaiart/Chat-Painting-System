<template>
    <div class="dialog">
        <div class="dialog__trigger" @click="open">
            <!-- 触发弹窗 -->
            <slot name="trigger"></slot>
        </div>

        <el-dialog
            v-model="visible"
            width="920px"
            :align-center="true"
            style="border-radius: 12px"
        >
            <template #header>
                <span class="text-xl font-medium text-[#101010]"
                    >关键词推荐</span
                >
            </template>
            <el-tabs v-model="activeName" @tab-change="getKeywordPrompt">
                <el-tab-pane
                    v-for="(item, index) in keywordCateList"
                    :key="index"
                    :label="item.name"
                    :name="index"
                >
                </el-tab-pane>
            </el-tabs>

            <ElScrollbar height="400px">
                <template
                    v-if="
                        keywordPromptData.prompt.length ||
                        keywordPromptData.cate_prompt.length
                    "
                >
                    <div
                        v-for="(item, index) in keywordPromptData.cate_prompt"
                        :key="index"
                        class="keyword-container"
                    >
                        <div class="keyword-title text-base text-[#666]">
                            {{ item.name }}({{ item?.prompt.length }})
                        </div>
                        <div
                            v-for="citem in item.prompt"
                            :key="citem.text"
                            class="keyword-item"
                            :class="{
                                'keyword-item-active': currentPrompt.includes(
                                    citem.prompt_en
                                )
                            }"
                            @click="onChoicePrompt(citem.prompt_en)"
                        >
                            {{ citem.prompt }}
                        </div>
                    </div>
                    <div
                        v-if="keywordPromptData.prompt.length"
                        class="keyword-container"
                    >
                        <div
                            v-if="keywordPromptData.cate_prompt.length"
                            class="keyword-title text-base text-[#666]"
                        >
                            其它({{ keywordPromptData.prompt.length }})
                        </div>
                        <div
                            v-for="item in keywordPromptData.prompt"
                            :key="item.text"
                            class="keyword-item"
                            :class="{
                                'keyword-item-active': currentPrompt.includes(
                                    item.prompt_en
                                )
                            }"
                            @click="onChoicePrompt(item.prompt_en)"
                        >
                            {{ item.prompt }}
                        </div>
                    </div>
                </template>
                <!--  空状态  -->
                <div
                    v-else
                    class="flex items-center justify-center w-full h-full"
                >
                    <el-result title="提示" sub-title="暂无对应关键词"
                        >>
                        <template #icon>
                            <el-image
                                class="w-[200px] h-[200px]"
                                :src="PromptEmpty"
                            />
                        </template>
                    </el-result>
                </div>
            </ElScrollbar>

            <template #footer>
                <span class="dialog-footer">
                    <el-button type="primary" @click="onPromptAdd()">
                        添加到文本描述
                    </el-button>
                    <el-button
                        type="primary"
                        class="ml-[10px]"
                        :plain="true"
                        @click="onAlternatePrompt()"
                    >
                        替换当前文本描述
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import { keywordCate, keywordPrompt } from '~/api/drawing'
import PromptEmpty from 'assets/images/empty_news.png'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: ''
    }
)

const visible = ref(false)
const activeName = ref(0)
const keywordCateList = ref<any[]>([])
const keywordPromptData = ref<any>({
    prompt: [],
    cate_prompt: []
})
const currentPrompt = ref<string[]>([])

watchEffect(() => {
    const prompt = props.modelValue
    if (prompt == '') {
        currentPrompt.value = []
    }
})

// 选择关键词
const onChoicePrompt = (text: string) => {
    const index = currentPrompt.value.findIndex((item: string) => item == text)
    if (index >= 0) {
        currentPrompt.value.splice(index, 1)
        return
    }
    currentPrompt.value.push(text)
}

// 添加关键词文本
const onPromptAdd = () => {
    visible.value = false
    if (props.modelValue.trim() == '') {
        emit('update:modelValue', currentPrompt.value.join(','))
    } else {
        const keyword = props.modelValue + ',' + currentPrompt.value.join(',')
        emit('update:modelValue', keyword)
    }
    currentPrompt.value = []
}

// 替换关键词文本
const onAlternatePrompt = () => {
    visible.value = false
    emit('update:modelValue', currentPrompt.value.join(',') + ',')
    currentPrompt.value = []
}

const open = () => {
    visible.value = true
}

const getKeywordCate = async () => {
    try {
        keywordCateList.value = await keywordCate()
        await getKeywordPrompt()
    } catch (error) {
        console.log('获取关键词分类错误', error)
    }
}

const getKeywordPrompt = async () => {
    try {
        keywordPromptData.value = await keywordPrompt({
            id: keywordCateList.value[activeName.value].id
        })
    } catch (error) {
        console.log('获取关键词错误', error)
    }
}

onMounted(() => {
    getKeywordCate()
})
</script>

<style lang="scss" scoped>
.keyword-container {
    .keyword-title {
        position: relative;
        padding: 10px;
        &::before {
            content: '';
            position: absolute;
            left: 0;
            top: 48%;
            transform: translateY(-52%);
            width: 4px;
            height: 14px;
            background-color: var(--el-color-primary);
        }
    }
    .keyword-item {
        color: #101010;
        cursor: pointer;
        display: inline-block;
        padding: 6px 15px;
        margin-right: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
        background-color: #f5f5f5;
    }
    .keyword-item-active {
        color: var(--el-color-primary);
        background-color: var(--el-color-primary-light-9);
    }
}
</style>
