<?php

namespace AFSAnalytics\Dashboard\Ajax;

class Result {

    private
            $request_,
            $data_,
            $visitor_ids_ = [],
            $product_skus_ = []

    ;

    public function __construct($request, $data = []) {
        $this->request_ = $request;
        $this->data_ = $data;
    }

    public function setData($data) {
        $this->data_ = $data;
    }

    public function render() {
        $data = $this->data_;
        $actions = [];

        if (!empty($data['error'])) {
            $this->parseError($data['error']);
        }

        if (empty($data) || empty($data['performed_actions'])) {
            return ['error' => 'no action requested'];
        }

        foreach ($data['performed_actions'] as $uid => $a) {
            $actions[$uid] = $this->parseAction($a, $uid);
        }

        $result = ['performed_actions' => $actions];


        return class_exists('AFSAHook', false) ?
                $this->renderEnhancedInfos($result) :
                $result
        ;
    }

    private function parseError($e) {
        switch ($e) {
            // Invalid key
            case 'access_denied':
                break;
        }
    }

    private function parseAction(&$a, $uid) {
        if (empty($a['metas'])) {
            return $a;
        }

        $m = &$a['metas'];

        if (!empty($m['custom'])) {
            foreach ($m['custom'] as $k => $v) {
                switch ($k) {
                    case 'user_id':
                        $this->registerVisitors($v);
                        break;
                    case 'product_sku':
                        $this->registerProducts($v);
                        break;
                }
            }
        }

        return $a;
    }

    private function registerVisitors(array $ids = []) {
        $this->visitor_ids_ = array_merge($this->visitor_ids_, $ids);
    }

    private function registerProducts(array $ids = []) {
        $this->product_skus_ = array_merge($this->product_skus_, $ids);
    }

    // CONTEXT INFOS -- PLACE HOLDERS ATM

    private function renderEnhancedInfos($result) {
        $visitors = &$this->visitor_ids_;
        $products = &$this->product_skus_;
        $ret = $result;

        $metas = ['context' => []];
        if (!empty($visitors)) {
            $infos = $this->renderVisitorsInfos();
            if (!empty($infos)) {
                $metas['context']['visitors'] = $infos;
            }
        }

        if (!empty($products)) {
            $infos = $this->renderProductsInfos();
            if (!empty($infos)) {
                $metas['context']['products'] = $infos;
            }
        }

        if (!empty($metas['context'])) {
            $ret['metas'] = $metas;
        }

        return $ret;
    }

    private function renderVisitorsInfos() {
        $context = $this->request_->context_;

        if (empty($this->visitor_ids_)) {
            return null;
        }

        $known_ids = empty($context['visitors']) ?
                [] :
                $context['visitors'];

        $result_ids = array_unique($this->visitor_ids_);

        $ids = array_diff($result_ids, $known_ids);
        if (empty($ids)) {
            return null;
        }

        $ret = [];
        $items = AFSAHook::getCustomerInfos($ids);

        if (!empty($items)) {
            foreach ($items as $item) {
                $ret[$item['id']] = $item;
            }
        }

        return $ret;
    }

    private function renderProductsInfos() {
        $context = $this->request_->context_;

        if (empty($this->product_skus_)) {
            return null;
        }

        $known_skus = empty($context['products']) ?
                [] :
                $context['products'];

        $result_skus = array_unique($this->product_skus_);

        $skus = array_diff($result_skus, $known_skus);
        if (empty($skus)) {
            return null;
        }

        $ret = array();
        $items = AFSAHook::getProductsInfos($skus);


        if (!empty($items)) {
            foreach ($items as $item) {
                $id = $item['id_product'];
                try {
                    $ret[$item['reference']] = AFSAHook::getAjaxContextInfoForProduct($id);
                } catch (Exception $e) {
                    
                }
            }
        }

        return $ret;
    }

}
