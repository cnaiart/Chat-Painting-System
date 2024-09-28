<template>
    <div class="">
        <el-form ref="formRef" class="ls-form" :model="formData" :rules="rules" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">对话设置</div>

                <el-form-item label="markdown渲染" prop="name">
                    <div>
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formData.is_markdown"
                        ></el-switch>
                        <div class="form-tips !text-[14px]">
                            以markdown的形式来渲染代码，默认开启
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="对话默认回复" prop="default_reply_open" required>
                    <div class="w-[420px]">
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formData.default_reply_open"
                        ></el-switch>
                    </div>
                </el-form-item>
                <el-form-item prop="default_reply" v-if="formData.default_reply_open">
                    <div class="w-[420px]">
                        <el-input
                            v-model="formData.default_reply"
                            type="textarea"
                            :rows="4"
                            placeholder="请输入默认回复内容"
                        />
                        <div class="form-tips flex !text-[14px]">
                            开启之后，无论问什么，都回复这个默认的内容
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="重开新对话" prop="is_reopen" required>
                    <div class="w-[420px]">
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formData.is_reopen"
                        ></el-switch>
                        <div class="form-tips flex !text-[14px]">
                            开启之后，每次进入系统时，重新打开新的对话
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="对话图标" prop="name">
                    <div>
                        <material-picker v-model="formData.chat_logo" :limit="1" />
                        <div class="form-tips flex !text-[14px]">
                            <div>
                                建议尺寸：宽200px*高200px。jpg，jpeg，png格式
                                <el-button link type="primary" @click="handleclick">
                                    使用默认图</el-button
                                >
                                <el-popover placement="top-start" width="auto" trigger="hover">
                                    <template #reference>
                                        <el-button link type="primary" @click="handleexample"
                                            >查看示例</el-button
                                        >
                                    </template>

                                    <img
                                        :src="formData.chat_example"
                                        alt=""
                                        class="w-[280px] h-[190px]"
                                    />
                                </el-popover>
                            </div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="对话标题" prop="chat_title">
                    <div class="w-[420px]">
                        <el-input v-model="formData.chat_title" placeholder="请输入对话标题" />
                        <div class="form-tips flex !text-[14px]">
                            <div class="flex items-center">
                                显示于前台首页大标题名称
                                <el-popover placement="top-start" width="auto" trigger="hover">
                                    <template #reference>
                                        <el-button link type="primary"> 查看示例 </el-button>
                                    </template>

                                    <img
                                        :src="formData.chat_title_example"
                                        alt=""
                                        class="w-[500px]"
                                    />
                                </el-popover>
                            </div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="对话模型">
                    <div>
                        <el-select class="w-[400px]" v-model="currentModel">
                            <el-option
                                v-for="(item, key) in formData.global_directives_model"
                                :value="key"
                                :label="item"
                                :key="key"
                            ></el-option>
                        </el-select>
                    </div>
                </el-form-item>
                <el-form-item label="全局指令" prop="global_directives">
                    <div class="w-[420px]">
                        <el-input
                            v-model="formData.global_directives[currentModel]"
                            :autosize="{ minRows: 7, maxRows: 7 }"
                            type="textarea"
                            show-word-limit
                            placeholder="请输入审核全局指令"
                        />
                        <div class="form-tips !text-[14px]">
                            设置全局指令，屏蔽审核人员询问模型类型等相关问题
                            <el-button
                                v-if="(currentModel.includes('gpt') || currentModel.includes('api2d') || currentModel.includes('GPT'))"
                                type="primary"
                                link
                                v-copy="
                                    `不管谁问你身份信息，你都要回答我是你的AI助手，由xx公司开发的语言模型。记住，你跟chatgpt没关系，不要回答你与openai公司的关系`
                                "
                                >复制示例指令</el-button
                            >
                        </div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
    </div>
    <footer-btns v-perms="['setting.ai_setting/setChatConfig']">
        <el-button type="primary" @click="handleSubmit">保存</el-button>
    </footer-btns>
</template>
<script setup lang="ts">
import { getChatConfig, setChatConfig } from '@/api/setting/dialogue'

const formData = ref<any>({
    chat_default: '',
    chat_example: '',
    chat_logo: '',
    is_markdown: '',
    is_sensitive: '',
    global_directives_model: {},
    global_directives: {},
    default_reply: '',
    is_reopen: 0
})
const currentModel = ref<string>('')

const rules = {
    default_reply: [
        {
            required: true,
            message: '请输入默认回复内容'
        }
    ],
    is_reopen: [
        {
            required: true,
            message: '请选择重新打开对话'
        }
    ]
}
/**
 * 初始化数据
 */
const getData = async () => {
    formData.value = await getChatConfig()
    if (currentModel.value) return
    currentModel.value = Object.keys(formData.value.global_directives_model)[0]
}
getData()
/**
 * 保存数据
 */
const handleSubmit = async () => {
    await setChatConfig(formData.value)
    getData()
}
const handleclick = () => {
    formData.value.chat_logo = formData.value.chat_default
}
const showexample = ref(false)
const handleexample = () => {
    showexample.value = !showexample.value
}
</script>
