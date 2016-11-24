<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 24.11.16
 * Time: 10:57
 */

namespace maxlen\webcrawler\strategies;


use maxlen\webcrawler\interfaces\CrawlStrategy;

abstract class SearchEngine implements CrawlStrategy
{
    public $params = [];

    public $query;

    public $page;

    public $result =[];

    public $lang = '';

    const GOOGLE = 'google';
    const YAHOO = 'yahoo';
    const BING = 'bing';
    const YANDEX = 'yandex';
    const ASK = 'ask';
    const WOW = 'wow';
    const ECOSIA = 'ecosia';
    const EXALEAD = 'exalead';
    const QWANT = 'qwant';
    const MYWEBSEARCH = 'mywebsearch';
    const GIGABLAST = 'gigablast';
    const AOL = 'aol';

    public function search($params)
    {

    }

    public function getSEUrl($se, $query, $params = [])
    {
        if (isset($params['lang'])) {
            $lang = $params['lang'];
        } else {
            $lang = $this->lang;
        }

        $query = urlencode($query);
        $start = '';

        switch ($se) {
            case self::GOOGLE:
                if (isset($params['page']) && $params['page'] != 0) {
                    $start = "&start=" . ((int) $params['page'] * 10);
                } else {
                    $start = "&start=0";
                }
                $url = "https://www.google.com/search?q={$query}{$start}{$lang}&gws_rd=cr&filter=0";
                break;
            case self::YAHOO:
                if (isset($params['page']) && $params['page'] != 0) {
                    $start = "&b=" . ((int) $params['page'] * 10+1);
                }
                $url = "https://search.yahoo.com/search;_ylt=Apg.fQFBzxhPrDgbbsxmnm2bvZx4;_ylu=X3oDM--;_ylc=X1MDMjc2NjY3OQRfcg?pz=40&fr2=sb-top-search"
                    . "&ei=UTF-8&fr=yfp-t&p={$query}{$start}";
                break;
            case self::BING:
                if (isset($params['page']) && $params['page'] != 0) {
                    $start = "&first=" . ((int) $params['page'] * 10 + 1);
                }
                $url = "http://www.bing.com/search?q={$query}{$start}";
                break;
            case self::ASK:
                $start = (isset($params['page']) && $params['page'] != 0) ? "&page={$params['page']}" : '';
                $url = "http://www.ask.com/web?q={$query}{$start}&o=312&l=dir&qsrc=998&qo=pagination";
                break;
            case self::ECOSIA:
                $start = (isset($params['page']) && $params['page'] != 0) ? "&p={$params['page']}" : '';
                $url = "https://www.ecosia.org/search?q={$query}{$start}";
                break;
            case self::QWANT:
                if (isset($params['page']) && $params['page'] != 0) {
                    $start = "&t=web&offset=" . (int) $params['page'] * 10;
                }
                $url = "https://api.qwant.com/api/search/web?count=10&f=safesearch%3A1"
                    . "&locale=en_us&q={$query}{$start}";
                break;
            case self::MYWEBSEARCH:
                if (isset($params['page']) && $params['page'] != 0) {
                    $start = "&pn=" . ((int) $params['page'] + 1);
                }
                $url = "http://int.search.mywebsearch.com/mywebsearch/GGweb.jhtml?n=&ss=sub"
                    . "&st=hp&tpr=sbt&searchfor={$query}{$start}";
                break;
            case self::EXALEAD:
                if (isset($params['page']) && $params['page'] != 0) {
                    $start = "&start_index=" . ((int) $params['page'] * 40);
                }
                $url = "https://www.exalead.com/search/web/results/?q={$query}&elements_per_page=40{$start}";
                break;
            case self::GIGABLAST:
                $query = trim(str_replace('filetype:pdf', '', $query));
                if (isset($params['page']) && $params['page'] != 0) {
                    $start = "&s=" . (int) $params['page'] * 100;
                }
                $url = "http://www.gigablast.com/search?c=main&index=search&"
                    . "q={$query}&rxivd=1&sb=1&dr=0&sc=0&n=100&qlang=en&showimages=0&"
                    . "filetype=pdf&format=json{$start}";
                break;
            case self::AOL:
                if (isset($params['page']) && $params['page'] != 0) {
                    $start = "&page=" . ((int) $params['page'] + 1);
                }
                $url = "http://search.aol.com/aol/search?s_it=sb-top&s_chn=prt_bon&v_t=comsearch-aolnewtab-t&q={$query}{$start}";
                break;

            default:
                break;
        }

        return $url;
    }
}