<?php

class Services_ImagesController extends Zend_Controller_Action {

    public function preDispatch() {
        
    }

    public function dataAction() {
        $w = 80;
        $h = 100;

        $img = $this->_getParam('img');
        $ar_img = explode('\.', $img);
        //print_r($ar_img);exit;

        $Product = new Product();
        $product = $Product->get($ar_img[0]);

        if (($product) && ($product['picture'])) {
            $res = GetWebPage($product['picture'], $RetStatus);
            //header('Content-type: image/jpg');
            //print_r($res);exit;
            //$ar = getimagesizefromstring($res);
            $im = imagecreatefromstring($res);
            $width = imagesx($im);
            $height = imagesy($im);
            //print_r($width." ".$height);

            if ($width > $height) {
                $new_height = $w * $height / $width;
                $new_width = $w;
            } else {
                $new_height = $h;
                $new_width = $h * $width / $height;
            }

            $im_new = imagecreatetruecolor($new_width, $new_height);
            //imagecopyresized($im_new,$im,0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagecopyresampled($im_new, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            header('Content-type: image/jpg');
            imagejpeg($im_new);
        }

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

}

?>