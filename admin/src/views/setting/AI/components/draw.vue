<template>
    <div>
        <el-form label-width="120px" ref="formRef" :rules="rules" :model="selectData">
            <el-form-item label="绘画功能">
                <div>
                    <el-switch
                        v-model="formData.is_open"
                        :active-value="1"
                        :inactive-value="0"
                    ></el-switch>
                    <div class="form-tips !text-[14px]">
                        默认关闭；绘画功能关闭后，有关绘画的入口将不显示
                    </div>
                </div>
            </el-form-item>

            <el-form-item label="AI接口" class="is-required">
                <div>
                    <el-radio-group v-model="interfaceKey" @change="interChange">
                        <el-radio
                            v-for="(item, index) in formData.config_lists"
                            :key="index"
                            :label="item.type"
                            :disabled="!item.is_open"
                            >{{ item.name }}</el-radio
                        >
                    </el-radio-group>
                    <div
                        class="form-tips !text-[14px]"
                        v-if="interfaceKey?.indexOf('yijian_sd') != -1"
                    >
                        启用意间绘画需按文档设置任务队列，php扩展gd库支持jpeg
                    </div>
                    <div class="flex items-center" v-if="interfaceKey?.indexOf('zhishuyun') != -1">
                        <span class="form-tips !text-[14px]"
                            >如果您已开通知数云接口，可直接填写；如果未开通</span
                        >
                        <a
                            href="https://auth.zhishuyun.com/auth/login?inviter_id=c7ff8573-940e-4dd0-828c-91adeda5f5dd&redirect=https://data.zhishuyun.com"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2"> 前往开通 </el-button>
                        </a>
                        <el-popover placement="top-start" width="auto" trigger="hover">
                            <template #reference>
                                <el-button type="primary" link class="ml-2">扫码开通</el-button>
                            </template>
                            <el-image :src="zhishiyun_code" class="w-[150px] h-[150px]"></el-image>
                        </el-popover>
                    </div>
                </div>
            </el-form-item>
            <el-form-item
                label="图片代理域名"
                prop="agency_api"
                v-if="selectData?.type?.indexOf('mdd') == 0"
            >
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入图片代理域名"
                            class="w-[400px]"
                            v-model="selectData.proxy_url"
                        ></el-input>
                    </div>

                    <div class="form-tips !text-[14px]">
                        图片代理域名，不填写默认为：https://cdn.discordapp.com
                    </div>
                </div>
            </el-form-item>
            <el-form-item
                label="自定义API域名"
                prop="agency_api"
                v-if="selectData?.type?.indexOf('dalle') == 0"
            >
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入自定义API域名"
                            class="w-[400px]"
                            v-model="selectData.proxy_url"
                        ></el-input>
                    </div>

                    <div class="form-tips !text-[14px]">
                        反向代理API域名，不填写默认为：https://api.openai.com
                    </div>
                </div>
            </el-form-item>
            <el-form-item
                label="API域名"
                prop="agency_api"
                v-if="selectData?.type?.indexOf('mdd') == 0"
            >
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入自定义API域名"
                            class="w-[400px]"
                            v-model="selectData.proxy_api"
                        ></el-input>
                    </div>

                    <div class="form-tips !text-[14px]">
                        反向代理API域名，不填写默认为：https://discord.com
                    </div>
                </div>
            </el-form-item>
            <el-form-item
                label="API域名"
                prop="proxy_url"
                v-if="selectData?.type?.indexOf('sd') == 0"
            >
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入API域名"
                            class="w-[400px]"
                            v-model="selectData.proxy_url"
                        ></el-input>
                    </div>

                    <div class="form-tips !text-[14px]">
                        该项为必填，不填写则无法使用
                    </div>
                </div>
            </el-form-item>
            <el-form-item label="WSS域名" v-if="selectData?.type?.indexOf('mdd') == 0">
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入WSS监听域名,必须以wss://开头"
                            class="w-[400px]"
                            v-model="selectData.proxy_wss"
                        ></el-input>
                    </div>

                    <div class="form-tips !text-[14px]">
                        WSS域名，不填写默认为：wss://gateway.discord.gg
                    </div>
                </div>
            </el-form-item>
            <el-form-item
                label="服务器ID"
                prop="guild_id"
                v-if="selectData?.type?.indexOf('mdd') == 0"
            >
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入服务器ID"
                            class="w-[400px]"
                            v-model="selectData.guild_id"
                        ></el-input>
                    </div>
                </div>
            </el-form-item>
            <el-form-item
                label="CHANNEL_ID"
                prop="channel_id"
                v-if="selectData?.type?.indexOf('mdd') == 0"
            >
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入频道ID"
                            class="w-[400px]"
                            v-model="selectData.channel_id"
                        ></el-input>
                    </div>
                </div>
            </el-form-item>
            <el-form-item
                label="SESSION_ID"
                prop="session_id"
                v-if="selectData?.type?.indexOf('mdd') == 0"
            >
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入会话ID"
                            class="w-[400px]"
                            v-model="selectData.session_id"
                        ></el-input>
                    </div>
                </div>
            </el-form-item>
            <el-form-item
                label="机器人token"
                prop="bot_token"
                v-if="selectData?.type?.indexOf('mdd') == 0"
            >
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入机器人token"
                            class="w-[400px]"
                            v-model="selectData.bot_token"
                        ></el-input>
                    </div>
                </div>
            </el-form-item>
            <el-form-item label="绘画翻译" required>
                <div>
                    <el-switch
                        v-model="selectData.auto_translate"
                        :active-value="1"
                        :inactive-value="0"
                    ></el-switch>
                    <div class="form-tips !text-[14px]">
                        开启自动翻译MJ关键词为英文，让绘图更精准。
                    </div>
                </div>
            </el-form-item>
            <template v-if="selectData.auto_translate">
                <el-form-item label="翻译形式" required>
                    <el-radio-group v-model="selectData.translate_type">
                        <el-radio :label="1">系统自动翻译</el-radio>
                        <el-radio :label="2">用户手动翻译</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="模型" prop="name">
                    <div>
                        <el-select class="w-[400px]" v-model="formData.translate_config.model">
                            <el-option
                                v-for="(item, key) in formData.translate_model_lists"
                                :value="key"
                                :label="item"
                                :key="item"
                            ></el-option>
                        </el-select>
                        <div class="form-tips">
                            选择翻译接口后，需前往【AI对话配置】设置相应的对话模型参数<br />
                            <span v-if="formData.translate_config.model == 'BAIDU'">
                                推荐使用ERNIE-Bot-turbo、ERNIE-Bot-4模型，这两个模型具有更强的理解能力，翻译效果更好
                            </span>
                        </div>
                    </div>
                </el-form-item>

                <el-form-item label="翻译指令" prop="translate_config.model">
                    <div class="w-[420px]">
                        <el-input
                            v-model="formData.translate_config.prompt"
                            :autosize="{ minRows: 7, maxRows: 7 }"
                            type="textarea"
                            show-word-limit
                            placeholder="请输入翻译指令"
                        />
                        <div class="form-tips flex !text-[14px]">
                            {prompt}是内置变量，表示用户输入的描述词
                        </div>
                        <div class="form-tips w-[400px] !text-[14px]">
                            <p>
                                示例：我会用任何语言和你交流，你只需将我的话翻译为英语，不要解释我的话或者回复其他信息，请立刻将我的话翻译返回，我的话是:{prompt}
                            </p>
                        </div>
                    </div>
                </el-form-item>
            </template>
            <el-form-item
                label="超时处理时长"
                prop="time_out"
                v-if="selectData?.type?.indexOf('dalle') == -1"
            >
                <div>
                    <div class="flex">
                        <el-input
                            type="number"
                            placeholder="请填写超时处理时长"
                            class="w-[400px]"
                            v-model="formData.time_out"
                            ><template #append>分钟</template>
                        </el-input>
                    </div>
                    <div class="form-tips !text-[14px]">
                        默认10分钟，设置时长过短或过长可能会影响到绘画体验，请谨慎操作！
                    </div>
                </div>
            </el-form-item>
        </el-form>
    </div>
