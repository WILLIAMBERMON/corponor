<?php
// Run: php tests/permisos.php (no database required).
define('BASEPATH', __DIR__);
class CI_Controller {}
class CI_Model {}
class HttpError extends RuntimeException {}
function show_error($message, $status = 500) { throw new HttpError($message, $status); }
function show_404() { throw new HttpError('Not found', 404); }
require __DIR__.'/../application/core/MY_Controller.php';
require __DIR__.'/../application/controllers/Admin.php';
require __DIR__.'/../application/models/Usuario_model.php';
class SessionStub {
    public $data;
    function __construct($role) { $this->data = array('usuario_id'=>7, 'es_admin'=>$role); }
    function userdata($key) { return $this->data[$key] ?? NULL; }
}
class InputStub { public $method='get'; function method() { return $this->method; } }
class UsersStub { function una($id) { return (object)array('id'=>$id, 'nombres'=>'Prueba'); } }
class GroupsStub { function puede_administrar($id,$user,$super) { return $super || $id===10; } }
class ResolutionsStub { function una($id) { return (object)array('id'=>$id,'grupo_id'=>20); } }
class AdminTest extends Admin {
    public $session, $input, $Usuario_model, $Grupo_model, $Resolucion_model, $rendered;
    function __construct($role) {
        $this->session=new SessionStub($role); $this->input=new InputStub();
        $this->Usuario_model=new UsersStub(); $this->Grupo_model=new GroupsStub();
        $this->Resolucion_model=new ResolutionsStub();
    }
    protected function render($view,$data=array(),$layout='admin') { $this->rendered=$data; }
}
function check($condition, $message) { if(!$condition) throw new RuntimeException($message); }
function denied($call, $status) {
    try { $call(); } catch(HttpError $e) { check($e->getCode()===$status,'Unexpected HTTP status'); return; }
    throw new RuntimeException('Expected access denial');
}
foreach(array(0,1,2) as $role) {
    $admin=new AdminTest($role);
    $admin->usuario_clave(7);
    check($admin->rendered['usuario']->id===7,'Own password unavailable');
    if($role===0) denied(function() use($admin) { $admin->usuario_clave(8); },403);
    else { $admin->usuario_clave(8); check($admin->rendered['usuario']->id===8,'Admin password reset unavailable'); }
    denied(function() use($admin) { $admin->resolucion_eliminar(1); },405);
    denied(function() use($admin) { $admin->grupo_eliminar(1); },405);
}
class UserModelTest extends Usuario_model { public $session, $db; }
foreach(array(0,1,2) as $role) {
    $model=new UserModelTest(); $model->session=new SessionStub($role);
    check($model->es_superadmin()===($role===2),'Incorrect superadmin role');
}
$admin=new AdminTest(0);
$admin->Usuario_model=new UserModelTest(); $admin->Usuario_model->session=$admin->session;
$admin->input->method='post';
denied(function() use($admin) { $admin->resolucion_eliminar(1); },403);
denied(function() use($admin) { $admin->grupo_eliminar(20); },403);
class DbStub {
    public $where;
    function get_where($table,$where) { $this->where=$where; return $this; }
    function row() { return (object)$this->where; }
}
$model=new UserModelTest(); $model->db=new DbStub();
$model->por_documento('AB-001X');
check($model->db->where['documento']==='AB-001X','Alphanumeric document changed');
echo "OK: roles, password access, POST deletion guards, group access, alphanumeric login lookup.\n";
