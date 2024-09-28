<!-- 网站信息 -->
<template>
    <div class="user-setup">
        <el-form ref="formRef" label-width="120px">
            <el-card shadow="never" class="!border-none mb-4">
                <div class="font-medium mb-4">语音播报配置</div>

                <el-form-item label="是否开启">
                    <div>
                        <el-switch
                            v-model="formData.voice_broadcast.is_open"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">开启/关闭语音播报功能，默认关闭</div>
                    </div>
                </el-form-item>

                <el-form-item label="语音通道" required>
                    <div>
                        <el-radio-group v-model="formData.voice_broadcast.channel">
                            <el-radio
                                v-for="(value, key) in formData.voice_broadcast.channel_config"
                                :key="key"
                                :label="key"
                            >
                                {{ value.name }}
                            </el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            <div class="flex items-center leading-none">
                                为确保功能正常使用，请先前往【Key池管理】-【语音播报】添加Key
                                <router-link
                                    :to="{
                                        path: getRoutePath('setting.KeyPool/lists')
                                    }"
                                >
                                    <el-button link type="primary">前往设置</el-button>
                                </router-link>
                            </div>
                        </div>
                        <template v-if="formData.voice_broadcast.channel === 'kdxf'">
                            <div class="form-tips">
                                <div class="flex items-center leading-none">
                                    <div>如果您已开通，可直接使用；如果未开通，点击</div>
                                    <a
                                        href="https://console.xfyun.cn/services/tts"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <el-button link type="primary"> 前往开通 </el-button>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </el-form-item>
                <template v-if="formData.voice_broadcast.channel === 'openai'">
                    <el-form-item label="语音模型" v-if="formData.voice_broadcast.channel">
                        <el-select
                            class="w-[280px]"
                            v-model="
                                formData.voice_broadcast.channel_config[
                                    formData.voice_broadcast.channel
                                ].model
                            "
                        >
                            <el-option
                                v-for="(i, key) in formData.voice_broadcast.channel_config[
                                    formData.voice_broadcast.channel
                                ].model_list"
                                :key="key"
                                :label="i"
                                :value="key"
                            />
                        </el-select>
                    </el-form-item>
                </template>
                <el-form-item label="发音人类型">
                    <el-radio> 基础发音人 </el-radio>
                </el-form-item>
                <el-form-item label="发音人" v-if="formData.voice_broadcast.channel">
                    <el-select
                        class="w-[280px]"
                        v-model="
                            formData.voice_broadcast.channel_config[
                                formData.voice_broadcast.channel
                            ].pronounce
                        "
                    >
                        <el-option
                            v-for="(i, key) in formData.voice_broadcast.channel_config[
                                formData.voice_broadcast.channel
                            ].pronounce_list"
                            :key="key"
                            :label="i"
                            :value="key"
                        />
                    </el-select>
                </el-form-item>
                <template v-if="formData.voice_broadcast.channel === 'openai'">
                    <el-form-item label="发音语速" v-if="formData.voice_broadcast.channel">
                        <div class="w-[280px]">
                            <el-slider
                                v-model="
                                    formData.voice_broadcast.channel_config[
                                        formData.voice_broadcast.channel
                                    ].speed
                                "
                                :min="0.25"
                                :max="4"
                                :step="0.01"
                            />
                            <div class="form-tips">可选0.25-4.0，默认1</div>
                        </div>
                    </el-form-item>
                    <el-form-item label="自定义API域名" v-if="formData.voice_broadcast.channel">
                        <div class="w-[280px]">
                            <el-input
                                placeholder="自定义API域名"
                                v-model="
                                    formData.voice_broadcast.channel_config[
                                        formData.voice_broadcast.channel
                                    ].agency_api
                                "
                            />
                            <div class="form-tips">
                                反向代理API域名，不填写默认为： https://api.openai.com
                            </div>
                        </div>
                    </el-form-item>
                </template>
                <template v-else>
                    <el-form-item label="发音语速" v-if="formData.voice_broadcast.channel">
                        <div class="w-[280px]">
                            <el-slider
                                v-model="
                                    formData.voice_broadcast.channel_config[
                                        formData.voice_broadcast.channel
                                    ].speed
                                "
                                :min="0"
                                :max="100"
                                :step="1"
                            />
                            <div class="form-tips">可选0-100，默认为50</div>
                        </div>
                    </el-form-item>
                </template>
            </el-card>
            <el-card shadow="never" class="!border-none mb-4">
                <div class="font-medium mb-4">语音输入配置</div>
                <el-form-item label="是否开启">
                    <div>
                        <el-switch
                            v-model="formData.voice_input.is_open"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">开启/关闭语音输入功能，默认关闭</div>
                    </div>
                </el-form-item>

                <el-form-item label="语音通道" required>
                    <div>
                        <el-radio-group v-model="formData.voice_input.channel">
                            <el-radio
                                v-for="(value, key) in formData.voice_input.channel_config"
                                :key="key"
                                :label="key"
                            >
                                {{ value.name }}
                            </el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            <div class="flex items-center leading-none">
                                为确保功能正常使用，请先前往【Key池管理】-【语音输入】添加Key
                                <router-link
                                    :to="{
                                        path: getRoutePath('setting.KeyPool/lists')
                                    }"
                                >
                                    <el-button link type="primary">前往设置</el-button>
                                </router-link>
                            </div>
                        </div>
                        <template v-if="formData.voice_input.channel === 'kdxf'">
                            <div class="form-tips">
                                <div class="flex items-center leading-none">
                                    <div>如果您已开通，可直接使用；如果未开通，点击</div>
                                    <a
                                        href="https://console.xfyun.cn/services/tts"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <el-button link type="primary"> 前往开通 </el-button>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </el-form-item>
                <template v-if="formData.voice_input.channel === 'openai'">
                    <el-form-item label="语音模型" v-if="formData.voice_input.channel">
                        <el-select
                            class="w-[280px]"
                            v-model="
                                formData.voice_input.channel_config[formData.voice_input.channel]
                                    .model
                            "
                        >
                            <el-option
                                v-for="(i, key) in formData.voice_input.channel_config[
                                    formData.voice_input.channel
                                ].model_list"
                                :key="key"
                                :label="i"
                                :value="key"
                            />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="自定义API域名" v-if="formData.voice_input.channel">
                        <div class="w-[280px]">
                            <el-input
                                placeholder="自定义API域名"
                                v-model="
                                    formData.voice_input.channel_config[
                                        formData.voice_input.channel
                                    ].agency_api
                                "
                            />
                            <div class="form-tips">
                                反向代理API域名，不填写默认为： https://api.openai.com
                            </div>
                        </div>
                    </el-form-item>
                </template>
            </el-card>
            <el-card shadow="never" class="!border-none mb-4">
                <div class="font-medium mb-4">语音对话配置</div>
                <el-form-item label-width="0">
                    <el-alert
                        type="warning"
                        title="温馨提示：语音对话需要用到【语音识别】和【语音合成】服务，语音合成服务可复用【语音播报】的key，语音识别服务可以复用【语音输入】的key，如使用的话，需要前往配置语音播报和语音输入的key才可正常使用！"
                        :closable="false"
                        show-icon
                    ></el-alert>
                </el-form-item>
                <el-form-item label="是否开启">
                    <div>
                        <el-switch
                            v-model="formData.voice_chat.is_open"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">开启/关闭语音对话功能，默认关闭</div>
                    </div>
                </el-form-item>

                <el-form-item label="语音通道" required>
                    <div>
                        <el-radio-group v-model="formData.voice_chat.channel">
                            <el-radio
                                v-for="(value, key) in formData.voice_chat.channel_config"
                                :key="key"
                                :label="key"
                            >
                                {{ value.name }}
                            </el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            <div class="flex items-center leading-none">
                                为确保功能正常使用，请先前往【Key池管理】-语音播报】和【语音输入】
                                添加Key
                                <router-link
                                    :to="{
                                        path: getRoutePath('setting.KeyPool/lists')
                                    }"
                                >
                                    <el-button link type="primary">前往设置</el-button>
                                </router-link>
                            </div>
                        </div>
                        <template v-if="formData.voice_chat.channel === 'kdxf'">
                            <div class="form-tips">
                                <div class="flex items-center leading-none">
                                    <div>如果您已开通，可直接使用；如果未开通，点击</div>
                                    <a
                                        href="https://console.xfyun.cn/services/tts"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <el-button link type="primary"> 前往开通 </el-button>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </el-form-item>
                <template v-if="formData.voice_chat.channel === 'openai'">
                    <el-form-item label="语音模型" v-if="formData.voice_chat.channel">
                        <el-select
                            class="w-[280px]"
                            v-model="
                                formData.voice_chat.channel_config[formData.voice_chat.channel]
                                    .model
                            "
                        >
                            <el-option
                                v-for="(i, key) in formData.voice_chat.channel_config[
                                    formData.voice_chat.channel
                                ].model_list"
                                :key="key"
                                :label="i"
                                :value="key"
                            />
                        </el-select>
                    </el-form-item>
                </template>
                <el-form-item label="发音人类型">
                    <el-radio> 基础发音人 </el-radio>
                </el-form-item>
                <el-form-item label="发音人" v-if="formData.voice_chat.channel">
                    <el-select
                        class="w-[280px]"
                        v-model="
                            formData.voice_chat.channel_config[formData.voice_chat.channel]
                                .pronounce
                        "
                    >
                        <el-option
                            v-for="(i, key) in formData.voice_chat.channel_config[
                                formData.voice_chat.channel
                            ].pronounce_list"
                            :key="key"
                            :label="i"
                            :value="key"
                        />
                    </el-select>
                </el-form-item>
                <template v-if="formData.voice_chat.channel === 'openai'">
                    <el-form-item label="发音语速" v-if="formData.voice_chat.channel">
                        <div class="w-[280px]">
                            <el-slider
                                v-model="
                                    formData.voice_chat.channel_config[formData.voice_chat.channel]
                                        .speed
                                "
                                :min="0.25"
                                :max="4"
                                :step="0.01"
                            />
                            <div class="form-tips">可选0.25-4.0，默认1</div>
                        </div>
                    </el-form-item>
                    <el-form-item label="自定义API域名" v-if="formData.voice_chat.channel">
                        <div class="w-[280px]">
                            <el-input
                                placeholder="自定义API域名"
                                v-model="
                                    formData.voice_chat.channel_config[formData.voice_chat.channel]
                                        .agency_api
                                "
                            />
                            <div class="form-tips">
                                反向代理API域名，不填写默认为： https://api.openai.com
                            </div>
                        </div>
                    </el-form-item>
                </template>
                <template v-else>
                    <el-form-item label="发音语速" v-if="formData.voice_chat.channel">
                        <div class="w-[280px]">
                            <el-slider
                                v-model="
                                    formData.voice_chat.channel_config[formData.voice_chat.channel]
                                        .speed
                                "
                                :min="0"
                                :max="100"
                                :step="1"
                            />
                            <div class="form-tips">可选0-100，默认为50</div>
                        </div>
                    </el-form-item>
                </template>
            </el-card>
        </el-form>
        <footer-btns v-perms="['setting.VoiceSetting/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="voiceConfig">
import { getVoiceConfig, setVoiceConfig } from '@/api/setting/voice_config'
import { getRoutePath } from '@/router'

const configs = ref([
    {
        title: '语音播报配置',
        key: 'voice_broadcast',
        tips: '【语音播报】'
    },
    {
        title: '语音输入配置',
        key: 'voice_input',
        tips: '【语音输入】'
    },
    {
        title: '语音对话配置',
        key: 'voice_chat',
        tips: '【语音播报】和【语音输入】',
        openai: '【语音对话】'
    }
])

const formData = reactive<any>({
    voice_broadcast: {
        channel_config: {}
    },
    voice_input: {
        channel_config: {}
    },
    voice_chat: {
        channel_config: {}
    }
})

const getData = async () => {
    try {
        const data = await getVoiceConfig()
        for (const key in formData) {
            //@ts-ignore
            formData[key] = data[key]
        }
    } catch (error) {
        console.log('获取=>', error)
    }
}

// 保存用户设置数据
const handleSubmit = async () => {
    try {
        await setVoiceConfig(formData)
        getData()
    } catch (error) {
        console.log('保存=>', error)
    }
}

getData()
</script>

<style lang="scss" scoped></style>
