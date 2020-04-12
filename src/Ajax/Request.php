<?php

namespace AFSAnalytics\Dashboard\Ajax;

use AFSAnalytics\Dashboard\Ajax\Result;
use AFSAnalytics\Dashboard\Ajax\HTTPClient;


class Request {

    private
            $api_key_,
            $requested_actions_,
            $server_url_

    ;

    public function __construct($api_key) {

        $this->api_key_ = $api_key;

        $this->server_url_ = AFSA_API_HOME . '/v' . AFSA_API_VERSION . '/stats/batch';

        if (!empty($_POST['actions']))
            $this->requested_actions_ = $_POST['actions'];
    }

    public function run() {
        return $this->sendBatch();
    }

    private function validate() {
        return !empty($this->api_key_) && !empty($this->requested_actions_);
    }

    public function sendBatch() {

        if (!$this->validate())
            return null;


        $client = new HTTPClient();
        $client->post($this->server_url_,
                [
                    'actions' => $this->requested_actions_,
                    'api_key' => $this->api_key_
                ]
        );


        $result = new Result($this, $client->getJSON());
        return $result->render();
    }

}
