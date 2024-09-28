<template>
    <div>
        <div
            class="flex items-center justify-between mt-[15px] text-info"
            @click="changeArrow"
        >
            <div class="cursor-default">{{ title }}</div>
            <div
                class="transition-transform rotate-"
                :class="{ 'rotate-180': !arrowStatus }"
            >
                <!-- <Icon v-if="!arrowStatus" name="el-icon-ArrowDown"></Icon> -->
                <Icon name="el-icon-ArrowUp"></Icon>
            </div>
            <!-- <div class="transition-transform rotate-180">
                <Icon v-if="arrowStatus" name="el-icon-ArrowUp"></Icon>
            </div> -->
        </div>
        <Transition>
            <div
                v-if="arrowStatus"
                :style="{ 'max-height': length * 110 + 'px' }"
                class="dropDownList overflow-hidden"
            >
                <slot name="menu"></slot>
            </div>
        </Transition>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    title: {
        type: String,
        default: ''
    },
    length: {
        type: Number,
        default: 0
    }
})

const arrowStatus = ref(true)

const changeArrow = () => {
    if (arrowStatus.value == true) {
        arrowStatus.value = false
    } else {
        arrowStatus.value = true
    }
}
</script>

<style lang="scss" scoped>
.dropDownList {
}

.v-enter-from,
.v-leave-to {
    max-height: 0 !important;
}
.v-enter-active,
.v-leave-active {
    transition: all 0.3s linear;
}
</style>
