<?php
namespace ImageStack\Tests;


use ImageStack\Cache\RawFileCache;

class RawFileCacheTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Root cannot be empty
     */
    public function testRawFileCacheEmptyRoot()
    {
        new RawFileCache('');
    }
    
    public function testRawFileCacheString()
    {
        $root = TESTDIR . '/string';
        $cache = new RawFileCache($root);
        
        $id = 'a/b/c.txt';
        $data = 'Hello World';
        $cache->save($id, $data);
        
        $filename = $root . '/' . $id;
        $this->assertFileExists($filename);
        
        $this->assertStringEqualsFile($filename, $data);
    }
    
    /**
     * @expectedException ImageStack\Cache\Exception\RawFileCacheException
     * @expectedExceptionCode ImageStack\Cache\Exception\RawFileCacheException::INVALID_CHARACTER
     */
    public function testRawFileCacheStringCharacterException()
    {
        $root = TESTDIR . '/string';
        $cache = new RawFileCache($root);
        
        $id = "a/b\x00";
        $data = 'Hello World';
        $cache->save($id, $data);
    }
    
    /**
     * @expectedException ImageStack\Cache\Exception\RawFileCacheException
     * @expectedExceptionCode ImageStack\Cache\Exception\RawFileCacheException::DIRECTORY_WITH_SAME_NAME
     */
    public function testRawFileCacheStringDirectoryException()
    {
        $root = TESTDIR . '/string';
        $cache = new RawFileCache($root);
        
        $id = 'a/b';
        $data = 'Hello World';
        $cache->save($id, $data);
    }

    /**
     * @expectedException ImageStack\Cache\Exception\RawFileCacheException
     * @expectedExceptionCode ImageStack\Cache\Exception\RawFileCacheException::FILE_WITH_SAME_NAME
     */
    public function testRawFileCacheStringFileException()
    {
        $root = TESTDIR . '/string';
        $cache = new RawFileCache($root);
        
        $id = 'a/b/c.txt/d.txt';
        $data = 'Hello World';
        $cache->save($id, $data);
    }
    
    public function testRawFileCacheFullCycle()
    {
        $root = TESTDIR . '/array';
        $cache = new RawFileCache($root);
        
        $id = 'a/b/e.serialized';
        $data = [1, 2, 3];
        $cache->save($id, $data);
        
        $filename = $root . '/' . $id;
        $this->assertFileExists($filename);
        $this->assertStringEqualsFile($filename, serialize($data));
        
        $this->assertTrue($cache->contains($id));
        
        $copy = $cache->fetch($id);
        $this->assertEquals($data, $copy);
        
        $data = new \DateTime();
        $cache->save($id, $data);
        
        $cache->delete($id);
        $this->assertFileNotExists($filename);
        
        $this->assertFalse($cache->contains($id));
    }
    
    public function testRawFileCacheExpiration()
    {
        $root = TESTDIR . '/expiration';
        $cache = new RawFileCache($root);
        
        $id = 'a/b/e.serialized';
        $data = [1, 2, 3];
        $cache->save($id, $data, 1);
        $this->assertTrue($cache->contains($id));
        sleep(2);
        $this->assertFalse($cache->contains($id));
    }
    
    public function testRawFileCacheHashTree()
    {
        $root = TESTDIR . '/tree';
        $level = 3;
        $cache = new RawFileCache($root, ['hash_tree' => $level]);
        
        $id = 'a/b/e.serialized';
        $data = [1, 2, 3];
        $cache->save($id, $data);
        
        $path = '';
        $md5 = md5($id);
        for ($i = 0; $i < $level; ++$i) {
            $path .= $md5[$i] . '/';
        }
        $path .= $id;
        
        $filename = $root . '/' . $path;
        $this->assertFileExists($filename);
        $this->assertStringEqualsFile($filename, serialize($data));
        
        $this->assertTrue($cache->contains($id));
        
        $copy = $cache->fetch($id);
        $this->assertEquals($data, $copy);
        
        $data = new \DateTime();
        $cache->save($id, $data);
        
        $cache->delete($id);
        $this->assertFileNotExists($filename);
        
        $this->assertFalse($cache->contains($id));
    }
    
}