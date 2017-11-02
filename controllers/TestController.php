<?php
/**
 * Created by PhpStorm.
 * User: Kurraz
 */

namespace app\controllers;


use yii\web\Controller;

class TestController extends Controller
{
    public function actionIndex()
    {
        phpinfo();
    }
}