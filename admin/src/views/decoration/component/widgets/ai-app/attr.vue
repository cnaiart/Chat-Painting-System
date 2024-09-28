<template>
    <div>
        <el-form label-width="85px">
            <div class="mb-[18px] max-w-[400px]">
                <Draggable
                    class="draggable"
                    v-model="content.data"
                    animation="300"
                    handle=".drag-move"
                >
                    <template v-slot:item="{ element, index }">
                        <del-wrap @close="handleDelete(index)" class="max-w-[400px]">
                            <div class="bg-fill-light w-full p-4 mb-4">
                                <el-form-item label="应用封面">
                                    <material-picker
                                        v-model="element.image"
                                        upload-class="bg-body"
                                        exclude-domain
                                        size="100px"
                                    >
                                    </material-picker>
                                </el-form-item>
                                <el-form-item label="应用标题">
                                    <el-input
                                        v-model="element.title"
                                        placeholder="请输入应用标题"
                                    />
                                </el-form-item>
                                <el-form-item label="应用描述">
                                    <el-input v-model="element.desc" placeholder="请输入应用描述" />
                                </el-form-item>
                                <el-form-item label="pc链接">
                                    <link-picker type="pc" v-model="element.pcLink" />
                                </el-form-item>
                                <el-form-item label="移动端链接">
                                    <link-picker type="mobile" v-model="element.link" />
                                </el-form-item>
                                <el-form-item label="是否显示">
                                    <div class="flex-1 flex items-center">
                                        <el-switch
                                            v-model="element.is_show"
                                            active-value="1"
                                            inactive-value="0"
                                        />
                                        <div class="drag-move cursor-move ml-auto">
                                            <icon name="el-icon-Rank" size="18" />
                                        </div>
                                    </div>
                                </el-form-item>
                            </div>
                        </del-wrap>
                    </template>
                </Draggable>
                <div class="mt-[20px]">
                    <el-button type="primary" @click="handleAdd">添加</el-button>
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

const handleAdd = () => {
    props.content.data.push({
        image: '',
        pcLink: {},
        link: {},
        is_show: '1'
    })
}
const handleDelete = (index: number) => {
    props.content.data.splice(index, 1)
}
</script>

<style lang="scss" scoped></style>
