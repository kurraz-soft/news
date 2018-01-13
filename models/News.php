<?php
/**
 * Created by
 * User: Kurraz
 * Date: 25.10.2014
 */

namespace app\models;

use yii\base\Model;
use app\components\Curl;
use yii\helpers\HtmlPurifier;
use yii\helpers\VarDumper;

require_once(\Yii::getAlias('@app/components/phpQuery.php'));

class News extends Model
{
    const SITE_URL = 'http://podrobnosti.ua';

    public $title;
    public $date;
    public $text;
    public $detail_url;
    public $is_hot = false;

    /**
     * @return News
     * @throws \Exception
     */
    static public function loadMain()
    {
        $curl = new Curl();
        $curl->url(self::SITE_URL)->execute();
        $html = $curl->result;
        $doc = \phpQuery::newDocument(($html));

        $block = $doc->find('.main-holder .content div:first');
        $news = new News();
        $news->title = \phpQuery::pq($block)->find('h3')->text();
        $news->date = \phpQuery::pq($block)->find('.date')->text();
        $news->text = \phpQuery::pq($block)->find('[itemprop="description"]')->text();
        $news->detail_url = base64_encode(\phpQuery::pq($block)->find('a:first')->attr('href'));

        $doc->unloadDocument();
        return $news;
    }

    /**
     * @return News[]
     * @throws \Exception
     */
    static public function loadList()
    {
        $curl = new Curl();
        $curl->url(self::SITE_URL)->execute();
        $html = $curl->result;
        $doc = \phpQuery::newDocument(($html));

        $tags = $doc->find('.main-holder .sidebar .tab:first li');
        $ret = array();
        foreach($tags as $tag)
        {
            $news = new News();
            $news->title = \phpQuery::pq($tag)->find('h3')->text();
            $news->date = \phpQuery::pq($tag)->children('.date')->text();
            $news->detail_url = base64_encode(\phpQuery::pq($tag)->find('a')->attr('href'));
            $news->is_hot = \phpQuery::pq($tag)->hasClass('important');

            $ret[] = $news;
        }

        $doc->unloadDocument();
        return $ret;
    }

    /**
     * @param $url
     * @return News
     * @throws \Exception
     */
    static public function loadDetail($url)
    {
        $cache = \Yii::$app->cache;

        $news = $cache->getOrSet($url,function() use ($url) {
            $curl = new Curl();
            $curl->url(self::SITE_URL.$url)->execute();
            $html = $curl->result;
            $doc = \phpQuery::newDocument($html);

            $block = $doc->find('.print_container');
            $news = new News();
            $news->title = \phpQuery::pq($block)->find('h1:first')->text();
            $news->date = \phpQuery::pq($block)->find('.article-head .author')->text();
            $news->text = HtmlPurifier::process(\phpQuery::pq($block)->find('[itemprop="description"]')->html(),function($config){
                $config->set('HTML.Allowed','p');
            });

            $doc->unloadDocument();
            return $news;
        });

        return $news;
    }
} 