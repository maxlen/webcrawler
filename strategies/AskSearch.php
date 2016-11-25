<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 25.11.16
 * Time: 14:23
 */

namespace maxlen\webcrawler\strategies;

use GuzzleHttp\Client;


class AskSearch extends SearchEngine
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

        $blocks = pq('div.web-result.ur');

        if (count($blocks) == 0) {
            return $this->result;
        }

        foreach ($blocks as $block) {
            $link = trim(pq($block)->find('h2 a')->attr('href'));
            if ($link != '') {
                $item = new \stdClass();
                $item->link = $link;
                $item->title = trim(pq($block)->find('h2 a')->text());
                $item->description = trim(pq($block)->find('.web-result-description')->text());
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

        return "http://www.ask.com/web?q={$query}{$start}&o=312&l=dir&qsrc=998&qo=pagination";
    }
}