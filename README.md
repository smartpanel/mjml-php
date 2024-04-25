# Convert MJML to HTML using PHP

Forked from spatie/mjml with a downgrade of php version and without sidecar.

## Installation

In your project, or on your server, you must have the JavaScript package [`mjml`](https://github.com/mjmlio/mjml) installed.

```bash
npm install mjml
```

... or Yarn.

```bash
yarn add mjml
```

Make sure you have installed Node 16 or higher.

## Usage

The easiest way to convert MJML to HTML is by using the `toHtml()` method.

```php
use SmartPanel\Mjml\Mjml;

// let's assume $mjml contains the MJML you want to convert

$html = Mjml::new()->toHtml($mjml);
```

If the MJML could not be converted at all a `SmartPanel\Mjml\Exceptions\CouldNotRenderMjml` exception will be thrown.

### Using `convert()`

The `toHtml()` method will just return the converted HTML. There's also a `convert()` method that will return an instance of `SmartPanel\Mjml\MjmlResult` that contains the converted HTML and some metadata.

```php
use SmartPanel\Mjml\Mjml;

// let's assume $mjml contains the MJML you want to convert

$result = Mjml::new()->convert($mjml); // returns an instance of SmartPanel\Mjml\MjmlResult
```

On the returned instance of `SmartPanel\Mjml\MjmlResult` you can call the following methods:

- `html()`: returns the converted HTML
- `array()`: returns a structured version of the given MJML
- `hasErrors()`: returns a boolean indicating if there were errors while converting the MJML
- `errors()`: returns an array of errors that occurred while converting the MJML

The `errors()` method returns an array containing instances of `SmartPanel\Mjml\MjmlError`. Each `SmartPanel\Mjml\MjmlError` has the following methods:

- `line()`: returns the line number where the error occurred
- `message()`: returns the error message
- `formattedMessage()`: returns the error message with the line number prepended
- `tagName()`: returns the name of the tag where the error occurred

### Customizing the rendering

There are various methods you can call on the `Mjml` class to customize the rendering. For instance the `minify()` method will minify the HTML that is returned.

```php
use SmartPanel\Mjml\Mjml;

// let's assume $mjml contains the MJML you want to convert
$minifiedHtml = Mjml::new()->minify()->toHtml($mjml);
```

These are all the methods you can call on the `Mjml` class:

- `minify()`: minify the HTML that is returned
- `beautify()`: beautify the HTML that is returned
- `hideComments()`: hide comments in the HTML that is returned
- `validationLevel(ValidationLevel $validationLevel)`: set the validation level to `strict`, `soft` or `skip`

Instead of using these dedicated methods, you could opt to pass an array with options as the second argument of the `toHtml` or  `convert` method. You can use any of the options that are mentioned in the [MJML documentation for Node.js](https://github.com/mjmlio/mjml#inside-nodejs).

```php
use SmartPanel\Mjml\Mjml;

// let's assume $mjml contains the MJML you want to convert
$minifiedHtml = Mjml::new()->minify()->toHtml($mjml, [
    'beautify' => true,
    'minify' => true,
]);
```

### Validating MJML

You can make sure a piece of MJML is valid by using the `canConvert()` method.

```php
use SmartPanel\Mjml\Mjml;

Mjml::new()->canConvert($mjml); // returns a boolean
```

If `true` is returned we'll be able to convert the given MJML to HTML. However, there may still be some errors while converting the MJML to HTML. These errors are not fatal and the MJML will still be converted to HTML. You can see these non-fatal errors when calling `errors()` on the `MjmlResult` instance that is returned when calling `convert`.

You can use `canConvertWithoutErrors` to make sure the MJML is both valid and that there are no non-fatal errors while converting it to HTML.

```php
use SmartPanel\Mjml\Mjml;

Mjml::new()->canConvertWithoutErrors($mjml); // returns a boolean
```

### Specifying the path to nodejs executable

By default, the package itself will try to determine the path to the `node` executable. If the package can't find a path, you can specify a path in the environment variable `MJML_NODE_PATH`

```shell
MJML_NODE_PATH=/home/user/.nvm/versions/node/v20.11.0/bin
```
