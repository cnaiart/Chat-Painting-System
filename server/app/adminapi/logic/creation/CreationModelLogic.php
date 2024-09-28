<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\adminapi\logic\creation;
use app\common\enum\chat\ChatEnum;
use app\common\model\creation\CreationModel;
use app\common\service\ConfigService;

/**
 * 创作模型逻辑类
 * Class CreationModelLogic
 * @package app\adminapi\logic\reation
 */
class CreationModelLogic
{

    /**
     * @notes 添加模型
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/4/14 14:57
     */
    public function add(array $post): bool
    {
        CreationModel::create($post);
        return true;
    }


    /**
     * @notes 修改模型
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/4/14 15:05
     */
    public function edit(array $post): bool
    {
        CreationModel::update($post);
        return true;
    }


    /**
     * @notes 删除
     * @param $id
     * @return bool|string
     * @author cjhao
     * @date 2023/4/14 15:23
     */
    public function del($id)
    {
        CreationModel::where(['id'=>$id])->delete();
        return true;
    }

    /**
     * @notes 获取详情
     * @param int $id
     * @return array
     * @author cjhao
     * @date 2023/4/17 10:14
     */
    public function detail(int $id):array
    {
        $detail = CreationModel::withoutField('create_time,update_time,delete_time')->with(['category'])->where(['id'=>$id])->findOrEmpty()->toArray();
        $type = ConfigService::get('ai_chat',  'type',ChatEnum::OPEN_GPT_35);
        //读取默认数据
        $defaultConfig = ChatEnum::getDefaultChatConfig($type);
        $detail['presence_penalty'] <= 0 &&   $detail['presence_penalty'] = $defaultConfig['presence_penalty'] ?? '';
        $detail['frequency_penalty'] <= 0 && $detail['frequency_penalty'] = $defaultConfig['frequency_penalty'] ?? '';
        $detail['n'] <= 0 &&  $detail['n'] = $defaultConfig['n'] ?? '';
//            $detail['presence_penalty'] = '';
//            $detail['frequency_penalty'] = '';
//            $detail['n'] = '';
        $detail['form'] = empty($detail['form']) ? [] : $detail['form'];
        return $detail;
    }


    /**
     * @notes 修改状态
     * @param int $id
     * @return bool
     * @author cjhao
     * @date 2023/4/14 15:33
     */
    public function status(int $id){
        $creationModel = CreationModel::where(['id' => $id])->findOrEmpty();
        if($creationModel->isEmpty()){
            return true;
        }
        $creationModel->status = $creationModel->status ? 0 : 1;
        $creationModel->save();
        return true;
    }

}