<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 24.11.16
 * Time: 10:59
 */

namespace maxlen\webcrawler\strategies;

use GuzzleHttp\Client;


class EcosiaSearch extends SearchEngine
{
    public function crawl($params)
    {
        echo PHP_EOL . "ECOSIA";
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

        $blocks = pq("div.result.js-result");

        if (count($blocks) == 0) {
            return $this->result;
        }

        foreach ($blocks as $block) {
            $link = trim(pq($block)->find('a.js-result-title')->attr('href'));
            if ($link != '') {
                $item = new \stdClass();
                $item->link = $link;
                $item->title = trim(pq($block)->find('a.js-result-title')->text());
                $item->description = trim(pq($block)->find('p.result-snippet')->text());
                $this->result['mainItems'][] = $item;
            }
        }

        $mainItemsAmount = pq("div.search-filters-text.left")->text();
        $mainItemsAmount = explode('result', $mainItemsAmount);
        $this->result['mainItemsAmount'] = (int) str_replace(',', '', trim($mainItemsAmount[0]));

        return $this->result;
    }

    public function getSEUrl($query, $params = [])
    {
        $query = urlencode($query);
        $start = (isset($params['page']) && $params['page'] != 0) ? "&p={$params['page']}" : '';
        return "https://www.ecosia.org/search?q={$query}{$start}";
    }
}