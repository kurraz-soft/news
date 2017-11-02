<?php

use app\models\News;

/**
 * Created by PhpStorm.
 * User: Kurraz
 */
class NewsTest extends \Codeception\Test\Unit
{
    public function testLoadMain()
    {
        $model = News::loadMain();
        expect(strlen($model->title))->greaterThan(0);
    }

    public function testLoadDetail()
    {
        $model = News::loadMain();
        $model = News::loadDetail(base64_decode($model->detail_url));
        expect(strlen($model->title))->greaterThan(0);
    }
}