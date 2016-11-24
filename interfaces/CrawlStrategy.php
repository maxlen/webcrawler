<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 24.11.16
 * Time: 11:00
 */

namespace maxlen\webcrawler\interfaces;


interface CrawlStrategy
{
    public function crawl($params);
}