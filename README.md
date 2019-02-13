A simple library to implement memory-efficient JSON serialization to a PHP stream.

## Usage

### Simple, to STDOUT

```
<?php
require('vendor/autoload.php');
use wrossmann\json_stream\JsonStream;

$js = new JsonStream();
$js->manual_encode('foo');
```

### Fancy, to anything

The first constructor parameter is the stream handle, the second is the flags argument you would otherwise pass to `json_encode()`.

The real benefit to this is being able to append additional stream filters, such as gzip compression.

```
$handle = fopen('zlib://foo.json.gz', 'wb');
$js = new JsonStream($handle, JSON_UNESCAPED_UNICODE);
$js->manual_encode('foo');
```

## Anticipated Memory Usage

Non-complex types are passed directly to `json_encode()` to ensure that they are properly encoded, so the maximum additional memory required should be roughly that of the largest string contained in the object you are serializing.

## Caveats

* Circular references are neither detected nor handled.
* No pretty-printing. Ugly-printing only.
