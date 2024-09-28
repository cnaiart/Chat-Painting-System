<template>
    <div class="decoration-pages flex">
        <div class="flex-1 min-w-[400px] h-full">
            <el-scrollbar>
                <div class="pc-app">
                    <div class="app-lists">
                        <el-scrollbar>
                            <div class="flex flex-wrap">
                                <div
                                    class="menu-item text-center mb-[10px]"
                                    v-for="(item, index) in showList"
                                    :key="index"
                                >
                                    <decoration-img
                                        :src="item.image"
                                        width="130px"
                                        height="130px"
                                    ></decoration-img>
                                    <div class="font-medium text-[15px] mt-[8px]">
                                        {{ item.title }}
                                    </div>
                                    <div class="mt-[4px] text-xs text-[#999999] truncate">
                                        {{ item.desc }}
                                    </div>
                                </div>
                            </div>
                        </el-scrollbar>
                    </div>
                </div>
            </el-scrollbar>
        </div>
        <div class="h-full bg-white w-[400px] ml-[16px]">
            <el-scrollbar>
                <div class="p-4">
                    <div
                        class="title flex items-center before:w-[3px] before:h-[14px] before:block before:bg-primary before:mr-2"
                    >
                        应用装修
                        <div class="ml-2 text-tx-secondary text-sm">移动端同步</div>
                    </div>

                    <el-form class="mt-4" label-width="85px">
                        <div class="mb-[18px] max-w-[400px]">
                            <Draggable
                                class="draggable"
                                v-model="pageData.data"
                                animation="300"
                                handle=".drag-move"
                            >
                                <template v-slot:item="{ element, index }">
                                    <del-wrap @close="handleDelete(index)" class="max-w-[400px]">
                                        <div class="bg-fill-light w-full p-4 mt-4">
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
                                                <el-input
                                                    v-model="element.desc"
                                                    placeholder="请输入应用描述"
                                                />
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
            </el-scrollbar>
        </div>
    </div>
    <footer-btns v-perms="['decorate.page/save']">
        <el-button type="primary" @click="setData">保存</el-button>
    </footer-btns>
</template>
<script lang="ts" setup name="decorationPc">
import Draggable from 'vuedraggable'
import { getDecoratePages, setDecoratePages } from '@/api/decoration'
import DecorationImg from './component/decoration-img.vue'
const pageData = ref<any>({
    data: []
})

const getData = async () => {
    const data = await getDecoratePages({ id: 7 })
    pageData.value = data
    pageData.value.data = JSON.parse(data.data)
}

const setData = async () => {
    await setDecoratePages({
        ...pageData.value,
        data: JSON.stringify(pageData.value.data)
    })
    getData()
}
const handleAdd = () => {
    pageData.value.data.push({
        image: '',
        pcLink: {},
        link: {},
        is_show: '1'
    })
}
const handleDelete = (index: number) => {
    pageData.value.data.splice(index, 1)
}
const showList = computed(() => {
    return pageData.value.data.filter((tab: any) => tab.is_show == 1) || []
})
getData()
</script>
<style lang="scss" scoped>
.decoration-pages {
    height: calc(100vh - var(--navbar-height) - 134px);
    .pc-app {
        width: calc(1920px / 2);
        height: calc(1080px / 2);
        background: url(./image/pc_app.png);
        background-size: cover;
        margin: 0 auto;
        position: relative;
        .app-lists {
            position: absolute;
            left: calc(190px / 2);
            top: calc(130px / 2);
            right: calc(20px / 2);
            bottom: calc(20px / 2);

            .menu-item {
                display: inline-block;
                margin-right: calc(30px / 2);
                padding: calc(20px / 2);
                width: calc(300px / 2);
                height: calc(400px / 2);
                border-radius: calc(16px / 2);
                background-color: #ffffff;
                box-shadow: 0 3px 10px #e3e3e3;
            }
        }
    }
}
</style>
