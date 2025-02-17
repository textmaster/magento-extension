<?php
/**
 * Curl Client HTTP Class
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\HTTP\Client;

use Magento\Framework\HTTP\Client\Curl as MagentoClientCurl;

/**
 * Add method put to Magento Curl Class
 */
class Curl extends MagentoClientCurl
{
    /**
     * Make GET request
     *
     * @param string $uri uri relative to host, ex. "/index.php"
     * @return void
     */
    public function get($uri)
    {
        $this->setOptions([]);
        parent::get($uri);
    }

    /**
     * Make POST request
     *
     * @param string $uri
     * @param array|string $params
     * @return void
     *
     * @see \Magento\Framework\HTTP\Client#post($uri, $params)
     */
    public function post($uri, $params)
    {
        $this->setOptions([]);
        parent::post($uri, $params);
    }

    /**
     * Make PUT request
     *
     * @param string $uri
     * @param array|string $params
     * @return void
     */
    public function put($uri, $params)
    {
        $this->setOption(CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);
        $this->makeRequest("PUT", $uri, $params);
    }
}
