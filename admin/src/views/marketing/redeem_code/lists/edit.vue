<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            title="生成卡密"
            :async="true"
            width="580px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :rules="rules" :model="formData" label-width="110px">
                <el-form-item label="卡密类型" prop="type">
                    <el-radio-group v-model="formData.type" @change="formData.relation_id = ''">
                        <el-radio :label="1">会员套餐</el-radio>
                        <el-radio :label="2">充值套餐</el-radio>
                        <el-radio :label="3">对话次数</el-radio>
                        <el-radio :label="4">绘画次数</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item v-if="formData.type == 1" label="会员套餐" prop="relation_id">
                    <el-select
                        class="w-[410px]"
                        placeholder="请选择"
                        v-model="formData.relation_id"
                    >
                        <el-option
                            v-for="(item, index) in packageList.member_pckge"
                            :key="index"
                            :value="index"
                            :label="item"
                        ></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item v-if="formData.type == 2" label="充值套餐" prop="relation_id">
                    <el-select
                        class="w-[410px]"
                        placeholder="请选择"
                        v-model="formData.relation_id"
                    >
                        <el-option
                            v-for="(item, index) in packageList.recharge_pckge"
                            :key="index"
                            :value="index"
                            :label="item"
                        ></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item v-if="formData.type == 3" label="对话次数" prop="chat_num">
                    <el-input
                        class="w-[410px]"
                        v-model="formData.chat_num"
                        placeholder="请输入对话次数"
                        :min="0"
                        :max="9999"
                    />
                </el-form-item>
                <el-form-item v-if="formData.type == 4" label="绘画次数" prop="draw_num">
                    <el-input
                        class="w-[410px]"
                        v-model="formData.draw_num"
                        placeholder="请输入绘画次数"
                        :min="0"
                        :max="9999"
                    />
                </el-form-item>
                <el-form-item label="卡密数量" prop="card_num">
                    <div>
                        <el-input
                            class="w-[410px]"
                            v-model="formData.card_num"
                            placeholder="请输入卡密数量"
                            :min="0"
                            :max="500"
                        />
                        <div class="form-tips">单次生成最多支持500张</div>
                    </div>
                </el-form-item>
                <el-form-item label="卡密生效时间" prop="valid_start_time">
                    <div class="w-[410px] flex">
                        <date-picker
                            v-model="formData.valid_start_time"
                            type="date"
                            placeholder="开始时间"
                            format="YYYY/MM/DD"
                            value-format="x"
                            :second="true"
                        />
                        <date-picker
                            v-model="formData.valid_end_time"
                            type="date"
                            placeholder="结束时间"
                            format="YYYY/MM/DD"
                            value-format="x"
                            :second="true"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="备注" prop="remark">
                    <el-input
                        class="w-[410px]"
                        v-model="formData.remark"
                        type="textarea"
                        :autosize="{ minRows: 4, maxRows: 6 }"
                        placeholder="请输入备注"
                        maxlength="200"
                        show-word-limit
                    />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import feedback from '@/utils/feedback'
import { cardcodePackageLists, cardcodeAdd } from '@/api/marketing/redeem_code'
import type { CardCodeFormType } from '@/api/marketing/redeem_code'

const emit = defineEmits(['success', 'close'])
//表单ref
const formRef = shallowRef<FormInstance>()
//弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>()
//套餐列表
const packageList: any = ref({
    member_pckge: {},
    recharge_pckge: {}
})

//表单数据
const formData: any = ref<CardCodeFormType>({
    type: 1,
    relation_id: '',
    card_num: '',
    valid_start_time: '',
    valid_end_time: '',
    remark: '',
    draw_num: '',
    chat_num: ''
})
//表单校验规则
const rules = {
    relation_id: [
        {
            required: true,
            message: '请选择套餐',
            trigger: ['blur']
        }
    ],
    chat_num: [
        {
            required: true,
            message: '请输入对话次数',
            trigger: ['blur']
        }
    ],
    draw_num: [
        {
            required: true,
            message: '请输入绘画次数',
            trigger: ['blur']
        }
    ],
    card_num: [
        {
            required: true,
            message: '请输入卡密数量',
            trigger: ['blur']
        }
    ],
    valid_start_time: [
        {
            required: true,
            message: '请选择生效时间',
            trigger: ['blur']
        }
    ]
}

//获取分类列表
const getPackageList = async () => {
    const data = await cardcodePackageLists()
    packageList.value = data
}

//提交表单
const handleSubmit = async () => {
    try {
        await formRef.value?.validate()
        await cardcodeAdd(formData.value)
        popupRef.value?.close()
        emit('success')
    } catch (error) {
        return error
    }
}

const handleClose = () => {
    emit('close')
}

const open = () => {
    popupRef.value?.open()
    getPackageList()
}

defineExpose({ open })
</script>