</template>

<script setup lang="ts">
import zhishiyun_code from '@/assets/images/zhishuyun_code.png'
import { getDrawConfig, setDrawConfig } from '@/api/setting/AI'
const props = defineProps({
    popRef: {
        type: Object,
        default: {} as any
    }
})

//表单ref
const formRef = shallowRef()

//AI接口列表

//表单数据
const formData = ref({
    is_open: '', //是/否开启
    disclaimer_status: 0, // 是否开启免责声明 0 关闭 1开启
    disclaimer_content: '',
    time_out: 10,
    config_lists: [] as any[],
    translate_config: {
        model: '',
        prompt: ''
    },
    translate_model_lists: []
})

const selectData: any = ref({})

const interChange = (value: any) => {
    selectData.value = formData.value.config_lists[value]
    selectData.value.status = 1
}
const interfaceKey = ref('')
//表单校验规则
const rules = {
    guild_id: [
        {
            required: true,
            message: '请填写服务器ID',
            trigger: ['blur']
        }
    ],
    channel_id: [
        {
            required: true,
            message: '请填写频道ID',
            trigger: ['blur']
        }
    ],
    session_id: [
        {
            required: true,
            message: '请填写会话ID',
            trigger: ['blur']
        }
    ],
    bot_token: [
        {
            required: true,
            message: '请填写机器人token',
            trigger: ['blur']
        }
    ],
    proxy_url: [
        {
            required: true,
            message: '请填写API域名',
            trigger: ['blur']
        }
    ],
}

//获取数据
const getData = async () => {
    formData.value = await getDrawConfig()
    selectData.value = Object.values(formData.value?.config_lists).find((item) => item.status) || {}
    interfaceKey.value = selectData.value.type
}

const submit = async () => {
    await formRef.value.validate()
    await setDrawConfig({
        draw_config: {
            ...selectData.value,
            is_open: formData.value.is_open,
            disclaimer_status: formData.value.disclaimer_status,
            disclaimer_content: formData.value.disclaimer_content,
            time_out: formData.value.time_out
        },
        translate_config: formData.value.translate_config
    })
    getData()
}

onMounted(() => {
    getData()
})

defineExpose({ submit })
</script>

<style scoped lang="scss"></style>
