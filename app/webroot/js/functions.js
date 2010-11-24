/**
 * Additional global functions
 */

/**
 * Strips slashes
 *
 * @param str string The string to strip slashes from
 * @link http://phpjs.org/functions/stripslashes:537
 */
function stripslashes (str) {
    return (str+'').replace(/\\(.?)/g, function (s, n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':
                return '\u0000';
            case '':
                return '';
            default:
                return n1;
        }
    });
}