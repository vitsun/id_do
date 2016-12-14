<?php

class ShopController extends Zend_Controller_Action {

    protected $payment_cache_file = 'payment_cache.dat';
    protected $delivery_cache_file = 'delivery_cache.dat';
    protected $day_for_update_cache = 1;

    public function preDispatch() {
        $Settings = new Settings();
        $settings = $Settings->get('partner_id');
        $this->_helper->layout()->partner_id = $settings['s_val'];
    }

    public function indexAction() {
        
    }

    public function basketAction() {
        $Settings = new Settings();
        $settings = $Settings->get('partner_id');
        $this->view->partner_id = $settings['s_val'];

        $this->_helper->_layout->setLayout('layout_without_left');
    }

    protected function replace_url($s, $partner_id) {
        $to_my_domen = array('/helper_25' => '/shop/delivery', '/helper_26' => '/shop/payment');

        $new_domen = "http://my-shop.ru";
        $ar_not = array('http', 'https', '#');
        if (preg_match_all('@(?: href|src)="(.*)"@U', $s, $r)) {
            $r[1] = array_unique($r[1]);
            foreach ($r[1] as $key => $val) {
                $val = trim($val);
                $need_repl = true;

                foreach ($to_my_domen as $key_md => $val_md) {
                    if (strpos($val, $key_md) !== false) {
                        $s = str_replace($val, $val_md, $s);
                        $need_repl = false;
                        break;
                    }
                }

                if ($need_repl) {
                    foreach ($ar_not as $item) {
                        if (strpos($val, $item) === 0) {
                            $need_repl = false;
                            break;
                        }
                    }

                    if ($need_repl) {
                        $s = str_replace('"' . $val . '"', '"' . $new_domen . $val . (strpos($val, '?') == false ? '?' : '&') . 'partner=' . $partner_id . '"', $s);
                        //print_r($val.' => '.$new_domen.$val.(strpos($val,'?') == false ? '?' : '&').'partner='.$partner_id."<br>");
                    }
                }
            }
        }
        //print_r($s);
        return ($s);
    }

    protected function save_cache($file_name, $content) {
        $f = fopen($file_name, 'wb');
        fwrite($f, $content);
        fclose($f);
    }

    protected function load_cache($file_name) {
        $res = '';
        if (file_exists($file_name)) {
            $res = implode('', file($file_name));
        }

        return ($res);
    }

    public function paymentAction() {
        $Settings = new Settings();
        $settings = $Settings->get('partner_id');
        $payment_info = $Settings->get('payment_info');

        $file_name = FTP_PATH . '/cache/' . $this->payment_cache_file;

        $ok = false;

        //if ( ((int)date('N') == 1) || (!file_exists($file_name)) ) {
        if (((time() - intval($payment_info['s_val'])) > 7 * 24 * 60 * 60) || (!file_exists($file_name))) {
            $s = GetWebPage('http://my-shop.ru/my/helper_26', $RetStatus);
            //$s = implode('',file('D:\\xampp\\htdocs\\is\\1\\helper_26.htm'));

            if ($RetStatus == 200) {
                if (preg_match('/(<hr class="help"><\/div>\nВ зависимости от выбранного.*зарезервированными на счете в My\-shop\.ru.*<\/td>)/Uis', $s, $r)) {
                    //$r[1] = preg_replace($find,$repl,$r[1]);
                    $r[1] = $this->replace_url($r[1], $settings['s_val']);
                    $content = "<table><tr><td>" . $r[1] . "</td></tr></table>";
                    $this->view->payment_content = $content;

                    $this->save_cache($file_name, $content);

                    $Settings->set('payment_info', time());

                    $ok = true;
                }
            }
        }

        if (!$ok) {
            $this->view->payment_content = $this->load_cache($file_name);
        }

        $this->view->partner_id = $settings['s_val'];

        $this->_helper->_layout->setLayout('layout_without_left');
    }

    public function deliveryAction() {
        $Settings = new Settings();
        $settings = $Settings->get('partner_id');
        $delivery_info = $Settings->get('delivery_info');

        $file_name = FTP_PATH . '/cache/' . $this->delivery_cache_file;

        $ok = false;

        //if ( ((int)date('N') == 1) || (!file_exists($file_name)) ) {
        if (((time() - intval($delivery_info['s_val'])) > 7 * 24 * 60 * 60) || (!file_exists($file_name))) {
            $s = GetWebPage('http://my-shop.ru/my/helper_25', $RetStatus);
            //$s = implode('',file('D:\\xampp\\htdocs\\is\\1\\helper_25.htm'));

            $RetStatus = 200;
            if ($RetStatus == 200) {
                if (preg_match('/(<hr class="help"><\/div>\nНабор доступных способов доставки зависит.*<li><a href="\/my\/helper_71">Дополнительные курьерские службы<.*<\/td>)/Uis', $s, $r)) {
                    //$r[1] = preg_replace($find,$repl,$r[1]);
                    $r[1] = $this->replace_url($r[1], $settings['s_val']);
                    $content = "<table><tr><td>" . $r[1] . "</td></tr></table>";
                    $this->view->delivery_content = $content;

                    $this->save_cache($file_name, $content);

                    $Settings->set('delivery_info', time());

                    $ok = true;
                }
            }
        }

        if (!$ok) {
            $this->view->delivery_content = $this->load_cache($file_name);
        }

        $this->view->partner_id = $settings['s_val'];

        $this->_helper->_layout->setLayout('layout_without_left');
    }

    public function contactsAction() {
        $Settings = new Settings();
        $settings = $Settings->get('partner_id');
        $this->view->partner_id = $settings['s_val'];
    }

    public function discountsAction() {
        $Settings = new Settings();
        $settings = $Settings->get('partner_id');
        $this->view->partner_id = $settings['s_val'];
    }

}
