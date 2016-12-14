<?php

class Services_ServicePriceController extends Zend_Controller_Action {

    public function preDispatch() {
        
    }

    public function indexAction() {
        //echo "vit";
        //$this->_helper->AutoCompleteDojo->disableLayouts();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $this->import_csv();
    }

    protected function import_csv() {
        set_time_limit(0);

        $file_name = dirname(APPLICATION_PATH) . '/files/price.csv';

        if (file_exists($file_name)) {
            $Price = new Price();

            $f = fopen($file_name, 'r');
            echo getdate() . "<BR>";
            while (!feof($f)) {
                $s = fgets($f);
                $s = trim($s);
                if ($s != '') {
                    $ar = explode(';', $s);

                    if ($ar[1] == '2') {
                        $data = array('id_product' => $ar[0],
                            'exists_type' => $ar[1],
                            'price' => $ar[2]);

                        if (!($er = $Price->add($data))) {
                            print_r($data);
                            print_r('error=' . $er);
                            exit;
                        }
                    }
                }
            }

            echo getdate();

            fclose($f);
        } else {
            echo 'file not found';
        }
    }

}

?>