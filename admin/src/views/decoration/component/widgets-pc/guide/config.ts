const config = () => ({
    title: '引导设置',
    name: 'guide',
    isShow: true,
    prop: {
        bgImage: '',
        content: '',
        link: {},
        isShowBtn: true,
        btnText: ''
    }
})

export type Prop = ReturnType<typeof config>['prop']
export default config
