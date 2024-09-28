import type { RequestEventStreamOptions } from 'ofetch'
//聊天记录
export function getchatLog(params: any) {
    return $request.get({
        url: '/chat_records/chatLog',
        params
    })
}

//聊天接口
export function getChat(params: any) {
    return $request.post({
        url: '/chat_records/chat',
        params
    })
}

//问题示例列表
export function samplesLists(params: any) {
    return $request.get({
        url: '/question/samplesLists',
        params
    })
}

//清除会话
export function cleanChat(params: any) {
    return $request.post({
        url: '/chat_records/cleanChat',
        params
    })
}

//收藏会话
export function collectRecords(params: any) {
    return $request.post({
        url: '/chat_records/collect',
        params
    })
}

export function chatSendText(params: any, config: RequestEventStreamOptions) {
    return $request.eventStream(
        { url: '/chat/chat', params, method: 'POST' },
        config
    )
}

//获取聊天模型接口
export function getChatModelApi() {
    return $request.get({ url: '/chat/getModel' })
}

export function getCreationDetail(params: any) {
    return $request.get({ url: '/creation/detail', params })
}

export function getSkillDetail(params: any) {
    return $request.get({ url: '/skill/detail', params })
}

export function getChatCategoryLists(params: any) {
    return $request.get({ url: '/chatCategory/lists', params })
}

export function chatCategoryAdd(params: any) {
    return $request.post({ url: '/chatCategory/add', params })
}

export function chatCategoryEdit(params: any) {
    return $request.post({ url: '/chatCategory/edit', params })
}

export function chatCategoryDelete(params: any) {
    return $request.post({ url: '/chatCategory/del', params })
}

export function chatCategoryClear() {
    return $request.post({ url: '/chatCategory/clear' })
}

export function getChatBroadcast(params: any) {
    return $request.post({ url: '/chat/voiceGenerate', params })
}
