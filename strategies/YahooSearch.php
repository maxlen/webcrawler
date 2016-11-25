<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 24.11.16
 * Time: 10:59
 */

namespace maxlen\webcrawler\strategies;

use GuzzleHttp\Client;


class YahooSearch extends SearchEngine
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
        $blocks = pq("div#main div#web ol li");

        if (count($blocks) == 0) {
            return $this->result;
        }

        foreach ($blocks as $block) {
            $link = trim(pq($block)->find('h3 a')->attr('href'));
            if ($link != '') {
                $item = new \stdClass();
                $item->link = $link;
                $item->title = trim(pq($block)->find('h3 a')->text());
                $item->description = trim(pq($block)->find('div.compText.aAbs p')->text());
                $this->result['mainItems'][] = $item;
            }
        }

        $mainItemsAmount = trim(pq('div.compPagination span:last')->text());
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
            $start = "&b=" . ((int) $params['page'] * 10+1);
        }

        return "https://search.yahoo.com/search?n=10&pz=10&ei=UTF-8&va_vt=any&vo_vt=any&ve_vt=any&vp_vt=any&vst=0&" .
            "vf=all&vm=p&fl=0&fr=yfp-t&vs=&p={$query}{$start}";
//        return "https://search.yahoo.com/search;_ylt=Apg.fQFBzxhPrDgbbsxmnm2bvZx4;_ylu=X3oDM--;_ylc=X1MDMjc2NjY3OQ" .
//            "Rfcg?pz=40&fr2=sb-top-search&ei=UTF-8&fr=yfp-t&p={$query}{$start}";
    }
}