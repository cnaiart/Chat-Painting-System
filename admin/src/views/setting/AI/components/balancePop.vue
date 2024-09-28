<template>
    <popup ref="popRef" title="查询余额">
        <el-form label-width="90" v-loading="loding">
            <el-form-item label="余额数量">${{ formData.hard_limit_usd }}</el-form-item>
            <el-form-item label="已使用数量">${{ formData.total_usage }}</el-form-item>
            <el-form-item label="剩余数量">${{ formData.surplus }}</el-form-item>
            <el-form-item label="到期时间">{{ formData.access_until }}</el-form-item>
        </el-form>
    </popup>
</template>

<script setup lang="ts">
import popup from '@/components/popup/index.vue'
import { checkBalance } from '@/api/setting/AI'

const loding = ref(false)
//弹框ref
const popRef = shallowRef()
//表单数据
const formData = ref({
    hard_limit_usd: '', //余额数量
    access_until: '', //订阅到期时间
    total_usage: '', //已使用数量
    surplus: '' //剩余数量
})

const getData = async (key: any) => {
    loding.value = true
    formData.value = await checkBalance({ ai_api_key: key })
    loding.value = false
}

//打开弹框
const open = (key: any) => {
    popRef.value.open()
    getData(key)
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
