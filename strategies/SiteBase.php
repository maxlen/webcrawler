<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 8.12.16
 * Time: 11:21
 */

namespace maxlen\webcrawler\strategies;


use maxlen\webcrawler\Client;
use maxlen\webcrawler\interfaces\CrawlStrategy;

abstract class SiteBase implements CrawlStrategy
{
    public $params = [];

    public $url;

    public $domain;

    public $scheme;

    public $result =[];

    public function search($params)
    {

    }

    protected function validateParams($params)
    {
        if (!isset($params['url'])) {
            throw new InvalidConfigException('You should set url');
        }
    }

    protected function setParamsForRequest($params)
    {
        $result = [];

        $this->setDomain($params['url']);
        $this->setScheme($params['url']);

        if (!empty($params['proxy'])) {
            $result['curl'] = [
                CURLOPT_PROXY => $params['proxy']['host'],
                CURLOPT_PROXYPORT => $params['proxy']['port'],
                CURLOPT_PROXYUSERPWD => "{$params['proxy']['login']}:{$params['proxy']['password']}",
             ];
        }

        return $result;
    }

    private function setDomain($url)
    {
        $parts = parse_url($url);
        $this->domain = $parts['host'];
    }

    private function setScheme($url)
    {
        $parts = parse_url($url);
        $this->scheme = $parts['scheme'];
    }

    protected function getAbsoluteUrl($url)
    {
        $hrefDomain = $this->getDomain($url);

        if (is_null($hrefDomain)) {
            if (is_null($hrefDomain)) {
                $url = (strpos($url, '/') !== FALSE && strpos($url, '/') == 0) ? "{$this->domain}{$url}" : "{$this->domain}/{$url}";
            } else {
                $url = self::cleanDomain($url);
            }

            $url = $this->scheme . '://' . $url;
        }

        return $url;
    }

    private function getDomain($url, $isDom = false, $saveWww = false)
    {

        if ($isDom) {
            $parse = parse_url($url);
            $domain = (isset($parse['host']) && !is_null($parse['host'])) ? $parse['host'] : rtrim($url, '/');
        } else {
            $parse = parse_url($url);
            $domain = (isset($parse['host']) && !is_null($parse['host'])) ? $parse['host'] : null;
        }

        if (!is_null($domain)) {
            $domain = $this->cleanDomain($domain, $saveWww);
        }

        return $domain;
    }

    private function cleanDomain($url, $saveWww = false)
    {
        if (strpos($url, 'http://') == 0) {
            $url = str_replace('http://', '', $url);
        }

        if (!$saveWww && strpos($url, 'www.') == 0) {
            $url = str_replace('www.', '', $url);
        }

        return $url;
    }
}