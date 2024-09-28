<!-- 网站信息 -->
<template>
    <div class="website-information">
        <el-form ref="formRef" :rules="rules" class="ls-form" :model="formData" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">示例设置</div>
                <el-form-item label="思维导图示例" prop="is_example">
                    <div>
                        <el-switch
                            v-model="formData.is_example"
                            :active-value="1"
                            :inactive-value="0"
                        ></el-switch>
                        <div class="form-tips">开启的话，前台显示示例按钮</div>
                    </div>
                </el-form-item>
                <el-form-item label="示例内容" prop="example_content">
                    <div class="w-[460px]">
                        <el-input
                            v-model.trim="formData.example_content"
                            type="textarea"
                            :rows="15"
                            resize="none"
                        />
                    </div>
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">内置提示词</div>
                <el-form-item label="提示词设置" prop="cue_word">
                    <div class="w-[460px]">
                        <el-input
                            v-model.trim="formData.cue_word"
                            placeholder="请输入"
                            type="textarea"
                            :rows="6"
                            resize="none"
                        ></el-input>
                        <div class="form-tips whitespace-pre-wrap">
                            <el-button
                                v-if="
                                    !(
                                        currentModel == 'minimax' ||
                                        currentModel == 'wenxin' ||
                                        currentModel == 'qwen'
                                    )
                                "
                                type="primary"
                                link
                                v-copy="
                                    `请按我接下来说的主题帮我制作一份思维导图，列出主分支内容和子分支内容，你需按以下格式返回数据：&quot;
# {标题}
## {子标题}
- {内容} 
...&quot;，不要返回其他提示信息或解释，我的主题是：{prompt}`
                                "
                            >
                                复制示例指令
                            </el-button>
                            如果示例指令效果不明显，或者效果不好，可自行调整提示词
                        </div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns v-perms="['setting.mindmap/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="mindMapSetting">
import { getMindMapConfig, setMindMapConfig } from '@/api/mind_map'

import type { FormInstance } from 'element-plus'
const formRef = ref<FormInstance>()

// 表单数据
const formData = reactive({
    is_example: 0,
    example_content: '',
    cue_word: ''
})

// 表单验证
const rules = {}

// 获取备案信息
const getData = async () => {
    const data = await getMindMapConfig()
    for (const key in formData) {
        //@ts-ignore
        formData[key] = data[key]
    }
}

// 设置备案信息
const handleSubmit = async () => {
    await formRef.value?.validate()
    await setMindMapConfig(formData)
    getData()
}

getData()
</script>

<style lang="scss" scoped></style>
