<?php
/**
 * Created by PhpStorm.
 * User: Kurraz
 * Date: 23.03.2016
 * Time: 10:04
 */

namespace app\components;


use app\models\ForexBalanceRow;

require_once(\Yii::getAlias('@app/components/phpQuery.php'));

class ForexUa
{
    const LOGIN_URL = "https://www.myforexltd.com/ru/client/login";

    private $cookie_file;

    public function __construct($login, $pass)
    {
        $this->cookie_file = \Yii::getAlias('@runtime/forexua.txt');

        //login
        $curl = new Curl();
        $curl->url(self::LOGIN_URL)
            ->cookie($this->cookie_file)
            ->cookieFile($this->cookie_file)
            ->ignoreSsl()
            ->post([
                'login' => $login,
                'password' => $pass,
            ])->execute();
    }

    public function getBalance()
    {
        $url = 'https://www.myforexltd.com/ru/managed-accounts';

        $curl = new Curl();
        $curl->url($url)
            ->cookie($this->cookie_file)
            ->cookieFile($this->cookie_file)
            ->ignoreSsl()
            ->execute();

        $out = $curl->result;

        $doc = \phpQuery::newDocument($out);

        $trs = $doc->find('table.tbl-data tr');

        $ret = [];

        $i = 0;
        foreach($trs as $tr)
        {
            $i++;
            if($i == 1) continue;

            $m = new ForexBalanceRow();

            $tds = pq($tr)->find('td');
            $td_i = 0;
            foreach($tds as $td)
            {
                $td_i++;
                switch($td_i)
                {
                    case 3:
                        $m->name = pq($td)->text();
                        break;
                    case 5:
                        $m->invests = pq($td)->text();
                        break;
                    case 6:
                        $m->balance = pq($td)->text();
                        break;
                    case 7:
                        $m->type = pq($td)->text();
                        break;
                }
            }

            $ret[] = $m;
        }

        return $ret;
    }
}