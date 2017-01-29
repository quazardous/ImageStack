<?php
namespace ImageStack\Cache;

use Doctrine\Common\Cache\Cache;
use ImageStack\Cache\Exception\RawFileCacheException;
use ImageStack\OptionnableTrait;

/**
 * Raw File Cache.
 * Cached items are stored in a directory tree.
 * Data are stored without serialization when possible (is_string()).
 * A resource file located in a parallel directory tree will contain metadata (expiration, serialized).
 * 
 * Some characters are forbidden in cache IDs:
 * Linux:
 *   0x00 (NUL)
 * 
 * Windows:
 *   < (less than)
 *   > (greater than)
 *   : (colon - sometimes works, but is actually NTFS Alternate Data Streams)
 *   " (double quote)
 *   | (vertical bar or pipe)
 *   ? (question mark)
 *   * (asterisk)
 *   All control codes (0x00-0x1F, 0x7F)
 *
 * Directory separators in cache Ids will create folders:
 *   / (forward slash)
 *   \ (backslash)
 */
class RawFileCache implements Cache
{
    use OptionnableTrait;
    
    /**
     * @param string $root
     * @param array $options
     * Options:
     *  - metadata_root: the root dir for metadata (default: <root>/.cache_metadata)
     *  - dir_mode: mkdir() mode (default: 0755)
     * @throws \InvalidArgumentException
     */
    public function __construct($root, array $options = [])
    {
        if (empty($root)) {
            throw new \InvalidArgumentException('Root cannot be empty');
        }
        $this->setOption('root', $root);
    }
    
