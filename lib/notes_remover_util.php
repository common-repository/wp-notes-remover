<?php

class WebWeb_WP_NotesRemoverUtil {
    // options for read/write methods.
    const FILE_APPEND = 1;
    const UNSERIALIZE_DATA = 2;
    const SERIALIZE_DATA = 3;

    /**
     * Replaces the template variables
     * @param string buffer to operate on
     * @param array the keys are uppercased and surrounded by %%KEY_NAME%%
     * @return string modified data
     */
    public static function replace_vars($buffer, $params = array()) {
        foreach ($params as $key => $value) {
            $key = trim($key, '%');
            $key = strtoupper($key);
            $key = '%%' . $key . '%%';

            $buffer = str_ireplace($key, $value, $buffer);
        }
//        var_dump($params);
        // Let's check if there are unreplaced variables
        if (preg_match('#(%%[\w-]+%%)#si', $buffer, $matches)) {
//            trigger_error("Not all template variables were replaced. Please check the missing and add them to the input params." . join(",", $matches[1]), E_USER_WARNING);
            trigger_error("Not all template variables were replaced. Please check the missing and add them to the input params." . var_export($matches, 1), E_USER_WARNING);
        }

        return $buffer;
    }

    /**
     * Checks if the url is valid
     * @param string $url
     */
    public static function validate_url($url = '') {
        $status = preg_match("@^(?:ht|f)tps?://@si", $url);

        return $status;
    }

    /**
     *
     * @param string $buffer
     */
    public static function sanitizeFile($str = '') {

        return $str;
    }

    /**
     * Serves the file for download. Forces the browser to show Save as and not open the file in the browser.
     * Makes the script run for 12h just in case and after the file is sent the script stops.
     *
     * Credits:
	 * http://php.net/manual/en/function.readfile.php
     * http://stackoverflow.com/questions/2222955/idiot-proof-cross-browser-force-download-in-php
     *
     * @param string $file
     * @param bool $do_exit - exit after the file has been downloaded.
     */
    public static function download_file($file, $do_exit = 1) {
        set_time_limit(12 * 3600); // 12 hours

        if (ini_get('zlib.output_compression')) {
            @ini_set('zlib.output_compression', 0);

            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', 1);
            }
        }

        if (!empty($_SERVER['HTTPS'])
                && ($_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)) {
            header("Cache-control: private");
            header('Pragma: private');

            // IE 6.0 fix for SSL
            // SRC http://ca3.php.net/header
            // Brandon K [ brandonkirsch uses gmail ] 25-Apr-2007 03:34
            header('Cache-Control: maxage=3600'); //Adjust maxage appropriately
        } else {
            header('Pragma: public');
        }

