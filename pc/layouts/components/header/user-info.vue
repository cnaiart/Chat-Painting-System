<template>
    <div class="user-info">
        <el-drawer v-model="showPopup" title="个人信息">
            <el-form label-width="90px" label-position="left">
                <el-form-item label="用户头像">
                    <div class="flex items-center">
                        <div class="change-btn">
                            <CropperUpload
                                @change="
                                    setUserInfo($event, UserFieldEnum.AVATAR)
                                "
                            >
                                <!-- <Icon class="ml-2" name="el-icon-EditPen" /> -->
                                <!-- <span class="ml-2 text-[#4073FA]">修改</span> -->
                                <ElAvatar
                                    :size="60"
                                    :src="userStore.userInfo.avatar"
                                ></ElAvatar>
                            </CropperUpload>
                        </div>
                    </div>
                </el-form-item>

                <el-form-item label="用户昵称">
                    <div class="flex items-center">
                        <div v-if="!isEdit">
                            {{ userStore.userInfo.nickname }}
                        </div>
                        <ElInput
                            v-if="isEdit"
                            v-model="nameValue"
                            size="small"
                        />

                        <template v-if="isEdit">
                            <div
                                class="cursor-pointer mr-[6px] flex"
                                @click="
                                    setUserInfo(
                                        nameValue,
                                        UserFieldEnum.NICKNAME
                                    )
                                "
                            >
                                <Icon name="el-icon-Select" />
                            </div>
                            <div
                                class="cursor-pointer flex"
                                @click="isEdit = false"
                            >
                                <Icon name="el-icon-CloseBold" />
                            </div>
                        </template>
                        <template v-else>
                            <div
                                class="cursor-pointer ml-[6px] flex text-primary"
                                @click="isEdit = true"
                            >
                                <Icon class="ml-2" name="el-icon-EditPen" />
                            </div>
                            <!-- <div class="cursor-pointer flex" @click="emit('delete', itemId)">
              <Icon name="el-icon-Delete" />
            </div> -->
                        </template>
                    </div>
                </el-form-item>
                <el-form-item label="用户ID">
                    <div>{{ userStore.userInfo.sn }}</div>
                </el-form-item>
                <el-form-item label="邀请人">
                    <div>{{ userStore.userInfo.inviter_name || '-' }}</div>
                </el-form-item>
                <el-form-item label="手机号码">
                    <div>
                        {{ userStore.userInfo.mobile || '-' }}
                        <span class="text-tx-secondary"
                            >（如需修改，请前往移动端修改）</span
                        >
                    </div>
                </el-form-item>
                <el-form-item label="注册时间">
                    <div>{{ userStore.userInfo.create_time }}</div>
                </el-form-item>
                <div class="font-bold !text-[#333] mb-[20px]">我的资产</div>
                <el-form-item label="对话余额">
                    <div>{{ userStore.userInfo.balance }}条</div>
                </el-form-item>
                <el-form-item
                    label="绘画余额"
                    v-if="appStore.getDrawConfig.is_open"
                >
                    <div>{{ userStore.userInfo.balance_draw }}条</div>
                </el-form-item>
                <el-form-item label="vip会员" v-if="memberPackageTimeView">
                    <div class="text-[#F0AB43]">
                        <span v-if="userStore.userInfo.is_member == 0"
                        >未开通</span
                        >
                        <span v-else>
                            {{ userStore.userInfo.member_package_name }}
                            （{{ userStore.userInfo.member_end_time }}）
                        </span>
                    </div>
                </el-form-item>
            </el-form>
        </el-drawer>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '~/stores/user'
import { userEdit } from '@/api/user'
import { useAppStore } from '~~/stores/app'
const userStore = useUserStore()
const appStore = useAppStore()
const showPopup = shallowRef(false)
const isEdit = ref(false)
const nameValue = ref('')
// 用户资料
enum UserFieldEnum {
    NONE = '',
    AVATAR = 'avatar',
    USERNAME = 'account',
    NICKNAME = 'nickname',
    SEX = 'sex'
}

const memberPackageTimeView = computed(() => appStore.config?.member_package_time_view?.includes('2'))

//打开弹框
const open = () => {
    showPopup.value = true
}

const setUserInfo = async (value: any, field: any) => {
    await userEdit({ value, field })
    isEdit.value = false
    userStore.getUser()
}

defineExpose({ open })
</script>

<style scoped lang="scss">
.user-info {
    :deep(.el-drawer__header) {
        border: none !important;
    }
}
</style>
