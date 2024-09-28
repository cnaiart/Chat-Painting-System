<template>
    <div class="app">
        <div class="header">
            <div class="title">全部应用</div>
        </div>
        <div class="main px-[20px] mt-[20px]">
            <div class="menu-item text-center" v-for="(item, index) in showList" :key="index">
                <decoration-img
                    :src="item.image"
                    width="130px"
                    height="130px"
                    radius="8px"
                ></decoration-img>
                <div class="mt-[8px] font-medium text-xl">
                    {{ item.title }}
                </div>
                <div class="mt-[6px] text-sm text-[#999999] truncate">
                    {{ item.desc }}
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
import DecorationImg from '../../decoration-img.vue'

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

const showList = computed(() => {
    return props.content.data.filter((item: any) => item.is_show == 1) || []
})
</script>

<style lang="scss" scoped>
.app {
    min-height: 615px;
    background: linear-gradient(to right, #e8fdf8, #ffffff, #e5f7ff);
}

.header {
    padding: 20px;
    padding-bottom: 0;

    .title {
        display: inline-block;
        font-size: 20px;
        font-weight: bold;
        background: -webkit-linear-gradient(90deg, #19e8b7, #00abff);
        background: -moz-linear-gradient(90deg, #19e8b7, #00abff);
        background: -o-linear-gradient(90deg, #19e8b7, #00abff);
        background: linear-gradient(90deg, #19e8b7, #00abff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        -moz-background-clip: text;
        -moz-text-fill-color: transparent;
        -o-background-clip: text;
        -o-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
    }
}

.main {
    flex: 1;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding: 24 rpx;

    .menu-item {
        display: inline-block;
        margin-bottom: 10px;
        padding: 10px;
        width: 150px;
        //height: 380rpx;
        border-radius: 8px;
        background-color: #ffffff;
        box-shadow: 0 2px 5px #e3e3e3;
    }
}
</style>
