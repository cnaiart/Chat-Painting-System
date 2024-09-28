<template>
    <div class="pt-[10px]">
        <el-form label-width="120px" ref="formRef" :rules="rules" :model="formData">
            <el-form-item label="AI接口" class="is-required">
                <div>
                    <el-radio-group v-model="interfaceKey" @change="interChange">
                        <el-radio
                            v-for="(item, index) in interfaceList"
                            :key="index"
                            :label="item.key"
                            :disabled="!item.is_open"
                            >{{ item.name }}</el-radio
                        >
                    </el-radio-group>

                    <div v-if="isChatGLM">
                        <span class="form-tips !text-[14px]"
                            >开通网址：https://open.bigmodel.cn/</span
                        >
                        <a
                            href="https://open.bigmodel.cn/"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2">前往开通</el-button>
                        </a>
                    </div>
                    <div v-if="isBAIDU">
                        <span class="form-tips !text-[14px]"
                            >开通网址：https://cloud.baidu.com/product/wenxinworkshop?track=jinggangwei</span
                        >
                        <a
                            href="https://cloud.baidu.com/product/wenxinworkshop?track=jinggangwei"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2">前往开通</el-button>
                        </a>
                    </div>
                    <div v-if="isXUNFEI">
                        <span class="form-tips !text-[14px]"
                            >开通网址：https://console.xfyun.cn</span
                        >
                        <a
                            href="https://console.xfyun.cn"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2">前往开通</el-button>
                        </a>
                    </div>
                    <div v-if="isApi2d">
                        <span class="form-tips !text-[14px]"
                            >开通网址：https://api2d.com/r/207827</span>
                        <a
                            href="https://api2d.com/r/207827"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2">前往开通</el-button>
                        </a>
                    </div>
                    <div v-if="isGemini">
                        <span class="form-tips !text-[14px]"
                        >开通网址：https://makersuite.google.com/app/apikey</span>
                        <a
                            href="https://makersuite.google.com/app/apikey"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2">前往开通</el-button>
                        </a>
                    </div>
                    <div v-if="isMiniMax">
                        <span class="form-tips !text-[14px]"
                        >开通网址：https://api.minimax.chat</span>
                        <a
                            href="https://api.minimax.chat"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2">前往开通</el-button>
                        </a>
                    </div>
                    <div v-if="isHunyuan">
                        <span class="form-tips !text-[14px]"
                        >开通网址：https://console.cloud.tencent.com/hunyuan</span>
                        <a
                            href="https://console.cloud.tencent.com/hunyuan"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2">前往开通</el-button>
                        </a>
                    </div>
                    <div v-if="isQwen">
                        <span class="form-tips !text-[14px]"
                        >开通网址：https://dashscope.console.aliyun.com/overview</span>
                        <a
                            href="https://dashscope.console.aliyun.com/overview"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2">前往开通</el-button>
                        </a>
                    </div>
                    <div v-if="isGpt">
                        <span class="form-tips !text-[14px]"
                        >开通网址：https://dashscope.console.aliyun.com/overview</span>
                        <a
                            href="https://dashscope.console.aliyun.com/overview"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <el-button type="primary" link class="ml-2">前往开通</el-button>
                        </a>
                    </div>
                </div>
            </el-form-item>
            <el-form-item label="模型" v-if="Object.keys(formData.model_list || []).length">
                <div>
                    <el-select class="w-[400px]" v-model="formData.model">
                        <el-option
                            v-for="(item, key) in formData.model_list"
                            :value="key"
                            :label="item"
                            :key="key"
                        ></el-option>
                    </el-select>
                </div>
            </el-form-item>
            <el-form-item label="参数设置" class="is-required">
                <div class="flex flex-wrap max-w-[500px]">
                    <div class="w-[190px] mr-[20px] mb-[20px]" v-if="formData.context_num !== ''">
                        <div class="flex items-center text-tx-regular text-xs">
                            <span class="mr-[4px] mt-[2px]">上下文总数</span>
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="生成文本的最大长度，取值范围为1~5之间的整数"
                                placement="top"
                            >
                                <el-icon size="16px"><QuestionFilled /></el-icon>
                            </el-tooltip>
                        </div>

                        <el-slider v-model="formData.context_num" :min="1" :max="5" />
                    </div>
                    <div class="w-[190px] mr-[20px] mb-[20px]" v-if="formData.n !== ''">
                        <div class="flex items-center text-tx-regular text-xs">
                            <span class="mr-[4px] mt-[2px]">回复条数</span>
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="为每个输入消息生成多个回复，取值范围为1~5之间的整数。"
                                placement="top"
                            >
                                <el-icon size="16px"><QuestionFilled /></el-icon>
                            </el-tooltip>
                        </div>

                        <el-slider v-model="formData.n" :min="1" :max="5" />
                    </div>
                    <div class="w-[190px] mr-[20px] mb-[20px]" v-if="formData.temperature !== ''">
                        <div class="flex items-center text-tx-regular text-xs">
                            <span class="mr-[4px] mt-[2px]">词汇属性</span>
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                :content="`用于控制生成文本的随机性，取值范围为${temperatureConfig.min}~${temperatureConfig.max}之间的浮点数，建议取值${temperatureConfig.default}左右。`"
                                placement="top"
                            >
                                <el-icon size="16px"><QuestionFilled /></el-icon>
                            </el-tooltip>
                        </div>

                        <el-slider
                            v-model="formData.temperature"
                            :min="temperatureConfig.min"
                            :max="temperatureConfig.max"
                            :step="0.1"
                        />
                    </div>

                    <div class="w-[190px] mr-[20px] mb-[20px]" v-if="formData.top_p !== ''">
                        <div class="flex items-center text-tx-regular text-xs">
                            <span class="mr-[4px] mt-[2px]">随机属性</span>
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="用于控制生成文本的多样性，取值范围为0~1之间的浮点数，建议取值0.9左右。"
                                placement="top"
                            >
                                <el-icon size="16px"><QuestionFilled /></el-icon>
                            </el-tooltip>
                        </div>
                        <el-slider v-model="formData.top_p" :min="0" :max="1" :step="0.1" />
                    </div>
                    <div
                        class="w-[190px] mr-[20px] mb-[20px]"
                        v-if="formData.presence_penalty !== ''"
                    >
                        <div class="flex items-center text-tx-regular text-xs">
                            <span class="mr-[4px] mt-[2px]">话题属性</span>
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="用于控制生成文本中是否出现给定的关键词，取值范围为0~1之间的浮点数，建议取值0.5左右。"
                                placement="top"
                            >
                                <el-icon size="16px"><QuestionFilled /></el-icon>
                            </el-tooltip>
                        </div>

                        <el-slider
                            v-model="formData.presence_penalty"
                            :step="0.1"
                            :min="0"
                            :max="1"
                        />
                    </div>
                    <!-- <div
                        class="w-[190px] mr-[20px] mb-[20px]"
                        v-if="formData.frequency_penalty !== ''"
                    >
                        <div class="flex items-center text-tx-regular text-xs">
                            <span class="mr-[4px] mt-[2px]">重复属性</span>
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="用于控制生成文本中重复的程度，取值范围为0~1之间的浮点数，建议取值0.5左右"
                                placement="top"
                            >
                                <el-icon size="16px"><QuestionFilled /></el-icon>
                            </el-tooltip>
                        </div>
                        <el-slider
                            v-model="formData.frequency_penalty"
                            :step="0.1"
                            :min="0"
                            :max="1"
                        />
                    </div> -->
                </div>
            </el-form-item>
            <el-form-item
                :label="getFieldKey(formData.key)"
                prop="agency_api"
                v-if="isGpt || isApi2d || isGemini || isAzure"
                :rules="[
                    {
                        required: isAzure,
                        message: '请输入',
                        trigger: 'change'
                    }
                ]"
            >
                <div>
                    <div class="flex">
                        <el-input
                            placeholder="请输入自定义API域名"
                            class="w-[400px]"
                            v-model="formData.agency_api"
                        ></el-input>
                    </div>

                    <div class="form-tips !text-[14px]" v-if="!isAzure">
                        反向代理API域名，不填写默认为：
                        <span v-if="isGemini">https://generativelanguage.googleapis.com</span>
                        <span v-if="isApi2d">https://openai.api2d.net</span>
                        <span v-if="isGpt">https://api.openai.com</span>
