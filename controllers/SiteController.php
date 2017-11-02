<?php

namespace app\controllers;

use app\models\News;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index',array(
            'main' => News::loadMain(),
            'list' => News::loadList(),
        ));
    }

    public function actionDetail($url)
    {
        $url = base64_decode($url);
        $news = News::loadDetail($url);
        $news->detail_url = $url;
        return $this->render('detail',array(
            'news' => $news,
        ));
    }
}
