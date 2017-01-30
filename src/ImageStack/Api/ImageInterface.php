<?php
namespace ImageStack\Api;

/**
 * API image interface.
 *
 */
interface ImageInterface {
    
    /**
     * Get the mime type.
     * @return string the image MIME type.
     */
    public function getMimeType();
    
    /**
     * Get the binary content.
     * @return string the image binary content.
     */
    public function getBinaryContent();
    
    /**
     * Set the binary content.
     * @param string $binaryContent the binary content.
     * @param string $mimeType the mime type.
     * NB: if the mime type is null the implementation should/could guess it
     */
    public function setBinaryContent($binaryContent, $mimeType = null);
    
}