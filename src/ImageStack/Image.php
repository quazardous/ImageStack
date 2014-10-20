<?php
namespace ImageStack;

use Imagine\Image\ImagineInterface;

/**
 * Simple image abstraction class.
 */
class Image {
	
	protected $type;
	protected $data;
	
	/**
	 * @var \Imagine\Image\ImagineInterface
	 */
	protected $imagine;
	
	/**
	 * @var \Imagine\Image\ImageInterface
	 */
	protected $image;
	
	/**
	 * Get Imagine image.
	 * @return \Imagine\Image\ImageInterface
	 */
	public function getImage() {
	  return $this->image;
	}
	
	/**
	 * @param \Imagine\Image\ImagineInterface $imagine
	 * @param string $type
	 * @param string $data
	 */
	public function __construct(ImagineInterface $imagine, $type = null, $data = null) {
	  $this->imagine = $imagine;
		$this->setType($type);
		$this->setData($data);
	}
	
	/**
	 * @throws \Exception if $data is invalid
	 * @param string $data
	 */
	public function setData($data) {
	  $this->image = $this->imagine->load($data);
		$this->data = $data;
	}
	
	public function &getData() {
		return $this->data;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getMime() {
		switch ($this->getType()) {
			case 'png': return 'image/png';
			case 'jpeg': return 'image/jpeg';
			default: return 'image/' . $this->getType();
		}
	}
}