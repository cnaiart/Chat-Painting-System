<template>
    <el-card shadow="never" class="!border-none">
        <div class="text-xl font-medium mb-[20px]">分享设置</div>
        <el-form ref="ruleFormRef" :rules="rules" :model="pagerData" label-width="120px">
            <el-form-item label="功能状态">
                <el-switch v-model="pagerData.status" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item label="分享一次奖励" prop="rewards">
                <div>
                    <div class="flex">
                        <div>
                            <el-input placeholder="请输入" v-model="pagerData.rewards"></el-input>
                        </div>
                        <div class="ml-[10px]">条对话次数</div>
                    </div>
                    <div class="flex mt-[20px]">
                        <div>
                            <el-input
                                placeholder="请输入"
                                v-model="pagerData.draw_rewards"
                            ></el-input>
                        </div>
                        <div class="ml-[10px]">条绘画次数</div>
                    </div>
                </div>
            </el-form-item>
            <el-form-item label="每天最多分享" prop="max_share">
                <div class="flex">
                    <div>
                        <el-input placeholder="请输入" v-model="pagerData.max_share"></el-input>
                    </div>
                    <div class="ml-[10px]">次有奖励</div>
                </div>
            </el-form-item>
        </el-form>
    </el-card>
    <footer-btns>
        <el-button
            v-perms="['task.task_share/setConfig']"
            type="primary"
            @click="handleSubmit(ruleFormRef)"
        >
            保存
        </el-button>
    </footer-btns>
</template>
<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import { getShareconfig, editShareconfig } from '@/api/marketing/share'
import feedback from '@/utils/feedback'

interface pagerDataInter {
    status: number
    rewards: number
    draw_rewards: number
    max_share: number
}

//表单ref
const ruleFormRef = ref<FormInstance>()
const pagerData = ref<pagerDataInter>({
    status: 1,
    rewards: 1,
    draw_rewards: 1,
    max_share: 5
})

//表单校验规则
const rules = reactive<FormRules>({
    max_share: [{ required: true, message: '请输入每天最多分享几次数有奖励', trigger: 'blur' }]
})

/**
 * 初始化数据
 */
const getData = async () => {
    pagerData.value = await getShareconfig()
}
getData()
/**
 * 提交数据
 */
const handleSubmit = async (formEl: FormInstance | undefined) => {
    if (!formEl) {
        console.log(formEl)
        return
    }
    try {
        await formEl.validate()
        if (pagerData.value.rewards > 0 || pagerData.value.draw_rewards > 0) {
            await editShareconfig(pagerData.value)
            await getData()
        } else {
            feedback.msgError('对话次数和绘画次数必须有一个大于0')
        }
    } catch (error) {
        console.log(error)
    }
}
</script>
