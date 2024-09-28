<template>
    <div class="price-container">
        <div
            :class="['price-wrap', { 'price-wrap--disabled': lineThrough }]"
            :style="{ color: color }"
        >
            <!-- Prefix -->
            <div class="fix-pre" :style="{ fontSize: minorSize }">
                <slot name="prefix">{{ prefix }}</slot>
            </div>

            <!-- Content -->
            <div :style="{ 'font-weight': fontWeight }">
                <!-- Integer -->
                <span :style="{ fontSize: mainSize }">{{ integer }}</span>
                <!-- Decimals -->
                <span :style="{ fontSize: minorSize }">{{ decimals }}</span>
            </div>

            <!-- Suffix -->
            <div class="fix-suf" :style="{ fontSize: minorSize }">
                <slot name="suffix">{{ suffix }}</slot>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
/**
 * @description 价格展示，适用于有前后缀，小数样式不一
 * @property {String|Number} content 价格 (必填项)
 * @property {Number} prec 小数位 (默认: 2)
 * @property {Boolean} autoPrec 自动小数位【注：以prec为最大小数位】 (默认: true)
 * @property {String} color 颜色 (默认: 'unset')
 * @property {String} mainSize 主要内容字体大小 (默认: 46rpx)
 * @property {String} minorSize 主要内容字体大小 (默认: 32rpx)
 * @property {Boolean} lineThrough 贯穿线 (默认: false)
 * @property {String|Number} fontWeight 字重 (默认: normal)
 * @property {String} prefix 前缀 (默认: ￥)
 * @property {String} suffix 后缀
 * @example <price content="100" suffix="\/元" />
 */
import { computed } from 'vue'
import { formatPrice } from '@/utils/util'
/** Props Start **/
const props = withDefaults(
    defineProps<{
        content: string | number // 标题
        prec?: number // 小数数量
        autoPrec?: boolean // 动态小数
        color?: string // 颜色
        mainSize?: string // 主要内容字体大小
        minorSize?: string // 次要内容字体大小
        lineThrough?: boolean // 贯穿线
        fontWeight?: string // 字重
        prefix?: string // 前缀
        suffix?: string // 后缀
    }>(),
    {
        content: '',
        prec: 2,
        autoPrec: true,
        color: 'inherit',
        mainSize: '18px',
        minorSize: '14px',
        lineThrough: false,
        fontWeight: 'normal',
        prefix: '￥',
        suffix: ''
    }
)
/** Props End **/

/** Computed Start **/
/**
 * @description 金额主体部分
 */
const integer = computed(() => {
    return formatPrice({
        price: props.content,
        take: 'int'
    })
})
/**
 * @description 金额小数部分
 */
const decimals = computed(() => {
    let decimals: any = formatPrice({
        price: props.content,
        take: 'dec',
        prec: props.prec
    })
    // 小数余十不能是 .10||.20||.30以此类推，
    decimals =
        decimals % 10 === 0 ? decimals.substr(0, decimals.length - 1) : decimals
    return props.autoPrec
        ? decimals * 1
            ? `.${decimals}`
            : ''
        : props.prec
        ? `.${decimals}`
        : ''
})
/** Computed End **/
</script>

<style lang="scss" scoped>
.price-container {
    display: inline-block;
}

.price-wrap {
    display: flex;
    align-items: baseline;
    &--disabled {
        position: relative;

        &::before {
            position: absolute;
            left: 0;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            display: block;
            content: '';
            height: 0.05em;
            background-color: currentColor;
        }
    }
}
</style>
