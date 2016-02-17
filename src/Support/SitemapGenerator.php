<?php
namespace Birdmin\Support;

use Birdmin\Core\Application;
use Sunra\PhpSimple\HtmlDomParser;
use Illuminate\Support\Str;

class SitemapGenerator  {

    /**
     * The starting crawl url.
     * @var string
     */
    protected $url;

    protected $host;

    protected $template = "cms::templates/sitemap-xml";

    protected $agent;



    public $cli = true;

    protected $start;
    protected $end;

    protected $messages = [];
    protected $errors = [];

    public $frequency = "daily";
    public $priority = "0.5";


    /**
     * Collection of URLs.
     * @var array|\Illuminate\Support\Collection
     */
    protected $urls = [];

    /**
     * Collection of completed crawled URLs.
     * @var array|\Illuminate\Support\Collection
     */
    protected $crawled = [];

    /**
     * SitemapGenerator constructor.
     * @param null $url
     * @param null $path
     */
    public function __construct($url=null)
    {
        $this->url      = $url?:config('app.url');
        $parts          = parse_url($this->url);
        $this->host     = array_get($parts,'host');

        $this->urls     = collect([]);
        $this->crawled  = collect([]);

        $this->agent    = "Mozilla/5.0 (compatible; Birdmin Sitemap Generator/" . Application::VERSION . ")";
    }

    /**
     * All done.
     */
    public function __destruct()
    {
        $elapsed = round($this->end - $this->start);
        $this->log("Completed in $elapsed seconds.");
        if (count($this->errors)>0)
        {
            $this->log("There were ".count($this->errors)." errors!");
            foreach ($this->errors as $message)
            {
                echo $message."\n";
            }
        }
    }

    /**
     * Begin crawling the site.
     * @param $cli boolean mode
     */
    public function crawl($cli=false)
    {
        $this->cli = $cli;

        $this->start = microtime(true);

        $this->crawlUrl($this->url);

        $this->end = microtime(true);
    }


    /**
     * Crawl and process a single URL.
     * @param $url string
     * @return mixed|null
     */
    protected function crawlUrl($url,$parentUrl=null)
    {
        if (!$url || $this->crawled->search($url) !== false || Str::startsWith($url,"#")) return null;

        $this->log("Crawling URL: ".$url);

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_USERAGENT, $this->agent);
        curl_setopt ($ch, CURLOPT_HEADER, 1);

        $response = curl_exec($ch);

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = $this->parseHeader(substr($response,0,$headerSize));
        $body = substr($response,$headerSize);

        curl_close($ch);

        $this->crawled->push($url);

        if (! $this->validate($header,$body,$url,$parentUrl)) {
            return null;
        };

        $processed = $this->processHtml($url,HtmlDomParser::str_get_html($body));

        $this->add($processed);

        // Recursively crawl other URLs that were found.
        foreach ($processed['urls'] as $href)
        {
            $this->crawlUrl($href,$url);
        }
    }

    /**
     * Validate the response.
     * @param $header
     * @param $body
     * @param $url
     * @param $parentUrl
     * @return bool
     */
    protected function validate($header,$body,$url,$parentUrl)
    {
        $mime = array_get($header,'Content-Type.0',$header['Content-Type']);
        if ($header['StatusCode'] !== 200)
        {
            $this->error("{$header['StatusCode']} Error: '$url'".($parentUrl ? " on '$parentUrl''" : ""));
            return false;
        }

        if (! $body || $mime !== "text/html") {
            return false;
        }

        return true;
    }

    /**
     * Parse a header returned by CURL.
     * @param $string string header
     * @return array
     */
    protected function parseHeader($string)
    {
        $parsed = [];
        $lines = explode("\r\n",trim($string));
        $parsed['Status'] = array_shift($lines);
        $parsed['StatusCode'] =  intval(explode(" ",$parsed['Status'])[1]);

        foreach($lines as $line)
        {
            $parts = explode(":", $line,2);
            $value = trim($parts[1]);
            if (str_contains($value,";")) {
                $value = explode("; ",$value);

            }
            $parsed[trim($parts[0])] = $value;
        }

        return $parsed;
    }

    /**
     * Parse the incoming HTML and check for links.
     * @param $url string
     * @param $dom
     * @return array
     */
    protected function processHtml($url,$dom)
    {
        $title = $dom->find('title')[0]->innertext;
        $meta = collect();
        foreach ($dom->find('meta') as $node) {
            if ($node && $node->name) {
                $meta[$node->name] = $node->content;
            }
        }

        // Crawl other found URLs.
        $urls = array_map(function($item)
        {
            $href = $item->href;
            $parsed = parse_url($href);
            if (!isset ($parsed['host']) && isset($parsed['path'])) $href = url($parsed['path']);
            if (isset($parsed['path'])) {
                if (str_contains($parsed['path'],".")) {
                    return null; // A file.
                }
            }
            if (isset($parsed['scheme']) && $parsed['scheme'] == "javascript") {
                return null;
            }
            if (isset($parsed['host']) && $parsed['host'] !== $this->host) {
                return null;
            }

            return $href;

        }, $dom->find('a'));

        return [
            'url'   => $url,
            'title' => $title,
            'meta'  => $meta,
            'description' => $meta->get('description'),
            'urls'  => $urls?:[]
        ];
    }

    /**
     * Add a url to the collection.
     * @param $object array
     * @return $this
     */
    protected function add($object=[])
    {
        //$this->log('Adding object: '.$object['url']);
        $this->urls->push($object);

        return $this;
    }

    /**
     * Log a message.
     * @param $message
     */
    public function log ($message)
    {
        $message = date(DATE_ATOM)." - ".$message;
        $this->messages[time()] = $message;
        if ($this->cli) {
            echo $message."\n";
        }
    }

    /**
     * Log an error.
     * @param $message string
     */
    public function error($message)
    {
        $this->log($message);
        $this->errors[] = $message;
    }

    /**
     * Save the sitemap xml and robots file.
     * @param null $path
     */
    public function save($path=null)
    {
        if (is_null($path)) $path = base_path();
        if (file_put_contents($path."/sitemap.xml",$this->render())) {
            $this->log("XML File saved to ".$path);
        }
        $robots =[
            "User-agent: *",
            "Sitemap: ".url('sitemap.xml'),
            (env('APP_ENV') !== "production" ? 'Disallow: /' : "")
        ];
        if (file_put_contents($path."/robots.txt", join("\n",$robots))) {
            $this->log("robots.txt saved.");
        };

    }

    /**
     * Render the XML string.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $data = [
            'objects' => $this->urls,
            'generator' => $this,
        ];
        return view($this->template, $data)->render();
    }
}