import { useAppStore } from '~/stores/app'
const appStore = useAppStore()
const is_draw_open = appStore.getDrawConfig.is_open

export const SIDEBAR = [
    {
        name: '对话',
        path: '/',
        icon: 'dialogue'
    },
    {
        name: '创作',
        path: '/create',
        icon: 'create'
    },
    {
        name: '技能',
        path: '/skill',
        icon: 'skill'
    },
    {
        name: '绘画',
        hidden: !is_draw_open,
        path: '/drawing',
        icon: 'painting'
    }
]

// export const SIDEBAR = [
//     {
//         module: 'personal',
//         hidden: true,
//         children: [
//             {
//                 name: '个人中心',
//                 path: '/user',
//                 children: [
//                     {
//                         name: '个人信息',
//                         path: 'info'
//                     },
//                     {
//                         name: '我的收藏',
//                         path: 'collection'
//                     }
//                 ]
//             },

//             {
//                 name: '账户设置',
//                 path: '/account',
//                 children: [
//                     {
//                         name: '账户安全',
//                         path: 'security'
//                     }
//                 ]
//             }
//         ]
//     }
// ]
