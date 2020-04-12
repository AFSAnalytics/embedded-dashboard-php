<?php

namespace AFSAnalytics\Dashboard;

class Tools {

    /**
     * Output JS data
     *
     * @param array $arr data
     *
     * @return string js code
     */
    public static function renderJSData(array $arr) {
        $js = '';

        if (count($arr)) {
            foreach ($arr as $k => $v) {
                if (is_array($v) || is_string($v)) {
                    $js .= 'var ' . $k . '=' . json_encode($v, JSON_UNESCAPED_SLASHES) . ';';
                } elseif (is_string($v)) {
                    $js .= 'var ' . $k . '=' . static::jsEscape($v) . ';';
                } else {
                    $js .= 'var ' . $k . '="' . $v . '";';
                }
            }
        }

        return static::renderJSSCript($js);
    }

    public static function renderJSScript($js) {
        return empty($js) ?
                '' :
                "<script>\n$js\n</script>\n";
    }

}
