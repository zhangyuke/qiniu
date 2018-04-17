<?php

namespace app\backend\model;

use think\Model;

#平台视频模型
class VideoModel extends Model
{
    # 确定链接表名
    protected $table = 'video';

    /**
     * 根据搜索条件获取视频列表信息
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getUsersByWhere($where, $offset, $limit)
    {
        return $this->where($where)->limit($offset, $limit)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的视频数量
     * @param $where
     */
    public function getAllUsers($where)
    {
        return $this->where($where)->count();
    }
    /**
     * 根据搜索条件获取所有的视频数量
     * @param $where
     */
    public function getAllVideo($where)
    {
        return $this->where($where)->field('id,title')->select();
    }

    /**
     * 插入视频信息
     * @param $param
     */
    public function insertUser($param)
    {
        try {

            $result = $this->allowField(true)->save($param);
            if (false === $result) {

                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());

            } else {

                return msg(1, $result, '添加视频成功');
            }
        } catch (PDOException $e) {

            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 编辑管理员信息
     * @param $param
     */
    public function editUser($param)
    {
        try {

            $result = $this->allowField(true)->save($param, ['id' => $param['id']]);

            if (false === $result) {
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            } else {

                return msg(1, url('users/index'), '编辑视频成功');
            }
        } catch (PDOException $e) {
            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 根据管理员id获取角色信息
     * @param $id
     */
    public function getOneUser($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 删除管理员
     * @param $id
     */
    public function delUser($id)
    {
        try {

            $this->where('id', $id)->delete();
            return msg(1, '', '删除视频成功');

        } catch (PDOException $e) {
            return msg(-1, '', $e->getMessage());
        }
    }

}
