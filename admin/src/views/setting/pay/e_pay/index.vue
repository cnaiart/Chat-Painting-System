<template>
    <div class="">
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="warning"
                title="温馨提示：要先前往申请易支付账号才能获取对应的参数"
                :closable="false"
                show-icon
            ></el-alert>
        </el-card>
        <el-form ref="formRef" class="ls-form" :model="formData" :rules="formRules" label-width="120px">
            <el-card shadow="never" class="!border-none mt-4">
                <el-form-item label="接口地址" prop="epay_url">
                    <div>
                        <el-input
                            v-model.trim="formData.epay_url"
                            placeholder="请输入"
                        />
                        <div class="form-tips">
                            易支付申请地址：https://yi-pay.com/user/certificate.php
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="商户ID" prop="epay_pid">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.epay_pid"
                            placeholder="请输入商户ID"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="商户密钥" prop="epay_key">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.epay_key"
                            placeholder="请输入商户密钥"
                        />
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
    </div>
    <footer-btns v-perms="['setting.pay.pay_config/setEpayConfig']">
        <el-button type="primary" @click="handleSubmit">保存</el-button>
    </footer-btns>
</template>
<script setup lang="ts">
import { getEpayConfig, setEpayConfig } from '@/api/setting/pay'
import type { FormInstance } from 'element-plus'

interface formDataInter {
    epay_url: string
    epay_pid: string
    epay_key: string
}

const formRef = shallowRef<FormInstance>()
const formData = ref<formDataInter>({
    epay_url: '',
    epay_pid: '',
    epay_key: ''
})

const formRules = {
    epay_url: [
        {
            required: true,
            message: '请输入接口地址',
            trigger: 'blur'
        }
    ],
    epay_pid: [
        {
            required: true,
            message: '请输入商户ID',
            trigger: 'blur'
        }
    ],
    epay_key: [
        {
            required: true,
            message: '请输入商户密钥',
            trigger: 'blur'
        }
    ]
}

/**
 * 初始化数据
 */
const getData = async () => {
    formData.value = await getEpayConfig()
}
getData()
/**
 * 保存数据
 */
const handleSubmit = async () => {
    await formRef.value?.validate()
    await setEpayConfig(formData.value)
    getData()
}
</script>
