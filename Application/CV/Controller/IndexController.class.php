<?php
namespace CV\Controller;

use Think\Controller;
use Think\Upload;

class IndexController extends Controller
{
    public function index()
    {
        $user_id = $_SESSION['sess_wcl']['id'];
        /**
         * 获取行业信息
         */
        $industry = M('industry');
        $this->indsutry_list = $industry->field('id,name')->order('id')->select();
        /**
         * 获取基本信息
         */
        $cv = M('cv');
        $this->basic_list = $cv
            ->where([' wcl_cv.user_id' => $user_id])
            ->field('wcl_cv.realname,wcl_cv.id,wcl_cv.updated_at')
            ->select();
        $this->display();
    }

    /***
     * ajax方式获取行业对应的职位
     * @param $industry_id
     */
    public function getFunction($industry_id)
    {
        /**
         * ajax请求传过来的行业id,,为职位表的外键
         */
        $industry_id = I('post.industry_id');
        /**
         * 获取职位信息
         */
        $function = M('function');
        $function_list = $function->where(['industry_id' => $industry_id])->field('id,name')->order('id')->select();
        /**
         * 转换成json格式返回
         */
        $this->ajaxReturn($function_list);
    }

    /**
     * 创建一个简历
     */
    public function createcv()
    {
        $user_id = $_SESSION['sess_wcl']['id'];
        if ($user_id) {
            /**
             * 到编辑页面的主页面
             */
            $this->display();
        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，不能创建简历', '/User/Index/login');
        }
    }

    /***
     * ajax方式获取行业对应的职位
     * @param $industry_id
     */
    public function getCity($province_id)
    {
        /**
         * ajax请求传过来的行业id,,为城市表的外键
         */
        $province_id = I('post.province_id');
        /**
         * 获取城市信息
         */
        $city = M('city');
        $city_list = $city->where(['province_id' => $province_id])->field('id,name')->order('id')->select();
        /**
         * 转换成json格式返回
         */
        $this->ajaxReturn($city_list);
    }

    /**
     * 新增基本信息
     */
    public function BasicAdd()
    {
        $user_id = $_SESSION['sess_wcl']['id'];
        if ($user_id) {
            /**
             * 通过user_id 查看该用户是否已经创建了简历，如果已经创建，则不能重复创建
             */
            $cv = M('cv');
            $cvinfo = $cv->where(['user_id' => $user_id])->field('id,realname')->select();
            if ($cvinfo) {
                /**
                 * 已经创建过简历
                 */
                $this->error('您已经创建过简历基本信息，不能重复创建');
            } else {
                /**
                 * 外语水平列表
                 */
                $level = M('level');
                $this->level_list = $level->field('id,name')->select();
                /**
                 * 学历列表
                 */
                $degree = M('degree');
                $this->degree_list = $degree->field('id,name')->select();
                /**
                 * 民族列表
                 */
                $nation = M('nation');
                $this->nation_list = $nation->field('id,name')->select();
                /**
                 * 省份列表
                 */
                $province = M('province');
                $this->province_list = $province->field('id,name')->select();
                /**
                 * 工作年限列表
                 */
                $worktime = M('worktime');
                $this->worktime_list = $worktime->field('id,name')->select();
                $this->display();
            }

        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，无法访问该网页！', '/User/Index/login');
        }
    }

