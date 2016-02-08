# phalconTool

This tool allows to manage project with Phalcon MVC Framework access on MYSQL.
#### Features :
- Create skeleton project application width default User controller using google connect
- User base database with social relation 
- Manage multi application/environment
- Generate models from Mysql database with column map and full relations
- Internal library Management
- Full Rest Api
- SCRUD on the fly with models relations and validations
- Generate controller/action
- Generate js/less template

#### In progress
- Find function Rest Api
- Bdd migrations 
- Cli support inline
- Documentation

## Installation

#### Requirement
- [Phalcon 2.0.x](https://phalconphp.com/fr/download)
- php >=5.4

using composer
```
{
    "require": {
        "v-cult/phalcon": "dev-master",
        "google/apiclient": "1.1.7"
    }
}
```

Create a phalcon symlink to application.php in your root project folder  
```
sudo ln -s /var/www/project/vendor/v-cult/phalcon/application.php /var/www/project/phalcon
```
or in /usr/bin to be used globally
```
sudo ln -s /var/www/project/vendor/v-cult/phalcon/application.php /usr/bin/phalcon
```

## Quick start

For all commands, you can specify the environment and application with options --env= and --app=   
The default values are dev/frontend

### Create project
```
phalcon generate:project
```
It will create apps and public dir in the root project folder initialized with frontend application.  
The Document root of the server must be the public dir.  
By default api et scrud libraries are enbaled and the project is secured by google user connect so you need to create an google application, authorize google+ api and generate devKey, client_id and client_secret to set them inside the configuration file.  
The default action user/login redirect to the google user authentification to log in the application and redirect to the SCRUD index (in progress).

### Generate models
Before generate models, don't forget to modify the config.php in your app folder.
On generation, if the databse is empty, it will import the ones used for the User management.
```
phalcon generate:models
```
Models will be created from the database with column map and all relations.
You're now able to access to SCRUD action for all model, example for User :
```
http//localhost/scrud/User/read?id=1
http//localhost/scrud/User/create
http//localhost/scrud/User/search
```
You can merge all model which has one relation like User and UserSocial like this :
```
http//localhost/scrud/User UserSocial/read?id=1
http//localhost/scrud/User UserSocial/create
http//localhost/scrud/User UserSocial/search
```
So you can set as model as hasOne relations exists.

### Generate controllers and actions
You can specify one or more action associated with the controller, by default the views associated with the actions are created but the option --no_view=true block this.
```
phalcon generate:controller home index,test
```
It wiil create the home controller with the index et test actions with their views.

### Generate Less/Css and Js
The asset manager set default collection css and js, a main css file that should be generated by less and 3 js files, jquery, a class on the module/ation and main that initiate it.
These collection are include inside the default layout.
For each module action, you can generate css/less and js file on the same way as the controller :
```
phalcon generate:css home index,test
phalcon generate:js home index,test
```
It will copy the type template in public/[type]/[moduleName]/[actionName] or in the [moduleName] root folder for the index action.
For the css minifier, I use sublime with Less2Css that compile and minify less file on save but there a command to do that using lessc node mdole (sudo npm install lessc -g)
```
phalcon generate:less home
phalcon generate:less home index,test
```
The first command will get all main.less inside the home module and compile/minify them.
The second command will only do the job for the actions listed

### Translation
Inide each view you can use $t->_ or $t->__ to get translation from key inside array from folder messages. The language used corresponding to the browser language using en by default.
$t->_ is used to acces direct key value from the array and $t->__ is more specific for the same key but inside the controller / action context.
```
view inside controller user action login
$t->_('hi') // $messages['hi']
$t->__('hi') // $mesages['user_login_hi']
```
