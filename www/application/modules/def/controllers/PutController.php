<?php

class PutController extends Zend_Controller_Action {

    public function preDispatch() {
        
    }

    public function indexAction() {
        
    }

    protected function set_layout_vars() {
        $Settings = new Settings();
        $settings = $Settings->get('partner_id');
        $this->_helper->layout()->partner_id = $settings['s_val'];
    }

    public function putToyAction() {
        $ar_put_toys = array();

        $toy_id = $this->_getParam('toy_id');

        $res = put_toy($toy_id);

        echo $res;

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

    public function listAction() {
        $ar_put = array();

        if ($this->getRequest()->isPost()) {
            $put_check_action = $this->_getParam('put_check_action');

            if ($put_check_action == 1) { // delete
                $ch = $this->_getParam('ch');

                if ((isset($_COOKIE['pt'])) && ($ch)) {
                    $ar_put = explode(",", $_COOKIE['pt']);
                    $ar_put = array_flip($ar_put);
                    foreach ($ch as $key => $val) {
                        unset($ar_put[$key]);
                    }
                    $ar_put = array_flip($ar_put);
                    setcookie('pt', implode(',', $ar_put), time() + 30 * 24 * 60 * 60, '/');
                    if (count($ar_put) > 0) {
                        $_COOKIE['pt'] = implode(',', $ar_put);
                    } else {
                        unset($_COOKIE['pt']);
                    }
                }
            } elseif ($put_check_action == 2) { // to basket
                $ch = $this->_getParam('ch');

                if ($ch) {
                    $this->view->to_basket = array_keys($ch);
                }
            }
        } else {
            
        }

        if (isset($_COOKIE['pt'])) {
            $ar_put = explode(",", $_COOKIE['pt']);

            $Product = new Product();

            $this->view->toys = $Product->get_put_products($ar_put);

            $this->view->h1 = 'Отложенные';
            $this->view->descr = ' Отложенные игрушки.';
        } else {
            $this->view->toys = array();
            $this->view->h1 = 'Нет отложенных';
            $this->view->descr = ' Нет отложенных игрушкек.';
        }

        $this->set_layout_vars();
    }

}

?>