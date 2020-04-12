<?php

namespace AFSAnalytics\Dashboard;

define('AFSA_API_HOME', 'https://api.afsanalytics.com');
define('AFSA_API_VERSION', 1);
define('AFSA_JS_ASSET_PATH', '/assets/js/common/current/');
define('AFSA_CSS_ASSET_PATH', '/assets/css/common/current/');

use AFSAnalytics\Dashboard\Tools;
use AFSAnalytics\Dashboard\Ajax\Server as AjaxServer;

class Controller {

    protected
            $api_key_,
            $lng_ = 'en',
            $template_ = 'traffic',
            $currency_ = 'USD',
            $ajax_url_,
            $access_key_,
            $ecom_level_,
            $parent_selector_ = 'body'

    ;

    public function __construct($key = null) {
        $this
                ->setAPIKey($key)
                ->setAJAXURL()
        ;
    }

    public function setAJAXURL($u = null) {

        if ($u)
            $this->ajax_url_ = $u;
        else {
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $this->ajax_url_ = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        return $this;
    }

    public function setAPIKey($key) {
        if ($key) {
            $this->api_key_ = $key;
            $this->account_id_ = explode('-', $this->api_key_)[0];
        }
        return $this;
    }

    /**
     * Set dashboard langage
     * 
     * @param string $lng  (supported : 'en', 'fr')
     * @return $this
     */
    public function setLangage($lng) {
        $this->lng_ = $lng;
        return $this;
    }

    public function setTemplate($template) {
        $this->template_ = $template;
        return $this;
    }

    public function setParentSelector($sel = null) {
        $this->parent_selector_ = $sel ? $sel : 'body';
        return $this;
    }

    public function enableECommerce($cfg = []) {

        if ($cfg['advanced'] ?? 0) {

            $this->template_ = 'ecom';
            $this->ecom_level_ = 'advanced';
        } else {

            $this->template_ = 'ecom';
            $this->ecom_level_ = 'basic';
        }

        $this->currency_ = cfg['currency'] ?? 'USD';
        return $this;
    }

    public function disableECommerce() {
        $this->template_ = 'traffic';
        $this->ecom_level_ = null;
        return $this;
    }

    //


    public function render($options = []) {
        $ret = '<link rel="stylesheet" property="stylesheet" href="'
                . AFSA_API_HOME
                . AFSA_CSS_ASSET_PATH
                . 'packed.css">'
                //
                . '<link rel="stylesheet" property="stylesheet"'
                . ' href="https://fonts.googleapis.com/css?family=Lato:700|Open+sans:500">'
        ;


        if (empty($options['fa-disaled']))
            $ret .= '<link rel="stylesheet" property="stylesheet"'
                    . ' href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">'
            ;



        $ret .= Tools::renderJSData(
                        [
                            'AFSA_dashboard_config' => $this->renderJSConfig()
                        ]
                )
                . $this->renderInlineScript()
        ;


        foreach ($this->getJSDependencies() as $n) {
            $ret .= '<script src='
                    . AFSA_API_HOME
                    . AFSA_JS_ASSET_PATH
                    . $n . '.js></script>';
        }

        if (!empty($options['css']))
            $ret .= '<style>' . $options['css'] . '</style>';

        return $ret;
    }

    protected function getJSDependencies() {
        return ['d3.min', 'c3.min', 'chart.engine', 'dashboard'];
    }

    // CONFIG

    protected function renderJSConfig() {

        $cfg = [
            'host' => 'php pluggin',
            'type' => 'plugin',
            'lng' => $this->lng_,
            'account_id' => $this->account_id_,
            'server_host' => AFSA_API_HOME,
            'ajax' => [
                'server' => $this->ajax_url_,
                'client' => 'AFSA:php:module'
            ],
            'dashboard' => [
                'parent' => $this->parent_selector_,
                'host' => 'php module',
                'container' => [
                    'template' => $this->template_
                ],
            ]
        ];



        if ($this->access_key_) {
            $cfg['access_key'] = $this->access_key_;
        }

        if ($this->ecom_level_) {
            $cfg['ecom'] = [
                'enabled' => 1,
                'level' => $this->ecom_level_,
                'currency' => $this->currenc_
            ];

            $cfg['dashboard'] = [
                'container' => [
                    'template' => 'ecom',
                ]
            ];
        }


        return $cfg;
    }

    protected function renderInlineScript() {
        return implode("\n", [
            '',
            '<script>',
            '$(function () {',
            'AFSA.version();',
            'AFSA.config().set({}).dump();',
            'AFSA.dashboard.container().run();',
            '});',
            '</script>',
            ''
        ]);
    }

//
    public function runAJAXServer() {
        AjaxServer::run($this->api_key_);
        return $this;
    }

}