    /**
     *
     * {@inheritdoc}
     *
     * @see \Doctrine\Common\Cache\Cache::fetch()
     */
    public function fetch($id)
    {
        $filename = $this->getFilename($id);
        if (!is_file($filename)) return false;
        if (is_dir($filename)) return false;
        $metadata = $this->readMetadata($id);
        if ($this->metadataCheckHasExpired($metadata)) return false;
        $data = file_get_contents($filename);
        if (false === $data) {
            throw new RawFileCacheException(sprintf('Cannot read file : %s', $filename), RawFileCacheException::CANNOT_READ_FILE);
        }
        if ($this->metadataCheckIsSerialized($metadata)) {
            if (false !== ($res = unserialize($data))) {
                $data = $res;
            }
        }
        return $data;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Doctrine\Common\Cache\Cache::contains()
     */
    public function contains($id)
    {
        $filename = $this->getFilename($id);
        if (!is_file($filename)) return false;
        if (is_dir($filename)) return false;
        $metadata = $this->readMetadata($id);
        return !$this->metadataCheckHasExpired($metadata);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Doctrine\Common\Cache\Cache::save()
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $filename = $this->getFilename($id);
        $this->assertDirectory($filename);
        $this->assertFile($filename);
        $metadata = [];
        $metadata['serialized'] = !is_string($data);
        $metadata['expiration'] = $lifeTime > 0 ? time() + $lifeTime : 0;
        if ($metadata['serialized']) {
            $data = serialize($data);
        }
        if (false === file_put_contents($filename, $data)) {
            throw new RawFileCacheException(sprintf('Cannot write file : %s', $filename), RawFileCacheException::CANNOT_WRITE_FILE);
        }
        $this->writeMetadata($id, $metadata);
        return true;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Doctrine\Common\Cache\Cache::delete()
     */
    public function delete($id)
    {
        $filename = $this->getFilename($id);
        if ($this->assertFile($filename)) {
            if (false === unlink($filename)) {
                throw new RawFileCacheException(sprintf('Cannot unlink file : %s', $filename), RawFileCacheException::CANNOT_UNLINK_FILE);
            }
        }
        $filename = $this->getFilename($id, true);
        if ($this->assertFile($filename)) {
            if (false === unlink($filename)) {
                throw new RawFileCacheException(sprintf('Cannot unlink file : %s', $filename), RawFileCacheException::CANNOT_UNLINK_FILE);
            }
        }        
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Doctrine\Common\Cache\Cache::getStats()
     */
    public function getStats()
    {
        // not implemented
        return null;
    }
    
    /**
     * Assert that cache ID is usable as file path.
     * @param string $id
     * @throws \InvalidArgumentException
     */
    protected function assertValidId($id)
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $bad = preg_match('/[\x00-\x1F\x7F]\<\>\:\"\|\?\*]/', $id);
        } else {
            $bad = preg_match('/\x00/', $id);
        }
        if ($bad) {
            throw new RawFileCacheException('Invalid character found in cache ID', RawFileCacheException::INVALID_CHARACTER);
        }
    }
    
    /**
     * Get the filename for the given ID.
     * @param string $id
     * @param string $metadata get the metadata filename
     * @return string
     */
    protected function getFilename($id, $metadata = false)
    {
        $this->assertValidId($id);
        if ($metadata) {
            $root = $this->getOption('metadata_root', $this->getOption('root') . DIRECTORY_SEPARATOR . '.cache_metadata');
        } else {
            $root = $this->getOption('root');
        }
        return rtrim(sanitize_path($root . DIRECTORY_SEPARATOR . $id), DIRECTORY_SEPARATOR);
    }
    
    /**
     * Read metadata.
     * @param string $id
     * @param array $metadata
     * @throws RawFileCacheException
     */
    protected function writeMetadata($id, array $metadata)
    {
        $filename = $this->getFilename($id, true);
        $this->assertDirectory($filename);
        $this->assertFile($filename);
        if (false === file_put_contents($filename, serialize($metadata))) {
            throw new RawFileCacheException(sprintf('Cannot write file : %s', $filename), RawFileCacheException::CANNOT_WRITE_FILE);
        }
    }

    /**
     * Write metadata.
     * @param string $id
     * @throws RawFileCacheException
     * @return array
     */
    protected function readMetadata($id)
    {
        $filename = $this->getFilename($id, true);
        $this->assertFile($filename);
        if (is_file($filename)) {
            if (false === ($data = file_get_contents($filename))) {
                throw new RawFileCacheException(sprintf('Cannot read file : %s', $filename), RawFileCacheException::CANNOT_READ_FILE);
            }
            return (array)unserialize($data);
        }
        return [];
    }
    
    /**
     * Assert that the parent directory can or will exist.
     * @param string $filename
     * @throws RawFileCacheException
     */
    protected function assertDirectory($filename)
    {
        $dirname = dirname($filename);
        if (!is_dir($dirname)) {
            if (is_file($dirname)) {
                throw new RawFileCacheException(sprintf('File with same name : %s', $dirname), RawFileCacheException::FILE_WITH_SAME_NAME);
            }
            @mkdir($dirname, $this->getOption('dir_mode', 0755), true);
            if (!is_dir($dirname)) {
                throw new RawFileCacheException(sprintf('Cannot create directory : %s', $dirname), RawFileCacheException::CANNOT_CREATE_DIRECTORY);
            }
        }
    }
    
    /**
     * Assert that the filename can be used.
     * @param string $filename
     * @throws RawFileCacheException
     * @return boolean
     */
    protected function assertFile($filename)
    {
        if (is_dir($filename)) {
            throw new RawFileCacheException(sprintf('Directory with same name : %s', $filename), RawFileCacheException::DIRECTORY_WITH_SAME_NAME);
        }
        return is_file($filename);
    }
    
    /**
     * Check if data has expired.
     * @param array $metadata
     * @return boolean
     */
    protected function metadataCheckHasExpired(array $metadata)
    {
        if (!isset($metadata['expiration'])) return true;
        if (0 == $metadata['expiration']) return false;
        return $metadata['expiration'] < time();
    }
    
    /**
     * Check if data is serialized.
     * @param array $metadata
     * @return boolean
     */
    protected function metadataCheckIsSerialized(array $metadata)
    {
        if (!isset($metadata['serialized'])) return false;
        return boolval($metadata['serialized']);
    }
}