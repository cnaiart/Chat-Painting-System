const config = () => ({
    title: '标题设置',
    name: 'title',
    isShow: true,
    prop: {
        isShowBtn: true,
        title: '',
        desc: '',
        btnText: '',
        link: {}
    }
})

export type Prop = ReturnType<typeof config>['prop']
export default config
