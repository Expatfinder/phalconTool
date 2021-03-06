<?
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Assets\Filters\Jsmin;
/**
 * The Assets plugin manages all assets (CSS/JS) on application.
 */
class AssetsPlugin extends Component
{
    /**
     * The event is called before the controller action.
     *
     * We create collection for assets.
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $currentPath = ($dispatcher->getActionName() === 'index') ? $dispatcher->getControllerName() : $dispatcher->getControllerName().'/'.$dispatcher->getActionName(); 
        
        $this->assets->collection('libjs')
        ->setPrefix('/lib/'.$dispatcher->getControllerName().'/public/js/');

        $this->assets->collection('js')
        ->setPrefix(APP.'/js/');

        $this->assets->collection('bowerjs')
        ->setPrefix('/bower_components/')
        ->addJs('require/index.js');

        $prefix = in_array($dispatcher->getControllerName(), $this->config->libraries->toArray()) ? 'lib' : '';

        $this->assets->collection($prefix.'js')
        ->addJs("$currentPath/".((ENV==="prod") ? "build" : "main").".js");
        
        $this->assets->collection('libcss')
        ->setPrefix('/lib/'.$dispatcher->getControllerName().'/public/css/');

        $this->assets->collection('css')
        ->setPrefix(APP.'/css/');
        
        $this->assets->collection($prefix.'css')
        ->addCss("$currentPath/main.css");
        
    }

    public function afterDispatch(Event $event, Dispatcher $dispatcher)
    {
        foreach($this->assets->getCollections() as $name => $collection){
            foreach($this->assets->collection($name) as $ressource){
                $ressource->setPath($ressource->getPath().'?v='.$this->config->version); 
            }
        }
    }
}