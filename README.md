# ImageStack
A PHP image serving framework.

The main goal is to provide a robust framework to create an "on the fly" image thumbnailer generator similar to [imagecache](https://www.drupal.org/project/imagecache) / [image style](https://www.drupal.org/docs/8/core/modules/image/working-with-images) in [Drupal](https://www.drupal.org/).

This project is only the framework part. The framework is brick designed so that you can put together your own image serving project and add special bricks.

See [SilexImageStack](https://github.com/quazardous/SilexImageStack) and [SilexImageStackDemo](https://github.com/quazardous/SilexImageStackDemo) for more infos.

## Installation

    composer require quazardous/imagestack

## Concept
### Typical flow
This describes the typical processing we want to achieve.

When the **front controller** handles an image HTTP request:

  - the **front controller** will call **image stack** with the **image path**.
  - the **image stack** will fetch the image using the **image backend**.
  - the **image stack** will apply **image manipulator**.
  - the **image stack** will store the image using the **storage backend**.
  - the **image stack** will return the image to the **front controller**.
  - the **front controller** will serve the image.

Ideally the image file will be stored so that the next HTTP request will be statically served.

### Lexicon / bricks

#### Image path
The path of the image that could come from the front controller.

See `ImageStack\Api\ImagePathInterface`.

#### Image
The image content we are willing to serve.

See `ImageStack\Api\ImageInterface`.

#### Image backend
Something that can provide image content.

See `ImageStack\Api\ImageBackendInterface`.

#### Image manipulator
Service that can modify/transform/optimize the image content.

See `ImageStack\Api\ImageManipulatorInterface`.

#### Storage backend
Something where we can store the image content.  
Typically we could store it onto the file system so that next HTTP query will be statically serve.

See `ImageStack\Api\StorageBackendInterface`.

#### Image stack
Top of the iceberg class.

See `ImageStack\Api\ImageStackInterface`.

## Usage

### Code example

This pseudo controller creates an image stack that will:
  - fetch image from `https://images.example.com/backend/` + `$path`
  - optimize it with `jpegtran`
  - store it on `'/var/www/my/local/image/storage/'` + `$path`
  - serve it


```php
function myImageController($path)
{
    $stack = new \ImageStack\ImageStack(
        new \ImageStack\ImageBackend\HttpImageBackend('https://images.example.com/backend/'),
        new \ImageStack\StorageBackend\FileStorageBackend('/var/www/my/local/image/storage/'));
    $oim = new \ImageStack\ImageManipulator\OptimizerImageManipulator();
    $oim->registerImageOptimizer(new \ImageStack\ImageOptimizer\JpegtranImageOptimizer());
    $stack->addImageManipulator($oim);
    $image = $stack->stackImage(new \ImageStack\ImagePath($path));
    return $image->getBinaryContent();
}

```

### Implementation

#### Image stack
A basic image stack.

See `ImageStack\ImageStack`

#### Image backends
##### File image backend
See `ImageStack\ImageBackend\FileImageBackend`

##### HTTP image backend
See `ImageStack\ImageBackend\HttpImageBackend`

##### Cache image backend
Add a cache layer before an image backend.

See `ImageStack\ImageBackend\CacheImageBackend`

##### Sequential image backend
Sequentially fetch image from a queue of image backends and return the first match.

See `ImageStack\ImageBackend\SequentialImageBackend`

#### Image manipulators
##### Converter image manipulator
Convert image type.

See `ImageStack\ImageManipulator\ConverterImageManipulator`

##### Optimizer image manipulator
Optimize image.

See `ImageStack\ImageManipulator\OptimizerImageManipulator`

`jpegtran` wrapper, see `ImageStack\ImageOptimizer\JpegtranImageOptimizer`. 

`pngcrush` wrapper, see `ImageStack\ImageOptimizer\PngcrushImageOptimizer`. 

##### Thumbnailer image manipulator
Create an image thumbnail with (path) rules.

See `ImageStack\ImageManipulator\ThumbnailerImageManipulator`

Path pattern rule, see `ImageStack\ImageManipulator\ThumbnailRule\PatternThumbnailRule`

You can associate path patterns to thumbnail formats:

```php
$rules = [
    '|^images/styles/big/|' => '<500x300', // resize to fit in 500x300 box
    '|^images/styles/box/|' => '200x200', // resize/crop to 200x200 box
    '|^images/styles/list/|' => '100', // resize/crop to 100x100 box
    '|^images/([0-9]+)x([0-9]+)/|' => function ($matches) { return "{$matches[1]}x{$matches[2]}"; }, // custom resize/crop
    '|.*|' => false, // trigger an image not found exception if nothing matches
];

$tim = new \ImageStack\ImageManipulator\ThumbnailerImageManipulator(new \Imagine\Gd\Imagine());

foreach ($rules as $pattern => $format) {
    $tim->addThumbnailRule(new \ImageStack\ImageManipulator\ThumbnailRule\PatternThumbnailRule($pattern, $format));
}

// this will resize the given image to fit in a 500x300 box
$tim->manipulateImage($image, new ImagePath('images/styles/big/photo.jpg'));

// this will resize/crop the given image to a 200x200 box
$tim->manipulateImage($image, new ImagePath('images/200x150/photo.jpg'));

// this will rise a 404
$tim->manipulateImage($image, new ImagePath('bad/path/photo.jpg'));

```

### Tests

```
git clone git@github.com:quazardous/ImageStack.git
cd ./ImageStack
cp tests/config-dist.php tests/config.php
```
Edit/adapt `tests/config.php`.

```
composer.phar update -dev
phpunit.phar
```

## Dependencies / support
Tested against [PHP 5.6](http://php.net/) and [PHP 7.1](http://php.net/).

Current implementation uses [Imagine](https://github.com/avalanche123/Imagine).

You will need either `php-gd`, `php-gmagick` or `php-imagick`.

Wrappers for external optimizer:
  - [jpegtran](http://jpegclub.org/jpegtran/)
  - [pngcrush](https://pmt.sourceforge.io/pngcrush/)

## History

  - 1.0: current version
  - 0.9: pre version mainly a raw copy/paste from an old project

## Credits
[quazardous](https://github.com/quazardous) and others.

## License
MIT
