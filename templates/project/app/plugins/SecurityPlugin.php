<?
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Manager\User as UserManager;

/**
 * The security plugin manages the Access Control List (ACL).
 */
class SecurityPlugin extends Plugin
{
    /**
     * Get the application acl list.
     */
    private function getAcl()
    {
        $acl = new AclList();
        // deny all access by default
        $acl->setDefaultAction(Acl::DENY);
        $permissions = [];
        // set roles        
        foreach(PermissionType::find() as $permission){
            $acl->addRole($permission->pety_id);
            $permissions[$permission->pety_name] = $permission->pety_id;
        }

        // define public/private ressources
        $private = [
            'index' => ['*'],
            'scrud'=> ['*'],
            'api'=> ['*']
        ];

        $public = [
           'user'  => ['login', 'connect']
        ];

        foreach(['private', 'public'] as $type){
            foreach($$type as $resource => $actions){
                $acl->addResource(new Resource($resource), $actions);
                foreach ($actions as $action) {
                    switch($type){
                        case 'private':
                            $acl->allow($permissions['admin'], $resource, $action);
                            $acl->allow($permissions['user'], $resource, $action);
                            break;
                        case 'public':
                            $acl->allow($permissions['anonymous'], $resource, $action);
                            break;
                    }
                    
                }
            }
        }

        return $acl;
    }

    /**
     * Redirect user to default route if no controller/action found
     * @param  \Phalcon\Events\Event $event      Event of the request
     * @param  \Phalcon\Mvc\Dispatcher $dispatcher Application dispatcher
     * @param  \Phalcon\Exception         $exception  Current Exception
     * @return boolean             Return false on exception found to stop propagation to view engine
     */
    public function beforeException($event, $dispatcher, $exception) {
        switch ($exception->getCode()) {
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                $this->redirectUser();
                return false;
        }
    }

    /**
     * Redirect user to default route
     */
    private function redirectUser(){
        if(!UserManager::isAuthenticated()){
            $this->response->redirect('/user/login');
        } else {
            $this->response->redirect('/');
        }
    }

    /**
     * Check user permissions vs ACL and redirect to default route if not allowed
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        // Check user data exists in session
        $permissions = UserManager::getPermissions();

        //Take the active controller/action from the dispatcher
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        //Obtain the ACL list
        $acl = $this->getAcl();
        //Check if the Role have access to the controller (resource)
        foreach($permissions as $permission){
            $allowed = $acl->isAllowed($permission, $controller, $action);
            if ($allowed === Acl::ALLOW) {
                break;
            }
        }
        if($allowed != Acl::ALLOW) {
            $this->redirectUser();
            $this->view->disable();
        }
    }
}