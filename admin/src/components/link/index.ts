export enum LinkTypeEnum {
    'SHOP_PAGES' = 'shop',
    'CUSTOM_LINK' = 'custom'
}

export interface Link {
    path: string
    name?: string
    type: string
    query?: Record<string, any>
}

export const mobileLink = [
    {
        path: '/pages/index/index',
        name: 'AI对话',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },

    {
        path: '/pages/user/user',
        name: '个人中心',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/collection/collection',
        name: '我的收藏',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/customer_service/customer_service',
        name: '联系客服',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/user_set/user_set',
        name: '个人设置',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/as_us/as_us',
        name: '关于我们',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/pages/user_data/user_data',
        name: '个人资料',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/agreement/agreement',
        name: '隐私政策',
        query: {
            type: 'privacy'
        },
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/pages/agreement/agreement',
        name: '服务协议',
        query: {
            type: 'service'
        },
        type: LinkTypeEnum.SHOP_PAGES
    },

    {
        path: '/packages/pages/feedback/feedback',
        name: '意见反馈',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/task_center/task_center',
        name: '任务中心',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/recharge/recharge',
        name: '充值中心',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/pages/ai_creation/ai_creation',
        name: 'AI创作',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/drawing/drawing',
        name: 'AI绘画',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/pages/app/app',
        name: 'AI应用',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/square/square',
        name: '绘画广场',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/pages/skills/skills',
        name: 'AI技能',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/mind_map/mind_map',
        name: '思维导图',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/qrcode/qrcode',
        name: '艺术二维码',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/open_vip/open_vip',
        name: '会员中心',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/promotion_center/promotion_center',
        name: '分销中心',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/invite_poster/invite_poster',
        name: '邀请海报',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/article_list/article_list',
        name: '文章资讯',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/follow_official/follow_official',
        name: '关注公众号',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/redeem_code/redeem_code',
        name: '卡密兑换',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    }
]

export const pcLink = [
    {
        path: '/',
        name: '首页',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/chat',
        name: 'AI对话',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/create',
        name: 'AI创作',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/app',
        name: 'AI应用',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/app/drawing',
        name: 'AI绘画',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/app/square',
        name: '绘画广场',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/app/mind_map',
        name: '思维导图',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/app/qrcode',
        name: '艺术二维码',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/skill',
        name: 'AI技能',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/openvip',
        name: '开通会员',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/recharge',
        name: '任务奖励',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/promotion/distribution',
        name: '分销推广',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/user/collection',
        name: '我的收藏',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/user/opinion',
        name: '意见反馈',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/news',
        name: '资讯中心',
        type: LinkTypeEnum.SHOP_PAGES
    }
]
