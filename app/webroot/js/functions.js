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

/**
 * Convenience wrapper function for redirecting
 *
 * @param string url The url to redirect to
 */
function redirect(url) {
	window.location.href = url;
}

/**
 * Wrapper for writing to the console
 *
 * @param string msg The message
 */
function debug(msg) {
	if (console == undefined) {
		return;
	}
	console.log(msg);
}
