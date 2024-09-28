<template>
    <div>
        <el-form label-width="120px" ref="formDataRef" :rules="rules" :model="formData">
            <el-form-item label="功能状态">
                <div>
                    <el-switch v-model="isOpen" :active-value="1" :inactive-value="0"></el-switch>
                    <div class="form-tips">默认关闭；功能关闭后，用户在前台将无法访问该功能</div>
                </div>
            </el-form-item>
            <template v-if="isOpen == 1">
                <el-form-item label="AI接口" required>
                    <div>
                        <el-radio-group @change="selectType" v-model="isSelectType">
                            <el-radio
                                v-for="(item, index) in configList"
                                :key="index"
                                :label="item.type"
                                :disabled="!item.is_open"
                                >{{ item.name }}</el-radio
                            >
                            <!-- <el-radio label="zhishuyun">知数云</el-radio> -->
                        </el-radio-group>
                        <div class="flex items-center" v-if="isSelectType == 'zhishuyun_qrcode'">
                            <div class="form-tips">
                                如果您已开通知数云的接口，可直接填写；如果未开通请
                            </div>
                            <a
                                href="https://auth.zhishuyun.com/auth/login?inviter_id=c7ff8573-940e-4dd0-828c-91adeda5f5dd&redirect=https://data.zhishuyun.com"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <el-button class="ml-2" type="primary" link>前往开通</el-button>
                            </a>
                        </div>
                        <div class="flex items-center" v-if="isSelectType == 'mewx'">
                            <div class="form-tips">
                                如果您已开通星月熊的接口，可直接填写；如果未开通请
                            </div>
                            <a
                                href="https://qr.mewx.art/"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <el-button class="ml-2" type="primary" link>前往开通</el-button>
                            </a>
                        </div>
                    </div>
                </el-form-item>
                <template v-if="isSelectType == 'mewx'">
                    <el-form-item label="支持版本" prop="version">
                        <div>
                            <el-checkbox-group v-model="formData.version">
                                <el-checkbox label="4">v3</el-checkbox>
                                <el-checkbox label="3">v2</el-checkbox>
                                <el-checkbox label="2">v1.1</el-checkbox>
                                <el-checkbox label="1">v1</el-checkbox>
                            </el-checkbox-group>
                            <div class="form-tips">v2、v3消耗的费用比较高，请合理设置</div>
                        </div>
                    </el-form-item>
                    <el-form-item label="自定义API域名">
                        <div>
                            <div class="flex">
                                <el-input
                                    placeholder="请输入自定义API域名(选填)"
                                    class="w-[400px]"
                                    v-model="formData.proxy_url"
                                ></el-input>
                            </div>

                            <div class="form-tips">不填写默认为：https://open-qr.mewx.art</div>
                        </div>
                    </el-form-item>
                </template>
            </template>
        </el-form>
    </div>
</template>

<script setup lang="ts">
import { getAIModelConfig, setAIModelConfig } from '@/api/setting/AI'
import register from '@/assets/images/QR_code_open.png'

const configList: any = ref({})

const isSelectType = ref('mewx')

const isOpen = ref(1)

//表单ref
const formDataRef = shallowRef()

const formData: any = ref({
    // is_open: 1, // 1-开启 0-关闭
    // type: '', //mewx-星月熊  zhishuyun-知数云
    // status: '', //0-关闭 1-开启
    // version: [],
    // proxy_url: ''
})

const rules = {
    version: [
        {
            required: true,
            message: '请至少选择一个支持版本',
            trigger: ['blur']
        }
    ]
}

const selectType = async (value: any) => {
    await nextTick()
    formData.value = configList.value[isSelectType.value]
    formData.value.status = 1
    // console.log(formData.value)
}

const getConfig = async () => {
    const res = await getAIModelConfig()
    configList.value = res.config_lists
    isOpen.value = res.is_open
    Object.keys(configList.value).map((item) => {
        if (configList.value[item].status == 1) {
            isSelectType.value = configList.value[item].type
            formData.value = configList.value[item]
        }
    })
}

//提交
const submit = async () => {
    await formDataRef.value.validate()
    await setAIModelConfig({ ...formData.value, is_open: isOpen.value })
    getConfig()
}

getConfig()

defineExpose({
    submit
})
</script>

<style scoped lang="scss"></style>
