<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 25.11.16
 * Time: 14:23
 */

namespace maxlen\webcrawler\strategies;

use GuzzleHttp\Client;


class BingSearch extends SearchEngine
{
    public function crawl($params)
    {
        $this->result = [];
        $client = new Client();

        $res = $client->request(
            'GET',
            $this->getSEUrl($params['query'], $params),
            $this->setParamsForRequest($params)
        );

        if ($res->getStatusCode() != 200) {
            return false;
        }

        $body = $res->getBody();

        \phpQuery::newDocumentHTML($body);
        $blocks = pq("#b_results li.b_algo");

        if (count($blocks) == 0) {
            return $this->result;
        }

        foreach ($blocks as $block) {
            $link = trim(pq($block)->find('h2 a')->attr('href'));
            if ($link != '') {
                $item = new \stdClass();
                $item->link = $link;
                $item->title = trim(pq($block)->find('h2 a')->text());
                $item->description = trim(pq($block)->find('div.b_caption p')->text());
                $this->result['mainItems'][] = $item;
            }
        }

        $mainItemsAmount = trim(pq('#b_content #b_tween span.sb_count')->text());
        if ($mainItemsAmount != '') {
            $mainItemsAmount = preg_replace('~\D~','',$mainItemsAmount);
            $this->result['mainItemsAmount'] = (int) trim($mainItemsAmount);
        }

        return $this->result;
    }

    public function getSEUrl($query, $params = [])
    {
        $query = urlencode($query);
        $start = '';

        if (isset($params['page']) && $params['page'] != 0) {
            $start = "&first=" . ((int) $params['page'] * 10 + 1);
        }

        return "https://www.bing.com/search?q={$query}{$start}";
    }
}