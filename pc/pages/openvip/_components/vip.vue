<template>
    <div class="h-full flex flex-col bg-white rounded-[6px]">
        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div class="py-[16px] px-[40px]">
                    <div
                        class="flex items-center mx-[125px] my-[25px]"
                        style="
                            background: linear-gradient(
                                -88.46deg,
                                rgba(252, 245, 232, 0) 1.34%,
                                rgba(252, 245, 232, 1) 50.41%,
                                rgba(252, 245, 245, 0) 99.48%
                            );
                        "
                    >
                        <el-carousel
                            height="40px"
                            direction="vertical"
                            :autoplay="true"
                            class="w-full"
                        >
                            <el-carousel-item
                                v-for="item in buyPackageLists"
                                :key="item"
                            >
                                <div
                                    class="w-full flex items-center justify-center"
                                >
                                    <ElAvatar :src="item.avatar" :size="28" />
                                    <span class="ml-[10px] leading-[40px]">
                                        {{ item.nickname }}
                                        开通了
                                        {{ item.member_package }}
                                    </span>
                                </div>
                            </el-carousel-item>
                        </el-carousel>
                    </div>
                    <div class="flex flex-wrap mx-[-10px]">
                        <div
                            v-for="(item, index) in packageState?.lists"
                            :key="index"
                            class="flex-1 max-w-[200px] mx-[10px] rounded-lg border border-br-light border-solid min-w-[180px] mb-[20px] cursor-pointer relative"
                            :class="{
                                ' !border-[rgba(255,179,36,1)] bg-[rgba(255,250,240,1)]':
                                    packageId == item.id
                            }"
                            @click="onChangePackage(item, index)"
                        >
                            <div
                                class="absolute top-[-1.5px] left-[-1px] translate-y-[-50%] bg-[red] inline-block text-white px-[5px] py-[2px] text-[12px] rounded-tl-[8px] rounded-br-[8px] max-w-full"
                                v-if="item.tag"
                            >
                                {{ item.tag }}
                            </div>
                            <div
                                class="flex flex-col items-center rounded-lg mb-[20px] mt-[20px] mx-[20px]"
                            >
                                <div class="text-[16px] line-clamp-1">
                                    {{ item.name }}
                                </div>
                                <div class="text-black flex items-center">
                                    <Price
                                        :content="item.sell_price"
                                        color="#101010"
                                        mainSize="28px"
                                        minorSize="20px"
                                        fontWeight="600"
                                    >
                                    </Price>
                                </div>
                                <div
                                    v-if="item.lineation_price > 0"
                                    class="text-sm"
                                >
                                    <Price
                                        :content="item.lineation_price"
                                        color="#999"
                                        mainSize="14px"
                                        minorSize="14px"
                                        prefix="原价￥"
                                    >
                                    </Price>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="member_benefit mt-[10px]"
                        v-if="memberBenefits.length"
                    >
                        <div>
                            <div class="text-black">会员权益</div>
                            <!-- @vue-ignore -->
                            <swiper
                                :slidesPerView="'auto'"
                                :spaceBetween="12"
                                :navigation="true"
                                class="member-benefits mt-[10px]"
                                :modules="[Navigation]"
                            >
                                <swiper-slide
                                    v-for="item in memberBenefits"
                                    :key="item.id"
                                    style="width: 200px; height: auto"
                                >
                                    <div
                                        class="rounded-lg border border-solid border-br-light p-[15px] flex items-center h-full"
                                    >
                                        <div class="flex">
                                            <img
                                                class="w-[44px] h-[44px]"
                                                :src="item.image"
                                                alt=""
                                            />
                                        </div>
                                        <div class="flex-1 min-w-0 ml-[10px]">
                                            <div class="line-clamp-1">
                                                {{ item.name }}
                                            </div>
                                            <div
                                                class="line-clamp-2 text-tx-secondary text-sm mt-[5px]"
                                            >
                                                {{ item.describe }}
                                            </div>
                                        </div>
                                    </div>
                                </swiper-slide>
                            </swiper>
                        </div>
                    </div>
                    <div
                        class="mt-[20px]"
                        v-if="
                            packageValue.give_chat_number > 0 ||
                            packageValue.give_draw_number > 0
                        "
                    >
                        <div class="text-black">额外赠送</div>
                        <div class="text-sm flex mt-[10px]">
                            <div
                                v-if="packageValue.give_chat_number > 0"
                                class="bg-[#FDF7E9] text-[#E4A71C] px-[5px] py-[1px] rounded-sm mr-[10px]"
                            >
                                对话条数{{ packageValue.give_chat_number }}条
                            </div>
                            <div
                                v-if="packageValue.give_draw_number > 0"
                                class="bg-[#FDF7E9] text-[#E4A71C] px-[5px] py-[1px] rounded-sm mr-[10px]"
                            >
                                绘画条数{{ packageValue.give_draw_number }}条
                            </div>
                        </div>
                    </div>
                    <div class="p-[20px] pt-[80px] text-white text-center">
                        <ElButton
                            type="primary"
                            size="large"
                            class="!px-[80px] !rounded-lg"
                            @click="handleBuyPackage"
                        >
                            <template #default> 立即购买 </template>
                        </ElButton>
                        <div class="mt-[10px] text-sm text-tx-secondary">
                            该服务为虚拟产品，支付成功后不支持退款。
                        </div>
                    </div>
                </div>
            </ElScrollbar>
        </div>

        <Payment
            ref="PaymentRef"
            type="member"
            redirect="/openvip?mode=vip"
            @paySuccess="getMemberPackageList"
        >
            <template v-slot:top="{ name }">
                <ElFormItem label="套餐名称">
                    <div>{{ name }}</div>
                </ElFormItem>
            </template>
        </Payment>
        <limitPop ref="limitPopRef"></limitPop>
    </div>
