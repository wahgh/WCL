<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
//       $post = M('post');
//       $maxid=$post->max('id');
//       $postCount = $post->count();
//       $condition['id'] = array(array('lt',($maxid-$postCount)),array('gt',$maxid));
//       $this->post_list= $post
//           ->where($condition['id'])
//           ->join('left join wcl_function on wcl_post.function_id=wcl_function.id')
//           ->join('left join wcl_salary on wcl_post.salary_id=wcl_salary.id')
//            ->field('wcl_function.name as function_name,wcl_post.peoplenumber,wcl_salary.cash as salary_cash,
//          wcl_post.mobile,wcl_post.contact')
//          ->select();

        /**
         * ����ҳ�����������
         * 1����һ�ν���ҳ��˭�����Խ�
         * 2����¼֮������ҳ��ַ
         * 3���˳�֮��������һ���û�������ҵ�û��˳�������ҳ
         */
        if (isset($_SESSION['sess_wcl']['is_auth']) && $_SESSION['sess_wcl']['is_auth']) {
            /**
             * ����ͨ�û���¼֮�������ҳ������ģ���û���˳�����ʱ���õ�session�����name��ʾ��ҳ����
             */
            $this->username = $_SESSION['sess_wcl']['username'];
            $this->mark = 'user';
        } else {
            if (isset($_SESSION['sess_wcl']['company_auth']) && $_SESSION['sess_wcl']['company_auth']) {
                /**
                 * ����ͨ�û���¼֮�������ҳ������ģ���û���˳�����ʱ���õ�session�����name��ʾ��ҳ����
                 */
                $this->username = $_SESSION['sess_wcl']['username'];
                $this->mark = 'company';
            } else {
                /**
                 * �ǵ�¼״̬��ֵΪ����
                 */
                $this->username = '';
                $this->mark = '';
            }
        }

        $this->display();
    }
}