<?php
namespace models;

class Role extends Model
{
    // 设置这个模型对应的表
    protected $table = 'role';
    // 设置允许接收的字段
    protected $fillable = ['role_name'];

    // 添加、修改角色之后自动被执行
    // 添加时，获取新添加的角色ID： $this->data['id']
    // 修改时，获取要修改的角色ID：$_GET['id']
    protected function _after_write()
    {
        // var_dump( $_POST );
        // exit;
        $roleId = isset($_GET['id']) ? $_GET['id'] : $this->data['id'];

        // 删除原权限
        $stmt = $this->_db->prepare('DELETE FROM role_privlege WHERE role_id=?');
        $stmt->execute([
            $roleId
        ]);

        // 重新添加新勾选的权限
        $stmt = $this->_db->prepare("INSERT INTO role_privlege(pri_id,role_id) VALUES(?,?)");
        // 循环所有勾选的权限ID插入到中间表
        foreach($_POST['pri_id'] as $v)
        {
            $stmt->execute([
                $v,
                $roleId,
            ]);
        }
    }

    // 在删除之前执行
    protected function _before_delete()
    {
        $stmt = $this->_db->prepare("delete from role_privlege where role_id=?");
        $stmt->execute([
            $_GET['id']
        ]);
    }

    // 取出拥有的权限ID
    public function getPriIds($roleId)
    {
        // 预处理
        $stmt = $this->_db->prepare('SELECT pri_id FROM role_privlege WHERE role_id=?');
        // 执行
        $stmt->execute([
            $roleId
        ]);
        // 取数据
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 转成一维的
        $_ret = [];
        foreach($data as $k => $v)
        {
            $_ret[] = $v['pri_id'];
        }
        // 把一维的返回
        return $_ret;
    }
}