<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="是否显示">
                <el-radio-group v-model="isShowModel" class="ml-4">
                    <el-radio :label="true">显示</el-radio>
                    <el-radio :label="false">不显示</el-radio>
                </el-radio-group>
            </el-form-item>
            <div>
                <div class="el-form-item__label w-[70px] mb-[10px]">引导设置</div>
                <div class="bg-fill-light w-full p-4 mb-4">
                    <el-form-item label="背景图片">
                        <div>
                            <material-picker
                                v-model="propModel.bgImage"
                                upload-class="bg-body"
                                size="100px"
                                :exclude-domain="true"
                            />

                            <div class="form-tips">建议尺寸：1920px*248px</div>
                        </div>
                    </el-form-item>
                    <el-form-item label="文案内容">
                        <el-input v-model="propModel.content" />
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
                </div>
            </div>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import type { Prop } from './config'
const props = defineProps<{
    isShow: boolean
    prop: Prop
}>()
const emit = defineEmits<{
    (event: 'update:prop', value: Prop): void
    (event: 'update:isShow', value: boolean): void
}>()
const propModel = useVModel(props, 'prop', emit)
const isShowModel = useVModel(props, 'isShow', emit)
</script>

<style lang="scss" scoped></style>