</template>
<script setup lang="ts">
import { getmemberlists } from '@/api/my'
import { getMemberBuyLog } from '@/api/my'
import { memberBuy } from '@/api/pay'
import limitPop from '../_components/limitPop.vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
const limitPopRef = shallowRef()

// 套餐索引
const packageIndex = ref<number>(0)
// 当前套餐ID
const packageId = ref<number>(0)
// 套餐状态列表
const packageState = reactive<any>({
    statsu: -1,
    lists: []
})
// 已购买套餐的人列表
const buyPackageLists = ref<any[]>([])

// 订单数据
const orderData = ref()

const packageValue = computed(() => {
    return (
        packageState.lists.find((item: any) => item.id == packageId.value) || {}
    )
})

const memberBenefits = computed(() => {
    return (
        packageValue.value.member_benefits?.filter((item) => item.is_checked) ||
        []
    )
})

// 获取会员套餐列表
const getMemberPackageList = async () => {
    try {
        const { status, lists } = await getmemberlists()
        packageState.status = status
        packageState.lists = lists
        const defaultvalue = lists.find((item: any) => item.is_default == 1)
        const defaultvalueIndex = lists.findIndex(
            (item: any) => item.is_default == 1
        )
        if (defaultvalue == null) {
            onChangePackage(lists[0], 0)
            packageState.lists[0].is_default = 1
        } else {
            onChangePackage(defaultvalue, defaultvalueIndex)
        }
    } catch (error) {
        console.log('获取会员套餐列表错误=>', error)
    }
}

// 获取购买套餐的人列表
const getBuyMemberPackageList = async () => {
    try {
        const data = await getMemberBuyLog()
        buyPackageLists.value = data
    } catch (error) {
        console.log('获取会员套餐人数列表错误=>', error)
    }
}

// 选择会员
const onChangePackage = async (value: any, index: number) => {
    packageId.value = value.id
    packageIndex.value = index
}
const PaymentRef = ref(null)
// 购买
const handleBuyPackage = async () => {
    if (packageState.lists[packageIndex.value].quota_tips_show) {
        limitPopRef.value.open(
            packageState.lists[packageIndex.value].quota_tips
        )
        return
    }
    orderData.value = await memberBuy({
        member_package_id: packageId.value
    })
    PaymentRef.value?.open({ ...orderData.value, ...packageValue.value })
}

await useAsyncData(() =>
    Promise.all([getMemberPackageList(), getBuyMemberPackageList()])
)

definePageMeta({
    auth: true,
    layout: false
})
</script>
<style lang="scss" scoped>
.member-benefits {
    :deep() {
        .swiper-button-prev,
        .swiper-button-next {
            --swiper-navigation-size: 15px;
            --swiper-navigation-color: #fff;
            width: 20px;
            height: 30px;
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
