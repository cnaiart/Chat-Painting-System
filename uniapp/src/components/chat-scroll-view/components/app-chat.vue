<template>
    <view>
        <text
            :paramsData="paramsData"
            :change:paramsData="appChatRender.getData"
        ></text>
        <text
            :requestData="requestData"
            :change:requestData="appChatRender.getRequestData"
        ></text>
        <!--暂停-->
        <text
            :stopCont="stopCont"
            :change:stopCont="appChatRender.stopMid"
        ></text>
    </view>
</template>

<script lang="ts">
import { nextTick, ref, shallowRef } from 'vue'
import { useUserStore } from '@/stores/user'
import config from '@/config'

interface IParamsData {
    model: string
    question: string
    type: number
    other_id: number
}

export default {
    setup(props, context) {
        let { token } = useUserStore()
        let baseUrl = ''

        const requestData: any = ref({})
        const paramsData = ref({})
        //暂停
        const stopCont: any = ref(0)
        const stop = () => {
            stopCont.value++
        }

        const onmessage = (value: any) => {
            context.emit('onmessage', value)
        }

        const onclose = () => {
            context.emit('onclose')
        }

        const onstart = (reader: any) => {
            console.log(reader.method)
            context.emit('onstart', reader)
            paramsData.value = {}
        }

        const getParamsData = async (data: any) => {
            token = useUserStore().token
            baseUrl = config.baseUrl
            requestData.value.baseUrl = baseUrl
            requestData.value.token = token
            setTimeout(() => {
                Object.keys(data).map((item) => {
                    //@ts-ignore
                    paramsData.value[item] = data[item]
                })
            }, 500)
        }
        return {
            getParamsData,
            stop,
            paramsData,
            requestData,
            stopCont,
            onmessage,
            onclose,
            onstart
        }
    }
}
</script>

<script module="appChatRender" lang="renderjs">
// import { chatSendText } from '@/api/chat'
import { getChat } from "./request/index";
export default {
    data(){
        return{
            paramData:{},
            requestData:{},
            count:0,
            stopMethod:{},
            evn:{}
        }
    },
    methods:{
        async getData(newValue, oldValue, ownerInstance, instance){
            this.paramData = newValue
            if(this.count>0&&Object.keys(this.paramData).length!=0){
                await this.sendChat({},ownerInstance)
            }
            this.count++
        },
        async getRequestData(newValue, oldValue, ownerInstance, instance){
            this.requestData.token = newValue.token
            this.requestData.baseUrl = newValue.baseUrl
        },
        async stopMid(newValue, oldValue, ownerInstance, instance){
            this.stop()
        },
        sendChat(event,ownerInstance){

            let that = this
            getChat({...this.paramData},{
                onstart(reader) {
                    that.stopMethod = reader.cancel.bind(reader)
                    ownerInstance.callMethod('onstart', {method:{}})
                },
                onmessage(value) {
                    ownerInstance.callMethod('onmessage', value)
                },
                onclose(value) {
                    ownerInstance.callMethod('onclose', value)
                },
                baseUrl:this.requestData.baseUrl,
                token:this.requestData.token
            })
        },
        stop(){
            this.stopMethod()
        }

    }
}
</script>
