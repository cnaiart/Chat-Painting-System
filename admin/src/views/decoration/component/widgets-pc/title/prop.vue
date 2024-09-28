<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="标题名称">
                <el-input v-model="propModel.title" />
            </el-form-item>
            <el-form-item label="简介描述">
                <el-input v-model="propModel.desc" type="textarea" :rows="10" resize="none" />
            </el-form-item>
            <el-form-item label="按钮显示">
                <el-radio-group v-model="propModel.isShowBtn">
                    <el-radio :label="true">显示</el-radio>
                    <el-radio :label="false">不显示</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="按钮显示">
                <el-input v-model="propModel.btnText" />
            </el-form-item>
            <el-form-item label="按钮链接">
                <link-picker v-model="propModel.link" type="pc" />
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import type { Prop } from './config'

const props = defineProps<{
    prop: Prop
}>()
const emit = defineEmits<{
    (event: 'update:prop', value: Prop): void
}>()
const propModel = useVModel(props, 'prop', emit)
</script>

<style lang="scss" scoped></style>
