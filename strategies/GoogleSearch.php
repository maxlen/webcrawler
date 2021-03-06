<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 24.11.16
 * Time: 10:59
 */

namespace maxlen\webcrawler\strategies;

use GuzzleHttp\Client;


class GoogleSearch extends SearchEngine
{
    public function crawl($params)
    {
        echo PHP_EOL . "GOOGLE";
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
                $item->description = trim(pq($block)->find('span.st')->text());
                $this->result['mainItems'][] = $item;
            }
        }

        $mainItemsAmount = trim(pq('#resultStats')->text());
        if ($mainItemsAmount != '') {
            $mainItemsAmount = explode('(', $mainItemsAmount);
            $mainItemsAmount = preg_replace('~\D~','',$mainItemsAmount[0]);
            $this->result['mainItemsAmount'] = (int) trim($mainItemsAmount);
        }

        return $this->result;
    }

    public function getSEUrl($query, $params = [])
    {
        if (isset($params['lang'])) {
            $lang = $params['lang'];
        } else {
            $lang = $this->lang;
        }

        $query = urlencode($query);
        $start = '';

        if (isset($params['page']) && $params['page'] != 0) {
            $start = "&start=" . ((int) $params['page'] * 10);
        }

        return "https://www.google.com/search?q={$query}{$start}{$lang}&gws_rd=cr&filter=0";
    }
}