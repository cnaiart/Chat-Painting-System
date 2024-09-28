<template>
    <ElDialog
        v-model="popShow"
        width="500px"
        title="申请成为分销商"
        :close-on-click-modal="false"
        @close="closePop"
    >
        <div>
            <el-form ref="ruleFormRef" :rules="rules" :model="params"  label-width="120px">
                <el-form-item label="真实名称" prop="name">
                    <div class="w-[280px]">
                        <el-input
                            v-model="params.name"
                            placeholder="请输入名称"
                        ></el-input>
                    </div>
                </el-form-item>
                <el-form-item label="联系方式" prop="mobile">
                    <div class="w-[280px]">
                        <el-input
                            v-model="params.mobile"
                            placeholder="请输入手机号码"
                        ></el-input>
                    </div>
                </el-form-item>
                <el-form-item>
                    <el-checkbox
                        :true-label="1"
                        :false-label="0"
                        v-model="isRead"
                    >
                        <div class="text-[12px]">
                            <span class="text-[#999999]">阅读并同意</span>
                            <NuxtLink to="/policy/distribution">
                                <span class="text-[#23B571]"
                                    >《用户分销协议》</span
                                >
                            </NuxtLink>
                        </div>
                    </el-checkbox>
                </el-form-item>
            </el-form>
        </div>
        <template #footer>
            <div>
                <el-button @click="closePop">取消</el-button>
                <el-button type="primary" @click="handleSub(ruleFormRef)"
                    >提交申请
                </el-button>
            </div>
        </template>
    </ElDialog>
</template>

<script setup lang="ts">
import {
    ElDialog,
    ElForm,
    ElFormItem,
    ElInput,
    ElCheckbox,
    ElButton
} from 'element-plus'
import { applyDistribution } from '@/api/promotion'
//弹框显示/隐藏
const popShow = ref(false)

const emit = defineEmits(['closePop'])

//表单ref
const ruleFormRef = ref()
const params = ref({
    name: '',
    mobile: ''
})

const isRead = ref(0)


//表单校验规则
const rules = reactive({
    name: [{ required: true, message: '请输入真实名称', trigger: 'blur' }],
    mobile: [{ required: true, message: '请输入手机号码', trigger: 'blur' }]
})

//打开弹框
const open = () => {
    popShow.value = true
}

//提交申请
const handleSub = async (formEl) => {
    if (!formEl) {
        console.log(formEl)
        return
    }
    await formEl.validate()
    if (isRead.value == 1) {
        await applyDistribution(params.value)
        closePop()
    } else {
        feedback.msgError('请先阅读并同意《用户分销协议》！')
    }
}

//关闭弹框
const closePop = () => {
    popShow.value = false
    emit('closePop')
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
