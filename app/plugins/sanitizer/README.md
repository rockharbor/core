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

If you don't define the field in the `$sanitize` var on the model, the Sanitize
behavior will automatically use `Sanitize::clean($value, array('remove_html' => true));`
on every field passed on `Model::save()`.

If you wish to skip a specific field, set the field to false

    // clean everything except the description
    var $sanitize = array(
        'description' => false
    );

If you wish to skip everything for a specific model, set the `$sanitize` var to
false. This is useful if you want to sanitize everything by default by applying
the behavior to your AppModel but have special case models.

    // sanitize nothing on this model
    var $sanitize = false;

Sanitization methods supported:
* clean
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

## Notes

* If you upload files, the Sanitize behavior will by default sanitize and escape
  the file path. Make sure to set `'file' => false` in your `$sanitize` var!

## Future plans

* Add ability to customize default method
* Allow using methods outside of Cake's Sanitize class

[1]: http://api13.cakephp.org/class/sanitize