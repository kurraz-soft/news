<?php
/**
 * @var $this yii\web\View
 * @var app\models\News $main
 * @var app\models\News[] $list
 */

use \yii\helpers\Url;

$this->title = 'NewsTest';
?>
<div class="row">
    <div class="col-sm-6">
        <h3><a href="<?= Url::to(['site/detail','url'=>$main->detail_url]) ?>"><?= $main->title ?></a></h3>
        <p><?= $main->date ?></p>
        <p><?= $main->text ?></p>
    </div>
    <div class="col-sm-6">
        <ul>
            <?php foreach($list as $item): ?>
            <li>
                <h5>
                    <?= $item->is_hot?'<strong>':'' ?>
                    <?= \yii\helpers\Html::a($item->title,['site/detail','url'=>$item->detail_url]) ?>
                    <?= $item->is_hot?'</strong>':'' ?>
                    <?= $item->date ?>
                </h5>
            </li>
            <?php endforeach ?>
        </ul>
    </div>
</div>
