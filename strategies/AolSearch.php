<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 24.11.16
 * Time: 10:59
 */

namespace maxlen\webcrawler\strategies;

use GuzzleHttp\Client;


class AolSearch extends SearchEngine
{
    public function crawl($params)
    {
        echo PHP_EOL . "AOL";
        $this->result = [];
        $client = new Client();

        var_dump($params['query']);

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
        $blocks = pq("#w div.ALGO ul > li");

        if (count($blocks) == 0) {
            return $this->result;
        }

        foreach ($blocks as $block) {
            $link = trim(pq($block)->find('h3 a')->attr('href'));
            if ($link != '') {
                $item = new \stdClass();
                $item->link = $link;
                $item->title = trim(pq($block)->find('h3 a')->text());
                $item->description = trim(pq($block)->find('p:eq(1)')->text());
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

        return "https://search.aol.com/aol/search?v_t=na&s_it=sb-home&q={$query}{$start}&oreq=";
    }
}