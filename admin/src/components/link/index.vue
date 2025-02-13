<template>
    <div class="link flex">
        <el-menu
            :default-active="activeMenu"
            class="w-[160px] min-h-[350px] link-menu"
            @select="handleSelect"
        >
            <template v-for="(item, index) in menus" :key="index">
                <el-menu-item :index="item.type">
                    <span>{{ item.name }}</span>
                </el-menu-item>
            </template>
        </el-menu>
        <div class="flex-1 pl-4">
            <shop-pages
                v-model="activeLink"
                :type="type"
                :is-tab="isTab"
                v-if="LinkTypeEnum.SHOP_PAGES == activeMenu"
            />
            <custom-link v-model="activeLink" v-if="LinkTypeEnum.CUSTOM_LINK == activeMenu" />
        </div>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import { LinkTypeEnum, type Link } from '.'
import ShopPages from './shop-pages.vue'
import CustomLink from './custom-link.vue'

const props = defineProps({
    modelValue: {
        type: Object as PropType<Link>,
        required: true
    },
    type: {
        type: String as PropType<'mobile' | 'pc'>,
        default: 'mobile'
    },
    isTab: {
        type: Boolean,
        default: false
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()

const menus = ref([
    {
        name: '页面',
        type: LinkTypeEnum.SHOP_PAGES,
        link: {}
    },
    {
        name: '自定义链接',
        type: LinkTypeEnum.CUSTOM_LINK,
        link: {}
    }
])
// if (props.type == 'pc' || props.isTab) {
//     menus.value = menus.value.filter((item) => item.type !== LinkTypeEnum.CUSTOM_LINK)
// }

const activeLink = computed({
    get() {
        return menus.value.find((item) => item.type == activeMenu.value)?.link as Link
    },
    set(value) {
        menus.value.forEach((item) => {
            if (item.type == activeMenu.value) {
                item.link = value
            }
        })
    }
})

const activeMenu = ref<string>(LinkTypeEnum.SHOP_PAGES)

const handleSelect = (index: string) => {
    activeMenu.value = index
}

watch(activeLink, (value) => {
    if (!value.type) return
    emit('update:modelValue', value)
})

watch(
    () => props.modelValue,
    (value) => {
        activeMenu.value = value.type
        activeLink.value = value
    },
    {
        immediate: true
    }
)
</script>

<style lang="scss" scoped>
.link-menu {
    --el-menu-item-height: 40px;
    :deep(.el-menu-item) {
        border-color: transparent;
        &.is-active {
            border-right-width: 2px;
            border-color: var(--el-color-primary);
            background-color: var(--el-color-primary-light-9);
        }
    }
}
</style>
