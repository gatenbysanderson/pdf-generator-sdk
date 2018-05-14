# PDF Generator SDK

PHP SDK for communicating with the PDF Generator (see [gatenbysanderson/pdf-generator](https://github.com/gatenbysanderson/pdf-generator)).

## Licence

Copyright &copy; 2018 GatenbySanderson Ltd.

This project is open source software released under the terms of the MIT licence: see [LICENCE.md](LICENCE.md).

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

**Note**: For Laravel projects, consider using the [Laravel PDF Generator](https://github.com/gatenbysanderson/laravel-pdf-generator) instead, which wraps around this SDK with support for compiling and parsing Blade files.

### Prerequisites

* PHP >=5.6

### Installing

Start by adding this repo to your projects composer.json file:

```json
{
  "repositories": [
    {
        "type": "vcs",
        "url":  "git@github.com:gatenbysanderson/pdf-generator-sdk.git",
        "no-api": true
    }
  ]
}
```

You can then simply require the package as with any other:

```
$ composer require gatenbysanderson/pdf-generator-sdk
```

As the PDF Generator client requires a dependency of Guzzle in it's constructor, we can take advantage of a dependency injection container to automatically resolve this for us.
In Laravel this can be done in a Service Provider:

```php
<?php

namespace App\Providers;

use GatenbySanderson\PdfGeneratorSdk\PdfGenerator;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PdfGenerator::class, function () {
            $guzzle = new Client(['base_uri' => config('gs.pdf_generator_url')]);
            return new PdfGenerator($guzzle);
        });
    }
}
```

### Examples

You can now use the container to resolve an instance of the PDF Generator Client for you:

```php
<?php

use GatenbySanderson\PdfGeneratorSdk\PdfGenerator;

// Anywhere
$pdfGenerator = resolve(PdfGenerator::class);
$response = $pdfGenerator->generate(['test.html' => '<h1>Hello</h1>']);

// Controller
public function index(PdfGenerator $pdfGenerator)
{
    $response = $pdfGenerator->generate(['test.html' => '<h1>Hello</h1>']);
}
```

### Generating a PDF

You can generate a PDF by calling the `generate()` method on the PDF Generator instance. This will return an associative array with the following structure:

```php
<?php

$response = [
    'status' => 'success',
    'data' => [
        'type' => 'application/pdf',
        'encoding' => 'base64',
        'content' => 'BASE 64 ENCODED PDF',
    ]
];
```

The `generate()` method takes an array of files with the following structure:

```php
<?php

$files = [
    'filename1.html' => 'FILE_CONTENTS',
    'filename2.html' => 'FILE_CONTENTS',
    //...
];
```

### Config Parameters

When calling the `generate()` method, as well as files, you can pass an array which will also be posted to the server.
This is useful for enabling certain flags, such as JavaScript:

```php
<?php

$pdfGenerator->generate(
    ['test.html' => '<h1>Hello</h1>'], 
    ['javascript' => true]
);
```

## Running the tests

To run the unit tests, begin by copying `.env.example` to `.env`, and set the `PDF_GENERATOR_URL` variable (**with** the protocol and **without** a trailing slash at the end).

You can then run PHPUnit:

```
$ php vendor/bin/phpunit
```

### And coding style tests

The code sniffer can be ran as follows:

```
$ composer gscs
```

## Deployment

It's important to remember to use the correct `base_uri` for the PDF server when binding the Guzzle client in the Service Provider.

## Built With

* [Guzzle](http://docs.guzzlephp.org//) - The HTTP client
* [Composer](https://getcomposer.org/) - Dependency management

## Contributing

All changes must be made through the medium of a pull request.

1. Create a feature branch off of the `develop` branch: `feature/my-awesome-feature`
2. Push your commits to the feature branch
3. Submit a pull request to merge the feature into `develop`
4. Once accepted, `develop` should then be merged into `master`
5. `master` should then be tagged with a new release


## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/gatenbysanderson/pdf-generator-sdk/tags).
