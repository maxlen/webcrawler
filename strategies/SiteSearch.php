<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 8.12.16
 * Time: 12:21
 */

namespace maxlen\webcrawler\strategies;

use GuzzleHttp\Client;


class SiteSearch extends SiteBase
{
    public function crawl($params)
    {
        echo PHP_EOL . "SITE SEARCH";

        $this->validateParams($params);

        $this->result = [];
        $client = new Client();

        $res = $client->request(
            'GET',
            $params['url'],
            $this->setParamsForRequest($params)
        );

        var_dump($res->getStatusCode());
        if ($res->getStatusCode() != 200) {
            return false;
        }

        $body = $res->getBody();
        \phpQuery::newDocumentHTML($body);
        $links = pq("a");

        if (count($links) == 0) {
            return $this->result;
        }

        foreach ($links as $link) {
            $href = trim(pq($link)->attr('href'));

            $item = new \stdClass();
            $item->absoluteUrl = $this->getAbsoluteUrl($href);
            $item->href = $href;
            $item->title = trim(pq($link)->attr('title'));
            $item->text = trim(pq($link)->text());
            $this->result['links'][] = $item;
        }

        return $this->result;
    }
}