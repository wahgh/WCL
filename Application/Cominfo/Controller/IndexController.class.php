<?php
namespace Cominfo\Controller;

use Think\Controller;
use Think\Upload;

class IndexController extends Controller
{
    public function index()
    {
        if(isset($_SESSION['sess_wcl']['company_auth'])&&$_SESSION['sess_wcl']['company_auth']) {
            $this->display();
        }else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，无法访问该网页！', '/Company/Index/login');
        }

    }

    /**
     * 到企业基本信息新增和职位新增页面
     */
    public function create()
    {
        $this->display();
    }

    /**
     * 企业基本信息创建
     */
    public function comInfoAdd()
    {
        $company_id = $_SESSION['sess_wcl']['company_id'];
        if ($company_id) {
            $cv = M('cominfo');
            $cominfo = $cv->where(['companyuser_id' => $company_id])->field('id,name')->select();
            if ($cominfo) {
                /**
                 * 已经创建过企业基本信息
                 */
                $this->error('您已经创建创建企业基本信息，不能重复创建');
            } else {
                /**
                 * 获取公司性质表信息
                 */
                $companytype = M('companytype');
                $this->companytype_list = $companytype->field('id,name')->order('id')->select();
                /**
                 * 省份列表
                 */
                $province = M('province');
                $this->province_list = $province->field('id,name')->select();
                /**
                 * 获取行业信息
                 */
                $industry = M('industry');
                $this->indsutry_list = $industry->field('id,name')->order('id')->select();
                $this->display();
            }

        } else {
            /**
             * 没有session，说明根本没有登录，让他去登录页
             */
            $this->error('您还没有登录，无法访问该网页！', '/Company/Index/login');
        }
    }

    /**
     * 企业基本信息的保存
     */
    public function comInfoSave()
    {
        /**
         * 对上传文件进行处理
         */
        $upload = new Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = ['jpg', 'gif', 'png', 'jpeg'];// 设置附件上传类型
        $upload->rootPath = C('TMPL_PARSE_STRING')['UPLOAD_PATH'] . 'companys/'; // 设置附件上传根目录
        $upload->savePath = ''; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            /**
             * 上传成功再对表单进行处理
             */
            $cv = D("cominfo"); // 实例化User对象
            if (!$cv->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($cv->getError());
            } else {
                // 验证通过 可以进行其他数据操作
                $cv->imagename = $info['photo']['name'];
                $cv->imagepath = $info['photo']['savepath'] . $info['photo']['savename'];
                $result = $cv->add();
                if ($result) {
                    $this->success('基本信息保存成功', '/Cominfo/index/create');
                } else {
                    $this->error('写入错误');
                }
            }
        }
    }
}