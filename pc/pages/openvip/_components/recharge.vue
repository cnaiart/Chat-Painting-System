<template>
    <div class="h-full rounded-[8px] bg-white">
        <ElScrollbar>
            <div class="">
                <div>
                    <div class="mx-[30px] pt-[80px] pb-[60px]">
                        <!-- @vue-ignore -->
                        <swiper
                            :slidesPerView="'auto'"
                            :spaceBetween="30"
                            :navigation="true"
                            class="recharge-package max-w-[1050px]"
                            :modules="[Navigation]"
                        >
                            <swiper-slide
                                v-for="(item, index) in data.lists"
                                :key="item.id"
                                style="width: 240px; height: auto"
                            >
                                <div
                                    class="recharge-package-item flex flex-col relative"
                                    :class="{ actived: selectval == index }"
                                    :key="item.id"
                                    @click="handleselected(index, item)"
                                >
                                    <div class="pt-[60%] relative w-full h-0">
                                        <div
                                            class="absolute left-0 right-0 bottom-0 top-0"
                                        >
                                            <ElImage
                                                :src="item.image"
                                                class="h-full w-full"
                                                fit="cover"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="absolute top-[-1px] left-[-1px] bg-[#FF3232] text-white text-sm rounded-[4px] px-[10px] py-[4px] max-w-[100%]"
                                        v-if="item.tag"
                                    >
                                        {{ item.tag }}
                                    </div>
                                    <div
                                        class="p-[15px] flex-1 min-h-0 flex flex-col"
                                    >
                                        <div>
                                            <Price
                                                :content="item.sell_price"
                                                color="#FF3232"
                                                font-weight="500"
                                                main-size="20px"
                                                minor-size="14px"
                                            />
                                        </div>
                                        <div
                                            class="text-xl font-medium mt-[6px] line-clamp-1"
                                        >
                                            {{ item.name }}
                                        </div>
                                        <div
                                            class="text-sm text-tx-secondary mt-[10px] line-clamp-3"
                                        >
                                            {{ item.describe }}
                                        </div>
                                        <div class="mt-auto">
                                            <div
                                                class="flex flex-wrap justify-between mt-[10px] text-sm"
                                            >
                                                <div
                                                    class="mr-[10px] flex-none"
                                                >
                                                    对话次数: {{ item.number }}
                                                </div>
                                                <div
                                                    class="text-primary"
                                                    v-if="
                                                        item.is_give &&
                                                        item.give_number
                                                    "
                                                >
                                                    +{{ item.give_number }}
                                                </div>
                                            </div>
                                            <div
                                                class="flex flex-wrap justify-between mt-[6px] text-sm"
                                            >
                                                <div
                                                    class="mr-[10px] flex-none"
                                                >
                                                    绘画次数:
                                                    {{ item.draw_number }}
                                                </div>
                                                <div
                                                    class="text-primary"
                                                    v-if="
                                                        item.is_give &&
                                                        item.give_draw_number
                                                    "
                                                >
                                                    +{{ item.give_draw_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </swiper-slide>
                        </swiper>
                    </div>
                    <div class="p-[20px] text-white text-center">
                        <ElButton
                            type="primary"
                            size="large"
                            class="!px-[80px] !rounded-lg"
                            @click="handlePay"
                        >
                            <template #default> 立即充值 </template>
                        </ElButton>
                        <div class="mt-[10px] text-sm text-tx-secondary">
                            该服务为虚拟产品，支付成功后不支持退款。
                        </div>
                    </div>
                </div>
            </div>
        </ElScrollbar>

        <Payment
            ref="PaymentRef"
            type="recharge"
            redirect="/openvip?mode=recharge"
        >
            <template
                v-slot:top="{
                    name,
                    number,
                    give_number,
                    is_give,
                    give_draw_number,
                    draw_number
                }"
            >
                <ElFormItem label="套餐名称">
                    <div>{{ name }}</div>
                </ElFormItem>
                <ElFormItem label="套餐内容">
                    <div>
                        <div>
                            对话次数: {{ number }}
                            <span v-if="is_give && give_number">
                                (赠送{{ give_number }}次)
                            </span>
                        </div>
                        <div>
                            绘画次数: {{ draw_number }}
                            <span v-if="is_give && give_draw_number">
                                (赠送{{ give_draw_number }}次)
                            </span>
                        </div>
                    </div>
                </ElFormItem>
            </template>
        </Payment>
    </div>
</template>
<script setup lang="ts">
import { ElScrollbar, ElButton } from 'element-plus'
import { getrechargePackage } from '@/api/recharge'
import { apiRechage } from '@/api/pay'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
//支付弹框ref
const PaymentRef = ref(null)

//已选项
const selectval = ref(0)
const selectItem: any = ref({})

//选择充值次数
const handleselected = (index: number, item: any) => {
    selectval.value = index
    selectItem.value = item
}
const { data } = await useAsyncData(() => getrechargePackage({}))
//选择第一条
const selectFirst = () => {
    let index = data.value.lists?.findIndex((item) => item.is_recommend)
    if (index === -1) index = 0
    handleselected(index, data.value.lists[index])
}
//充值
const handlePay = async () => {
    const orderData = await apiRechage({
        recharge_package_id: selectItem.value.id
    })
    PaymentRef.value.open({ ...orderData, ...selectItem.value })
}

onMounted(async () => {
    selectFirst()
})
definePageMeta({
    auth: true,
    layout: false
})
</script>
<style lang="scss" scoped>
.recharge-package-item {
    border: 1px solid;
    border-radius: 6px;
    overflow: hidden;
    margin: 4px 0;
    cursor: pointer;
    @apply border-br-light;
    &.actived {
        outline: 1px solid;
        @apply border-primary outline-primary;
    }
}
.recharge-package {
    :deep() {
        .swiper-button-prev,
        .swiper-button-next {
            --swiper-navigation-size: 15px;
            --swiper-navigation-color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 0;
            transform: translateY(-50%);
            cursor: pointer;
            background-color: rgba(0, 0, 0, 0.4);
            &.swiper-button-disabled {
                opacity: 0;
            }
        }
        .swiper-button-prev {
            left: 0;
        }
        .swiper-button-next {
            right: 0;
        }
    }
}
</style>
