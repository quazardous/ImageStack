<?php
namespace ImageStack\ImageBackend;

use ImageStack\Image;
use ImageStack\Api\ImageBackendInterface;
use ImageStack\OptionnableTrait;
use ImageStack\Api\ImagePathInterface;
use ImageStack\ImageBackend\Exception\ImageBackendException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
	 *   - curl : array of CURL options
	 */
	public function __construct($rootUrl, $options = array()) {
	    if (!filter_var($rootUrl, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
	        throw new \InvalidArgumentException('root URL cannot be empty');
	    }
		$this->setOptions($options);
		$this->setOption('root_url', $rootUrl);
	  
	}
	
	/**
	 * Get the image URL.
	 * @param ImagePathInterface $path
	 * @return string
	 */
	protected function getImageUrl(ImagePathInterface $path) {
	   return filter_var(rtrim($this->getOption('root_url'), '/') . '/' . $path->getPath(), FILTER_SANITIZE_URL);
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
                    throw new ImageBackendException(sprintf('Image Not Found : %s', $url), ImageBackendException::IMAGE_NOT_FOUND, $e);
                }
            }
            throw new ImageBackendException(sprintf("Cannot read file : %s", $url), ImageBackendException::CANNOT_READ_FILE, $e);
        }
        $content = $response->getBody();
        $contentType = $response->getHeader('content-type');
        return isset($contentType[0]) ? $contentType[0] : null;
    }
	
}