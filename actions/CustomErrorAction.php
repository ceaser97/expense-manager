<?php

namespace app\actions;

use yii\web\ErrorAction;

class CustomErrorAction extends ErrorAction
{
    public $layout = 'error'; // Define a custom layout for error pages
    public $errorAssets = null; // Define custom assets for error pages

    public function run()
    {
        // Register the custom assets
        if ($this->errorAssets !== null) {
            $this->errorAssets::register(\Yii::$app->view);
        }

        // Set the custom layout
        \Yii::$app->controller->layout = $this->layout;

        // Run the default behavior
        return parent::run();
    }
}
