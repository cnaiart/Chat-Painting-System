export function getsamplesLists(params: any) {
    return $request.get({
        url: '/question/samplesLists',
        params
    })
}
export function getdecorate(params: any) {
    return $request.get({
        url: '/index/decorate',
        params
    })
}