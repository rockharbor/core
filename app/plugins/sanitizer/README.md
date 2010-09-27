# Sanitizer

Sanitizer is CakePHP plugin that makes it very easy to automatically sanitize
your data. For example, if you don't want HTML tags in some field and want clean
HTML tags in others, Sanitizer makes it easy to automatically get what you want.

## Usage

Simply add the behavior to your model:

    var $actsAs = array('Sanitizer.Sanitize');

Now just define your sanitization rules. These rules are formatted similar to
CakePHP's validation rules. The Sanitize behavior uses the built-in [Sanitize class][1].
By default, uses Sanitize::clean() and strips html. To use a different method on
the Sanitize class, set the $sanitize var with the key as the field name and the
value the method. Optionally pass an array with the options that you would
normally pass to the Sanitize method you want to use.

    // clean the name field using Sanitize::html()
    var $sanitize = array(
        'name' => 'html'
    );

    // or clean the name field using Sanitize::paranoid() and allowing '%'
    var $sanitize = array(
        'name' => array(
            'paranoid => array('%')
        )
    );

If you don't define the `$sanitize` var on the model, the Sanitize behavior will
automatically use Sanitize::clean() on every field passed on Model::save().

Sanitization methods supported (aside from clean):
* html
* paranoid
* stripAll
* stripImages
* stropScripts
* stripWhitespace

## Advanced

By default, the Sanitize behavior cleans the data before validation. If you want
validate *then* sanitize, use:

    var $actsAs = array(
        'Sanitizer.Sanitize' => array(
            'validate' => 'before'
        )
    );

See the test cases for code samples.

## Future plans

* Add ability to customize default method
* Allow using methods outside of Cake's Sanitize class

[1]: http://api13.cakephp.org/class/sanitize