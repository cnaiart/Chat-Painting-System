<template>
    <div>
        <el-form label-width="85px" label-position="left">
            <div class="mb-[18px] max-w-[400px]">
                <el-form-item label="标题名称" class="is-required">
                    <el-input
                        v-model="content.title"
                        placeholder="请输入标题名称"
                    />
                </el-form-item>
                <el-form-item label="副标题" class="is-required">
                    <el-input
                        v-model="content.subTitle"
                        placeholder="请输入副标题"
                    />
                </el-form-item>
                <div class="">
                    <div class="mb-[15px] pr-[12px] w-[85px]">
                        <span class="text-error">*</span>
                        <span class="text-tx-regular">奖励设置</span>
                    </div>
                    <Draggable
                        class="draggable"
                        v-model="content.data"
                        animation="300"
                        handle=".drag-move"
                    >
                        <template v-slot:item="{ element, index }">
                            <div class="flex items-center bg-fill-light w-full p-4 mb-4">
                                <material-picker
                                    v-model="element.image"
                                    upload-class="bg-body"
                                    exclude-domain
                                    size="60px"
                                >
                                </material-picker>
                                <div class="ml-6">
                                    <el-form-item label="默认名称">
                                        <el-input
                                            disabled
                                            v-model="element.name"
                                            placeholder="请输入默认名称"
                                        />
                                    </el-form-item>
                                    <el-form-item label="自定义">
                                        <el-input v-model="element.customName" placeholder="请输入自定义名称" />
                                    </el-form-item>
                                    <el-form-item label="是否显示" class="!mb-0">
                                        <div class="flex-1 flex items-center">
                                            <el-switch
                                                v-model="element.show"
                                                :active-value="1"
                                                :inactive-value="0"
                                            />
                                            <div class="drag-move cursor-move ml-auto">
                                                <icon name="el-icon-Rank" size="18" />
                                            </div>
                                        </div>
                                    </el-form-item>
                                </div>
                            </div>
                        </template>
                    </Draggable>
                </div>
            </div>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
import Draggable from 'vuedraggable'
type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})
</script>

<style lang="scss" scoped></style>
