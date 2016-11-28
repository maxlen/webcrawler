<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 24.11.16
 * Time: 10:59
 */

namespace maxlen\webcrawler\strategies;

use GuzzleHttp\Client;


class WowSearch extends SearchEngine
{
    public function crawl($params)
    {
        echo PHP_EOL . "WOW";
        $this->result = [];
        $client = new Client();

        $res = $client->request(
            'GET',
            $this->getSEUrl($params['query'], $params),
            $this->setParamsForRequest($params)
        );

        var_dump($res->getStatusCode());
        if ($res->getStatusCode() != 200) {
            return false;
        }

        $body = $res->getBody();

        \phpQuery::newDocumentHTML($body);

        $blocks = pq("div#c div.ALGO ul li");

        if (count($blocks) == 0) {
            return $this->result;
        }

        foreach ($blocks as $block) {
            $link = trim(pq($block)->find('h3 a')->attr('href'));
            if ($link != '') {
                $item = new \stdClass();
                $item->link = $link;
                $item->title = pq($block)->find('h3 a')->text();
                $item->description = pq($block)->find('p[property="f:desc"]')->text();
                $this->result['mainItems'][] = $item;
            }
        }

        return $this->result;
    }

    public function getSEUrl($query, $params = [])
    {
        $query = urlencode($query);
        $start = '';

        if (isset($params['page']) && $params['page'] != 0) {
            $start = "&page=" . ((int) $params['page'] + 1);
        }

        return "http://search.wow.com/search?v_t=splus-hda&q={$query}&s_it=sb-top{$start}&oreq=";
    }
}