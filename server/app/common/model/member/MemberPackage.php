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

namespace app\common\model\member;


use app\common\enum\MemberPackageEnum;
use app\common\enum\PayEnum;
use app\common\model\BaseModel;
use app\common\model\user\UserMember;
use think\model\concern\SoftDelete;

class MemberPackage extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


    /**
     * @notes 购买人数
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2023/4/14 11:45 上午
     */
    public function getBuyNumAttr($value,$data)
    {
        $result = UserMember::where(['package_info->id'=>$data['id']])->setFieldType(['package_info->id' => 'int'])->count('id');
        return $result;
    }

    /**
     * @notes 会员权益
     * @param $value
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/6/27 2:19 下午
     */
    public function getMemberBenefitsAttr($value,$data)
    {
        $result = MemberBenefits::where(['status'=>1])->field('id,name,image,describe')->order(['sort'=>'desc','id'=>'desc'])->select()->toArray();
        if (!empty($result)) {
            $benefits_ids = empty($data['benefits_ids']) ? [] : explode(',',$data['benefits_ids']);
            foreach ($result as $key=>$val) {
                if (in_array($val['id'],$benefits_ids)) {
                    $result[$key]['is_checked'] = 1;
                } else {
                    $result[$key]['is_checked'] = 0;
                }
            }
        }

        return $result;
    }

    /**
     * @notes 套餐时长
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2023/12/4 4:14 下午
     */
    public function getDurationDescAttr($value,$data)
    {
        $result = '';
        switch ($data['duration_type']) {
            case MemberPackageEnum::DURATION_TYPE_MONTH:
                $result = $data['duration'].'个月';
                break;
            case MemberPackageEnum::DURATION_TYPE_DAY:
                $result = $data['duration'].'天';
                break;
        }

        return $result;
    }
}