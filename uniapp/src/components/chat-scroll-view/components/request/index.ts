import { eventStream } from './http'

export const getChat = (data: any, config: any) => {
    return eventStream(
        {
            url: '/chat/chat',
            data,
            method: 'POST'
        },
        config
    )
}
