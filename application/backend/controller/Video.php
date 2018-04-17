<?php
namespace app\backend\controller;

use app\backend\model\VideoModel;
use QiniuUpload\UploadQiniu;

class Video extends Base
{
    //视频列表
    public function index()
    {
        if (request()->isAjax()) {

            $param = input('param.');

            $limit  = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where  = [];
            if ($param['title']) {
                $where['title'] = ['like', '%' . trim($param['title']) . '%'];
            }

            $user         = new VideoModel();
            $selectResult = $user->getUsersByWhere($where, $offset, $limit);

            // 拼装参数
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['created_at'] = date('Y-m-d H:i:s', $vo['created_at']);
                $selectResult[$key]['thumbnail']  = "<a onclick='detail(" . $vo['id'] . ")'>点击查看详情</a>";
                $selectResult[$key]['content']    = mb_substr($vo['content'], 0, 10) . "....";
                $selectResult[$key]['title']      = mb_substr($vo['title'], 0, 10) . "....";
                $selectResult[$key]['operate']    = showOperate1(['删除' => "javascript:delinfo(" . $vo['id'] . ")"]);
            }
            $return['total'] = $user->getAllUsers($where); //总数据
            $return['rows']  = $selectResult;
            return json($return);
        }
        return $this->fetch();
    }

    // 添加用户
    public function videoadd()
    {
        if (request()->isPost()) {

            $param          = input('param.');
            $user           = new VideoModel();
            $d = request()->file('thumbnail');
            try{
                $info_d = $d->validate(['ext' => 'jpg,png'])->move(ROOT_PATH . 'public' . DS . 'uploads/course/thumbnail/');
                $param['thumbnail'] = DOMIAN . '/uploads/course/thumbnail/' . $info_d->getSaveName();

                $qiniu          = new UploadQiniu();
                $param['keyId'] = $qiniu->fopss($param['keyId'])['data'];//提交转码任务
                $param['created_at'] = time();
            }catch(Exception $e)
            {
                throw new Exception($e);
            }
            
            $flag           = $user->insertUser($param);

            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }

        return $this->fetch();
    }
    #用户详情
    public function videoDetail()
    {
        $id = input('param.id');

        $user = new VideoModel();
        $data = $user->getOneUser($id);

        return json($data);
    }

    #删除用户
    public function videoDel()
    {
        $id = input('param.id');

        $role = new VideoModel();
        $flag = $role->delUser($id);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

}