    /**
     * 对简历的保存
     */
    public function BasicSave()
    {
        /**
         * 对上传文件进行处理
         */
        $upload = new Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = ['jpg', 'gif', 'png', 'jpeg'];// 设置附件上传类型
        $upload->rootPath = C('TMPL_PARSE_STRING')['UPLOAD_PATH'] . 'users/'; // 设置附件上传根目录
        $upload->savePath = ''; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            /**
             * 上传成功再对表单进行处理
             */
            $cv = D("cv"); // 实例化User对象
            if (!$cv->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($cv->getError());
            } else {
                // 验证通过 可以进行其他数据操作
                $cv->photo_name = $info['photo']['name'];
                $cv->photo_path = $info['photo']['savepath'] . $info['photo']['savename'];
                $result = $cv->add();
                if ($result) {
                    $this->success('基本信息保存成功', '/CV/Index/createcv');
                } else {
                    $this->error('写入错误');
                }
            }
        }
    }

    /**
     * 简历预览功能
     */
    public function showcv()
    {
        $user_id = $_SESSION['sess_wcl']['id'];
        if ($user_id) {
            /**
             * 通过user_id 查看该用户是否已经创建了简历，如果还没有创建，就先去创建简历
             */
            $cv = M('cv');
            $cvinfo = $cv->where(['user_id' => $user_id])->field('id,realname')->select();
            if ($cvinfo) {
                /**
                 * 已经创建过简历,那就把简历信息拿出来
                 */
                $user = M('user');
                $cv = M('cv');
                /**
                 * 获取工作意向相关信息
                 */
                $this->job_list = $user
                    ->where([' wcl_cv.user_id' => $user_id])
                    ->join('left join wcl_cv ON wcl_user.id = wcl_cv.user_id')
                    ->join('left join  wcl_job ON wcl_cv.id = wcl_job.cv_id')
                    ->join('left join  wcl_industry ON wcl_industry.id = wcl_job.sindustry_id')
                    ->join('left join  wcl_companytype ON wcl_companytype.id = wcl_job.scompanytype_id')
                    ->join('left join  wcl_function ON wcl_function.id = wcl_job.sfunction_id')
                    ->join('left join  wcl_salary ON wcl_salary.id = wcl_job.ssalary_id')
                    ->field('wcl_job.shouse,wcl_industry.name as industry_name,wcl_companytype.name as companytype_name,wcl_function.name as function_name,wcl_salary.cash as salary_cash')
                    ->select();

                /**
                 * 获取教育经历相关信息
                 */
                $this->edu_list = $user
                    ->where(['wcl_cv.user_id' => $user_id])
                    ->join('wcl_cv ON wcl_user.id = wcl_cv.user_id')
                    ->join('wcl_edu ON wcl_cv.id = wcl_edu.cv_id')
                    ->join('wcl_degree ON wcl_degree.id = wcl_edu.degree_id')
                    ->field('wcl_edu.fromdate,wcl_edu.todate,wcl_edu.school,wcl_degree.name')
                    ->order('todate')
                    ->select();

                /**
                 * 获取基本信息
                 */
                $this->basic_list = $cv
                    ->where([' wcl_cv.user_id' => $user_id])
                    ->join('left join wcl_degree ON wcl_degree.id = wcl_cv.degree_id')
                    ->join('left join wcl_nation ON wcl_nation.id = wcl_cv.nation_id')
                    ->join('left join wcl_worktime ON wcl_worktime.id = wcl_cv.worktime_id')
                    ->join('left join wcl_province ON wcl_province.id = wcl_cv.province_id')
                    ->join('left join wcl_city ON wcl_city.id = wcl_cv.city_id')
                    ->join('left join wcl_level ON wcl_level.id = wcl_cv.level_id')
                    ->field
                    (
                        'wcl_cv.realname,wcl_cv.mobile,wcl_cv.email,wcl_cv.gender,wcl_cv.gender,
                    wcl_cv.birthday,wcl_cv.evaluation,wcl_cv.school,wcl_cv.language,wcl_degree.name
                     as degree_name,wcl_nation.name as nation_name,wcl_worktime.name as worktime_name,
                     wcl_province.name as province_name ,wcl_city.name as city_name,wcl_level.name as level_name'
                    )
                    ->select();
                $this->display();
            } else {
                /**
                 * 还没有创建简历，进入简历创建页
                 */
                $this->error('您还没有创建简历，将进入简历创建页', '/CV/Index/createcv');
            }

        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，无法访问该网页', '/User/Index/login');
        }
    }

    /**
     * 简历完善修改功能实现
     */
    public function editcv()
    {
        $this->display();
    }

    public function BasicEdit()
    {
        $user_id = $_SESSION['sess_wcl']['id'];
        if ($user_id) {
            /**
             * 通过user_id 查看该用户是否已经创建了简历，如果还没有创建，就先去创建简历
             */
            $cv = M('cv');
            $cvinfo = $cv->where(['user_id' => $user_id])->field('id,realname,city_id')->select();
            if ($cvinfo) {
                /**
                 * 外语水平列表
                 */
                $level = M('level');
                $this->level_list = $level->field('id,name')->select();
                /**
                 * 学历列表
                 */
                $degree = M('degree');
                $this->degree_list = $degree->field('id,name')->select();
                /**
                 * 民族列表
                 */
                $nation = M('nation');
                $this->nation_list = $nation->field('id,name')->select();
                /**
                 * 省份列表
                 */
                $province = M('province');
                $this->province_list = $province->field('id,name')->select();
                /**
                 * 所拿到的城市值
                 */
                $city = M('city');
                $this->city_list = $city->where(['id' => $cvinfo[0]['city_id']])->field('id,name')->select();
                /**
                 * 工作年限列表
                 */
                $worktime = M('worktime');
                $this->worktime_list = $worktime->field('id,name')->select();
                /**
                 * 已经创建过简历,那就把简历信息拿出来
                 */
                $user = M('user');
                $cv = M('cv');
                /**
                 * 获取基本信息
                 */
                $this->basic_list = $cv
                    ->where([' wcl_cv.user_id' => $user_id])
                    ->select();
                $this->display();
            } else {
                /**
                 * 还没有创建简历，进入简历创建页
                 */
                $this->error('您还没有创建简历，不能对其修改，将进入简历创建页', '/CV/Index/createcv');
            }

        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，无法访问该网页', '/User/Index/login');
        }
    }

    public function BasicEditSave()
    {
        $id=I('post.id');
        if($id) {
            /**
             * 对上传文件进行处理
             */
            $upload = new Upload();// 实例化上传类
            $upload->maxSize = 3145728;// 设置附件上传大小
            $upload->exts = ['jpg', 'gif', 'png', 'jpeg'];// 设置附件上传类型
            $upload->rootPath = C('TMPL_PARSE_STRING')['UPLOAD_PATH'] . 'users/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } else {// 上传成功
                /**
                 * 上传成功再对表单进行处理
                 */
                $cv = D("cv"); // 实例化User对象
                if (!$cv->create()) {
                    // 如果创建失败 表示验证没有通过 输出错误提示信息
                    $this->error($cv->getError());
                } else {
                    // 验证通过 可以进行其他数据操作
                    $cv->photo_name = $info['photo']['name'];
                    $cv->photo_path = $info['photo']['savepath'] . $info['photo']['savename'];
                    $result = $cv->save();
                    if ($result) {
                        $this->success('基本信息更新成功', '/CV/Index/editcv');
                    } else {
                        $this->error('写入错误');
                    }
                }
            }
        }else {
            $this->error('无法访问该网页', '/User/Index/login');
        }
    }
}