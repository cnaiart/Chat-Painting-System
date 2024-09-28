<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\api\logic;


use app\common\enum\user\AccountLogEnum;
use app\common\enum\WithdrawEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\distribution\WithdrawApply;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;
use think\facade\Db;

class WithdrawLogic extends BaseLogic
{
    /**
     * @notes 提现申请
     * @param $params
     * @return false|mixed
     * @author ljj
     * @date 2023/5/24 8:38 下午
     */
    public static function apply($params)
    {
        Db::startTrans();
        try {
            // 手续费
            $percentage = ConfigService::get('withdraw', 'handling_fee') ?? 0;

            $handlingFee = round(((float)$params['money'] * (float)$percentage / 100), 2);

            //新增提现记录
            $WithdrawApply = WithdrawApply::create([
                'sn' => generate_sn(WithdrawApply::class, 'sn'),
                'user_id' => $params['user_id'],
                'money' => $params['money'],
                'left_money' => $params['money'] - $handlingFee,
                'handling_fee' => $handlingFee,
                'real_name' => $params['type'] == WithdrawEnum::TYPE_WECHAT ? '' : $params['real_name'],
                'account' => $params['type'] == WithdrawEnum::TYPE_WECHAT ? '' : $params['account'],
                'type' => $params['type'],
                'money_qr_code' => $params['money_qr_code'] ?? '',
                'batch_no' => generate_sn(WithdrawApply::class, 'batch_no'),
            ]);

            // 扣减用户可提现金额
            User::update(['user_money'=>['dec',$params['money']]],['id'=>$params['user_id']]);

            // 记录账户流水
            AccountLogLogic::add(
                $params['user_id'],
                AccountLogEnum::MONEY_DEC_WITHDRAW,
                AccountLogEnum::DEC,
                $params['money'],
                $WithdrawApply['sn']
            );

            Db::commit();
            return ['id'=>$WithdrawApply->id];
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 提现详情
     * @param $params
     * @return array
     * @author ljj
     * @date 2023/5/24 8:57 下午
     */
    public function detail($params)
    {
        $result = WithdrawApply::alias('wa')
            ->leftJoin('user u', 'u.id = wa.user_id')
            ->field('wa.id,wa.type,wa.money,wa.left_money,wa.handling_fee,wa.real_name,wa.account,wa.status,wa.verify_remark,wa.verify_time,wa.transfer_remark,wa.finish_time,wa.create_time,u.nickname,wa.money_qr_code')
            ->append(['type_desc','status_desc','handling_fee_ratio'])
            ->findOrEmpty($params['id'])
            ->toArray();

        if (!empty($result)) {
            $result['verify_time'] = empty($result['verify_time']) ? '' : date('Y-m-d H:i:s',$result['verify_time']);
            $result['finish_time'] = empty($result['finish_time']) ? '' : date('Y-m-d H:i:s',$result['finish_time']);
        }

        return $result;
    }

    /**
     * @notes 提现方式
     * @return array[]
     * @author ljj
     * @date 2023/6/20 7:39 下午
     */
    public function withdrawType()
    {
        $data = [
            [
                'id' => 1,
                'title' => '支付宝',
                'image' => FileService::getFileUrl(config('project.default_image.ali')),
            ],
            [
                'id' => 2,
                'title' => '微信零钱',
                'image' => FileService::getFileUrl(config('project.default_image.wechat')),
            ],
            [
                'id' => 3,
                'title' => '微信收款码',
                'image' => FileService::getFileUrl(config('project.default_image.wechat_qrcode')),
            ],
            [
                'id' => 4,
                'title' => '支付宝收款码',
                'image' => FileService::getFileUrl(config('project.default_image.ali_qrcode')),
            ],
        ];
        $type = ConfigService::get('withdraw', 'type',[1]);
        $result = [];
        foreach ($data as $data_val) {
            if (in_array($data_val['id'],$type)) {
                $result[] = $data_val;
            }
        }

        return $result;
    }
}