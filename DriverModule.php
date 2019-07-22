<?php

namespace backoffice\modules\driver;

use Yii;

/**
 * driver module definition class
 */
class DriverModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'backoffice\modules\driver\controllers';
    public $defaultRoute = 'person-as-driver/create';
    public $name = 'Driver';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        Yii::configure($this, require __DIR__ . '/config/navigation.php');
    }
}
