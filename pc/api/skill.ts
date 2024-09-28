/**
 * @description 技能列表
 * @return { Promise }
 */
export function getSkillList(params?: any) {
    return $request.get({ url: '/skill/lists', params })
}