<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 24.11.16
 * Time: 10:59
 */

namespace maxlen\webcrawler\strategies;

use common\helpers\Object;
use GuzzleHttp\Client;


class GoogleSearch extends SearchEngine
{
    public function crawl($params)
    {
        $this->result = [];
        $client = new Client();

        $res = $client->request(
            'GET',
            $this->getSEUrl(SearchEngine::GOOGLE, $params['query'], $params),
            $this->setParamsForRequest($params)
        );

        var_dump($res->getStatusCode());
        if ($res->getStatusCode() != 200) {
            return false;
        }

        $body = $res->getBody();
        \phpQuery::newDocumentHTML($body);
        $blocks = pq("div.g");

        if (count($blocks) == 0) {
            return $this->result;
        }

        foreach ($blocks as $block) {
            $link = trim(pq($block)->find('h3 a')->attr('href'));
            if ($link != '') {
                $item = new \stdClass();
                $item->link = explode('&sa=U', trim($link, '/url?q='))[0];
                $item->title = trim(pq($block)->find('h3 a')->text());
                $item->descriptions = trim(pq($block)->find('span.st')->text());
                $this->result['mainItems'][] = $item;
            }
        }


        return $this->result;
    }
}