<?php
namespace ImageStack\ImageBackend;

use ImageStack\Image;
use ImageStack\Api\ImageBackendInterface;
use ImageStack\OptionnableTrait;
use ImageStack\Api\ImagePathInterface;
use ImageStack\ImageBackend\Exception\ImageBackendException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use ImageStack\Api\Exception\ImageNotFoundException;

/**
 * HTTP image backend.
 * Fetch images from HTTP server.
 *
 */
class HttpImageBackend implements ImageBackendInterface {
    use OptionnableTrait;
    
    /**
     * HTTP image backend constructor.
     * @param string $rootUrl the root URL to look after images
     * @param array $options
     *   - curl: array of CURL options
     *   - allow_empty_root_url: the HTTP image backen will have no root URL so it can handle only full path in fetchImage().
     *   - use_prefix : prepend stack prefix (default: false)
     *   - intercept_exception: intercept all guzzle exception to throw an image not found exception (default: false)
     */
    public function __construct($rootUrl, $options = array()) {
        $this->setOptions($options);
        if (!$this->getOption('allow_empty_root_url', false)) {
            if (empty($rootUrl)) {
                throw new \InvalidArgumentException('root URL cannot be empty or use option allow_empty_root_url');
            }
        }
        if ($rootUrl) {
            if (!filter_var($rootUrl, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
                throw new \InvalidArgumentException('root URL is invalid');
            }
        }
        
        $this->setOption('root_url', $rootUrl);
    }
    
    /**
     * Get the image URL.
     * @param ImagePathInterface $path
     * @return string
     */
    protected function getImageUrl(ImagePathInterface $path) {
        $url = $path->getPath();
        if ($this->getOption('use_prefix', false)) {
            $url = rtrim($path->getPrefix(), '/') . '/' . ltrim($url, '/');
        }
        if ($this->getOption('root_url')) {
            $url = rtrim($this->getOption('root_url'), '/') . '/' . ltrim($url, '/');
        }
        return filter_var($url, FILTER_SANITIZE_URL);
    }
    
    public function fetchImage(ImagePathInterface $path) {
        $content = null;
        $url = $this->getImageUrl($path);
        $this->httpRequest($url, $content);
        return new Image($content);
    }

    /**
     * Perform HTTP query.
     * @param string $url
     * @param string &$content
     * @return string|null MIME type
     */
    protected function httpRequest($url, &$content)
    {
        $client = new Client([
            'allow_redirects' => true,
            'curl' => $this->getOption('curl', []),
        ]);
        try {
            $response = $client->request('GET', $url);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                if ($e->getResponse()->getStatusCode() == 404) {
                    throw new ImageNotFoundException(sprintf('Image Not Found : %s', $url), 0, $e);
                }
            }
            if ($this->getOption('intercept_exception', false)) {
                throw new ImageNotFoundException(sprintf('Image Not Found : %s', $url), 0, $e);
            } else {
                throw new ImageBackendException(sprintf("Cannot read file : %s", $url), ImageBackendException::CANNOT_READ_FILE, $e);
            }
        }
        $content = (string)($response->getBody());
        $contentType = $response->getHeader('content-type');
        return isset($contentType[0]) ? $contentType[0] : null;
    }
    
}
