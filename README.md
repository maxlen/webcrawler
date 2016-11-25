# webcrawler
Search engines crawlers

### Google example:
```
  $proxy = []; //['host' => '*.*.*.*', 'port' => '', 'login' => '', 'password' => '']
  $params = ['query' => 'test search', 'page' => $page, 'proxy' => $proxy];
  $crawler = new WebCrawler(['strategy' => new GoogleSearch()]);
  print_r($crawler->crawl($params));
```
