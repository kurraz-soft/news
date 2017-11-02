<?php
/**
 * Created by
 * User: Kurraz
 * Date: 25.10.2014
 *
 * @var $this yii\web\View
 * @var NewsTest $news
 */
use app\models\News;
use yii\helpers\Html;

$this->title = $news->title;
?>
<div>
    <div class="help-block">
        <ul class="list-inline">
            <li><?= Html::a('Список',['site/index']) ?></li>
            <li><?= Html::a('Оригинал',News::SITE_URL . $news->detail_url) ?></li>
        </ul>
    </div>
    <h2><?= $news->title ?></h2>
    <?= $news->date ?>
    <hr>
    <p><?= $news->text ?></p>
</div>