		header('Expires: 0');
 		header('Content-Description: File Transfer');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . (string) (filesize($file)));
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');

		ob_clean();
		flush();

        readfile($file);

		if ($do_exit) {
			exit;
		}
    }

    /**
     * Gets the content from the body, removes the comments, scripts
     * Credits: http://php.net/manual/en/function.strip-tags.phpm /  http://networking.ringofsaturn.com/Web/removetags.php
     * @param string $buffer
     * @string string $buffer
     */

    public static function html2text($buffer = '') {
        // we care only about the body so it must be beautiful.
        $buffer = preg_replace('#.*<body[^>]*>(.*?)</body>.*#si', '\\1', $buffer);
        $buffer = preg_replace('#<script[^>]*>.*?</script>#si', '', $buffer);
        $buffer = preg_replace('#<style[^>]*>.*?</style>#siU', '', $buffer);
//        $buffer = preg_replace('@<style[^>]*>.*?</style>@siU', '', $buffer); // Strip style tags properly
        $buffer = preg_replace('#<[a-zA-Z\/][^>]*>#si', ' ', $buffer); // Strip out HTML tags  OR '@<[\/\!]*?[^<>]*\>@si',
        $buffer = preg_replace('@<![\s\S]*?--[ \t\n\r]*>@', '', $buffer); // Strip multi-line comments including CDATA
        $buffer = preg_replace('#[\t\ ]+#si', ' ', $buffer); // replace just one space
        $buffer = preg_replace('#[\n\r]+#si', "\n", $buffer); // replace just one space
        //$buffer = preg_replace('#(\s)+#si', '\\1', $buffer); // replace just one space
        $buffer = preg_replace('#^\s*|\s*$#si', '', $buffer);

        return $buffer;
    }

    /**
     * Gets the content from the body, removes the comments, scripts
     *
     * @param string $buffer
     * @param array $keywords
     * @return array - for now it returns hits; there could be some more complicated results in the future so it's better as an array
     */
    public static function match($buffer = '', $keywords = array()) {
        $status_arr['hits'] = 0;

        foreach ($keywords as $keyword) {
            $cnt = preg_match('#\b' . preg_quote($keyword) . '\b#si', $buffer);

            if ($cnt) {
                $status_arr['hits']++; // total hits
                $status_arr['matches'][$keyword] = array('keyword' => $keyword, 'hits' => $cnt,); // kwd hits
            }
        }

        return $status_arr;
    }

    /**
     * @desc write function using flock
     *
     * @param string $vars
     * @param string $buffer
     * @param int $append
     * @return bool
     */
    public static function write($file, $buffer = '', $option = null) {
        $buff = false;
        $tries = 0;
        $handle = '';

        $write_mod = 'wb';

        if ($option == self::SERIALIZE_DATA) {
            $buffer = serialize($buffer);
        } elseif ($option == self::FILE_APPEND) {
            $write_mod = 'ab';
        }

        if (($handle = @fopen($file, $write_mod))
                && flock($handle, LOCK_EX)) {
            // lock obtained
            if (fwrite($handle, $buffer) !== false) {
                @fclose($handle);
                return true;
            }
        }

        return false;
    }

    /**
     * @desc read function using flock
     *
     * @param string $vars
     * @param string $buffer
     * @param int $option whether to unserialize the data
     * @return mixed : string/data struct
     */
    public static function read($file, $option = null) {
        $buff = false;
        $read_mod = "rb";
        $tries = 0;
        $handle = false;

        if (($handle = @fopen($file, $read_mod))
                && (flock($handle, LOCK_EX))) { //  | LOCK_NB - let's block; we want everything saved
            $buff = @fread($handle, filesize($file));
            @fclose($handle);
        }

        if ($option == self::UNSERIALIZE_DATA) {
            $buff = unserialize($buff);
        }

        return $buff;
    }

    /**
     *
     * Appends a parameter to an url; uses '?' or '&'
     * It's the reverse of parse_str().
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public static function add_url_params($url, $params = array()) {
        $str = '';

        $params = (array) $params;

        if (empty($params)) {
            return $url;
        }

        $query_start = (strpos($url, '?') === false) ? '?' : '&';

        foreach ($params as $key => $value) {
            $str .= ( strlen($str) < 1) ? $query_start : '&';
            $str .= rawurlencode($key) . '=' . rawurlencode($value);
        }

        $str = $url . $str;

        return $str;
    }

    // generates HTML select
    public static function html_select($name = '', $sel = null, $options = array(), $attr = '') {
        $html = "\n" . '<select name="' . $name . '" id="' . $name . '" ' . $attr . '>' . "\n";

        foreach ($options as $key => $label) {
            $selected = $sel == $key ? ' selected="selected"' : '';
            $html .= "\t<option value='$key' $selected>$label</option>\n";
        }

        $html .= '</select>';
        $html .= "\n";

        return $html;
    }

    // generates status msg
    public static function msg($msg = '', $status = 0) {
        $cls = empty($status) ? 'error' : 'success';
        $cls = $status == 2 ? 'notice' : $cls;

        $msg = "<p class='status_wrapper'><div class=\"status_msg $cls\">$msg</div></p>";

        return $msg;
    }

    /**
     * checks several variables and returns the lowest.
     * @see http://www.kavoir.com/2010/02/php-get-the-file-uploading-limit-max-file-size-allowed-to-upload.html
     * @return int
     */
    public static function get_max_upload_size() {
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));

        $upload_mb = min($max_upload, $max_post, $memory_limit);

        return $upload_mb;
    }

    /**
     * proto str formatFileSize( int $size )
     *
     * @param string
     * @return string 1 KB/ MB
     */
    public static function format_file_size($size) {
    	$size_suff = 'Bytes';

        if ($size > 1024 ) {
            $size /= 1024;
            $size_suff = 'KB';
        }

        if ( $size > 1024 ) {
            $size /= 1024;
            $size_suff = 'MB';
        }

        if ( $size > 1024 ) {
            $size /= 1024;
            $size_suff = 'GB';
        }

        if ( $size > 1024 ) {
            $size /= 1024;
            $size_suff = 'TB';
        }

        $size = number_format($size, 2);

        return $size . " $size_suff";
    }
}
