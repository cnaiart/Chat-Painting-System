export default () => ({
    title: '任务中心',
    name: 'task-center',
    content: {
        title: '每日任务',
        subTitle: '免费获得条数',
        data: [
            {
                image: '/resource/image/api/default/sign.png',
                name: '每日签到',
                customName: '',
                show: 1,
                type: 1
            },
            {
                image: '/resource/image/api/default/invite.png',
                name: '邀请新用户',
                customName: '',
                show: 1,
                type: 2
            },
            {
                image: '/resource/image/api/default/share.png',
                name: '分享给好友',
                customName: '',
                show: 1,
                type: 3
            },
            {
                image: '/resource/image/api/default/share.png',
                name: '分享绘画至广场',
                customName: '',
                show: 1,
                type: 4
            }
        ]
    },
    styles: {}
})
