<template>
    <div class="flex flex-col items-center sm:py-[80px] py-[30px] mx-[20px]">
        <h1 v-if="prop.title" class="font-medium sm:text-[45px] text-[30px]">
            {{ prop.title }}
        </h1>
        <p
            v-if="prop.desc"
            class="max-w-[850px] text-center text-lg sm:my-[40px] my-[20px]"
        >
            {{ prop.desc }}
        </p>
        <div>
            <div v-if="prop.isShowBtn">
                <NuxtLink
                    :to="getLink(prop.link)"
                    :target="
                        typeof getLink(prop.link) == 'string'
                            ? '_blank'
                            : '_self'
                    "
                >
                    <ElButton
                        type="primary"
                        class="enter-btn hover-to-right"
                        size="large"
                    >
                        {{ prop.btnText }}
                    </ElButton>
                </NuxtLink>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
const props = defineProps<{
    prop: any
}>()
const getLink = (item: any) => {
    if (item?.type == 'custom') {
        return item?.query?.url
    } else {
        return {
            path: item?.path,
            query: item?.query
        }
    }
}
</script>

<style lang="scss" scoped>
.enter-btn {
    --el-button-size: 60px;
    --el-font-size-base: 18px;
    background: linear-gradient(
        90deg,
        var(--gradient-1) 0%,
        var(--gradient-2) 100%
    );
    border: none;
    padding: 20px 50px;
    border-radius: 8px;
}
</style>
