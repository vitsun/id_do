<?php

function GetWebPage($url_page, &$RetStatus, $post_str = "", $is_post = false) {
    $ch = curl_init($url_page);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    //curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    //curl_setopt ($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/temp/"."mail_coockie.txt");
    //curl_setopt ($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/temp/"."mail_coockie.txt");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //0
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIE, 'fuid01=4b55eb3819e45ffc.GHz1qZGVLdiellfrdaV8oOurD-eyAQLruoiXkgwQlajZVIiK72GT1sl3vBlpr8MCD-dfUUrA7hZR_ahgXIXDZ-3EAqCx5Nfdnl4SSdbSbfPeOJCprMor9M0eB8hpEVX1;');

    //curl_setopt ($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
    //curl_setopt ($ch, CURLOPT_PROXY,"192.168.5.1:8080");
    //curl_setopt ($ch, CURLOPT_PROXYUSERPWD,"respect\prog3:128146");

    if ($is_post) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
        curl_setopt($ch, CURLOPT_POST, 1);
    }

    $str_ret = curl_exec($ch);
    $RetStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if (empty($curl_error)) {
        $res = $str_ret;
    } else {
        $res = false;
    }

    return ($res);
}

function make_new_url($url, $ar_add, $ar_remove) {
    $info = parse_url($url);
    $path = $info['path'];

    $ar = $_GET;

    foreach ($ar_remove as $item) {
        unset($ar[$item]);
    }

    foreach ($ar_add as $key => $val) {
        $ar[$key] = $val;
    }

    $req_str = from_ar_to_url($ar);

    return DOMAIN_PATH . $path . ($req_str ? '?' . $req_str : '');
}

function from_ar_to_url($ar) {
    $res = '';

    foreach ($ar as $key => $val) {
        if (is_array($val)) {
            foreach ($val as $key_ar => $val_ar) {
                $res .= $key . '[' . $key_ar . ']=' . $val_ar . '&';
            }
        } else {
            $res .= $key . '=' . $val . '&';
        }
    }

    if ($res != '') {
        $res = substr($res, 0, strlen($res) - 1);
    }

    return ($res);
}

function append_cookie($cookie_name, $cookie_value) {
    $ar_values = array();

    if (isset($_COOKIE[$cookie_name])) {
        $ar_values = explode(',', $_COOKIE[$cookie_name]);
        $key = array_search($cookie_value, $ar_values);
        if ($key !== false) {
            unset($ar_values[$key]);
        }
    }

    array_unshift($ar_values, $cookie_value);
    //$ar_values[] = $cookie_value;
    setcookie($cookie_name, implode(',', $ar_values), 0, '/');
}

function put_toy($toy_id) {
    if (isset($_COOKIE['pt'])) {
        $ar_put_toys = explode(',', $_COOKIE['pt']);
        if (!in_array($toy_id, $ar_put_toys)) {
            $ar_put_toys[] = $toy_id;
        }
    } else {
        $ar_put_toys[] = $toy_id;
    }

    if (count($ar_put_toys) > 0) {
        setcookie('pt', implode(',', $ar_put_toys), time() + 30 * 24 * 60 * 60, '/');
        $res = 1;
    } else {
        $res = 0;
    }

    return ($res);
}

?>