<!--                        <span v-if="isAzure">https://api.openai.com</span>-->
                    </div>
                </div>
            </el-form-item>
        </el-form>
    </div>
</template>

<script setup lang="ts">
import { getConfig, setConfig, getAiSetting } from '@/api/setting/AI'

const props = defineProps({
    popRef: {
        type: Object,
        default: {} as any
    }
})

//表单ref
const formRef = shallowRef()

const interfaceKey = ref('')

const allData = ref()

//表单数据
const formData = ref<any>({
    // key: '',
    // api_key: [''],
    // api_type: 'open_ai',
    // is_sensitive: 0,
    // // max_tokens: 150,
    // model: 'text-davinci-003',
    // new_user_give: '',
    // agency_api: '',
    // global_directives: '',
    // model_list: []
    // n: 0,
    // temperature: '',
    // context_num: '',
    // top_p: '',
    // presence_penalty: '',
    // frequency_penalty: '',
    // status: ''
})
//AI接口列表
const interfaceList = ref<any[]>([])
const isChatGLM = computed(() => formData.value.key?.includes('chatglm'))
const isXUNFEI = computed(() => formData.value.key?.includes('xinghuo'))
const isBAIDU = computed(() => formData.value.key === 'wenxin')
const isApi2d = computed(() => formData.value.key?.includes('api2d'))
const isGpt = computed(() => formData.value.key?.includes('gpt'))
const isGemini = computed(() => formData.value.key?.includes('gemini'))
const isHunyuan = computed(() => formData.value.key?.includes('hunyuan'))
const isQwen = computed(() => formData.value.key?.includes('qwen'))
const isAzure = computed(() => formData.value.key?.includes('azure'))
const isMiniMax = computed(() => formData.value.key?.includes('minimax'))


