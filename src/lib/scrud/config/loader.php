<?
$loader->registerClasses([
    'ScrudController' => $config->application->libDir.'scrud/ScrudController.php',
    'Rest' => $config->application->libDir.'../Tools/Rest.php',
    'Phalcon\Builder\Form' => $config->application->libDir.'../Builder/Form.php'
], true);