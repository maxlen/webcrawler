<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 24.11.16
 * Time: 10:57
 */

namespace maxlen\webcrawler;


use maxlen\webcrawler\interfaces\CrawlStrategy;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Object;

class WebCrawler extends Object
{
    /**
     * @var CrawlStrategy
     */
    protected $strategy;

    /**
     * @param CrawlStrategy $strategy
     */
    public function setStrategy(CrawlStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function crawl($params = [])
    {
        return $this->strategy->crawl($params);
    }

    public function init()
    {
        if (!$this->strategy) {
            throw new InvalidConfigException('You should set strategy');
        }
    }
}