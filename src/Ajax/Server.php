<?php

namespace AFSAnalytics\Dashboard\Ajax;

use AFSAnalytics\Dashboard\Ajax\Request;

class Server {

    public static function run($api_key) {

        if (empty($_POST['account_id']) || empty($_POST['actions']))
            return;

        ob_start();
        $request = new Request($api_key);
        $result = $request->run();
        ob_end_clean();

        header('Content-type: application/json');
        echo json_encode($result);
        exit;
    }

}