const temperatureConfig = computed(() => {
    switch (formData.value.key) {
        case 'hunyuan':
            return {
                min: 0,
                default: 1,
                max: 2
            }
        case 'qwen':
            return {
                min: 0,
                default: 1,
                max: 1.9
            }
        default:
            return {
                min: 0,
                default: 0.7,
                max: 1
            }
    }
})

//表单校验规则
const getFieldKey = computed(() => {
    return (val: string) => {
        switch (val) {
            case 'azure_gpt3.5':
                return 'Language APIs'
            case 'azure_gpt4.0':
                return 'Language APIs'
            default:
                return '自定义API域名'
        }
    }
})

//表单校验规则
const rules = {
    api_key: [
        {
            required: true,
            message: '请输入秘钥',
            trigger: ['blur']
        }
    ],
    api_type: [
        {
            required: true,
            message: '请选择',
            trigger: ['blur']
        }
    ]
}

//接口切换
const interChange = (value: any) => {
    formData.value = allData.value[value]
    formData.value.status = 1
}

/**
 * 获取初始化数据
 */
const getData = async () => {
    const { config_lists } = await getAiSetting()
    interfaceList.value = config_lists
    allData.value = config_lists
    Object.keys(config_lists).map((item) => {
        if (config_lists[item].status == 1) {
            interfaceKey.value = item
            formData.value = config_lists[item]
        }
    })
}

const submit = async () => {
    await formRef.value.validate()
    setConfig(formData.value)
    console.log('保存对话')
}

getData()

defineExpose({ submit })
</script>

<style scoped lang="scss"></style>
