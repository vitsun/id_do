<?php

class ToyController extends Zend_Controller_Action {

    public function preDispatch() {
        
    }

    public function indexAction() {
        
    }

    protected function set_layout_vars() {
        $Settings = new Settings();
        $settings = $Settings->get('partner_id');
        $this->_helper->layout()->partner_id = $settings['s_val'];
    }

    public function getNewToysAction() {
        $Product = new Product();
        $this->view->toys = $Product->get_new_products(10);

        //$this->_forward('list-toys');
        //$this->_forward('list-toys',null,null,array('list_type' => 'new_toys'));
        //$this->_helper->actionStack('list-toys');

        $this->view->h1 = "Новинки";
        $this->view->list_type = 'top_new';
        $this->_helper->viewRenderer('list-toys');

        $this->set_layout_vars();
    }

    public function getTopToysAction() {
        $this->view->new_toys = array(
            0 => array('title' => 'Наклей и раскрась для самых маленьких. Лунтик',
                'small_title' => 'Раскраски с наклейками',
                'description' => 'В серии выходят книжки-раскраски увеличенного формата с яркими наклейками. Малыши дошкольного и младшего школьного возраста учатся раскрашивать аккуратно, не заходя за контур изображения.',
                'price' => '53.00',
                'img_src' => 'http://my-shop.ru/_files/product/2/66/658793.jpg',
                'tovar_id' => 1),
            1 => array('title' => 'Наклей и раскрась для самых маленьких. Лунтик',
                'small_title' => 'Раскраски с наклейками',
                'description' => 'В серии выходят книжки-раскраски увеличенного формата с яркими наклейками. Малыши дошкольного и младшего школьного возраста учатся раскрашивать аккуратно, не заходя за контур изображения.',
                'price' => '53.00',
                'img_src' => 'http://my-shop.ru/_files/product/2/66/658792.jpg',
                'tovar_id' => 2)
        );

        //$this->_helper->actionStack('list-toys');
        $this->view->h1 = "Популярные";
        $this->_helper->viewRenderer('list-toys');
    }

    public function getToySortToolbarAction() {
        
    }

    public function getVisitedToysScrollbarAction() {
        $front = Zend_Controller_Front::getInstance();

        if ($front->getRequest()->getParam('controller') != 'put') {
            if (isset($_COOKIE['vt'])) {
                $ar_toys = array();
                $Product = new Product();

                $ar_ids = explode(',', $_COOKIE['vt']);
                foreach ($ar_ids as $id) {
                    $toy = $Product->get($id);
                    if ($toy)
                        $ar_toys[] = $toy;
                }

                if (count($ar_toys) > 0) {
                    $this->view->toys = $ar_toys;
                } else {
                    $this->_helper->viewRenderer->setNoRender();
                    $this->_helper->layout->disableLayout();
                }
            } else {
                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->layout->disableLayout();
            }
        } else {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
        }
    }

    protected function get_list_params() {
        $res = array();

        $order = $this->_getParam('order');

        $ar_order = array('rating', 'price');
        if ($this->_getParam('search')) {
            $ar_order [] = 'relevance';
        }
        $order = ( (in_array($order, $ar_order)) ? $order : 'rating');
        setcookie('order', $order, 0, '/');

        $orderby = $this->_getParam('orderby');
        $orderby = ( (in_array($orderby, array('desc', 'asc'))) ? $orderby : 'desc');
        setcookie('orderby', $orderby, 0, '/');

        $pl = $this->_getParam('pl');
        $pl = ($pl ? $pl : '1');
        setcookie('pl', $pl, 0, '/');

        $show = $this->_getParam('show');
        $show = (in_array($show, array('list', 'grid')) ? $show : 'grid');
        setcookie('show', $show, 0, '/');

        $pageNumber = $this->_getParam('page');
        $pageNumber = ($pageNumber ? $pageNumber : 1);

        return array('order' => $order,
            'orderby' => $orderby,
            'pl' => $pl,
            'show' => $show,
            'pageNumber' => $pageNumber,
            'ar_order' => $ar_order);
    }

    public function getToysFromCategoryAction() {
        $categoryId = $this->_getParam('categoryId');
        $t = $this->_getParam('t');

        $Category = new Category();
        $cur_category = $Category->get($categoryId);

        if (!$cur_category) {
            throw new Zend_Controller_Action_Exception('Такой категории у нас нет', 404);
        }

        $Product = new Product();

        if (trim($cur_category['children']) == $categoryId) {

            $list_params = $this->get_list_params();

            $options = array('page' => $list_params['pageNumber'],
                'perPage' => 20 * $list_params['pl'],
                'order' => $list_params['order'],
                'orderby' => $list_params['orderby']
            );

            if ($t) {
                $options['t'] = $t;
            }
            //$this->view->toys = $Product->get_products_by_category($categoryId,$options,$rPaginator);
            $Product->get_products_by_category($categoryId, $options, $rPaginator);
            $rPaginator->setPageRange(20);
            $this->view->toys = $rPaginator->getCurrentItems()->toArray();
            $this->view->paginator = $rPaginator;
            $this->view->h1 = $cur_category['name'];
            $this->view->descr = ' Категория ' . $cur_category['name'] . '.';

            $this->view->order = $list_params['order'];
            $this->view->orderby = $list_params['orderby'];
            $this->view->ar_order = $list_params['ar_order'];
            $this->view->pl = $list_params['pl'];
            $this->view->list_type = 'from_category';
            $this->view->show = $list_params['show'];
        } else {
            $this->view->toys = $Product->get_new_products_by_category($cur_category['children'], 10);
            $this->view->h1 = "Новинки - " . $cur_category['name'];
            $this->view->descr = ' Новинки из категории ' . $cur_category['name'] . '.';
            $this->view->list_type = 'top_new';
            $this->view->show = 'list';
        }

        if (count($this->view->toys) == 1) {
            $this->_forward('detail', null, null, array('toy_id' => $this->view->toys[0]['id']));
        } else {
            if ($this->view->show == 'list') {
                $this->_helper->viewRenderer('list-toys');
            } else {
                $this->_helper->viewRenderer('grid-toys');
            }
        }

        $this->set_layout_vars();
    }

    public function detailAction() {
        $id = $this->_getParam('toy_id');

        $Product = new Product();

        if ($toy = $Product->get($id)) {
            $this->view->toy = $toy;
            append_cookie('vt', $toy['id']); // save visited toy to cookie
        } else {
            throw new Zend_Controller_Action_Exception('Такого товара у нас нет', 404);
        }

        $Settings = new Settings();
        $settings = $Settings->get('partner_id');
        $this->view->partner_id = $settings['s_val'];

        $this->set_layout_vars();
    }

    public function getToysBySearchTextAction() {
        $search_text = $this->_getParam('search_text');
        $search_text = trim($search_text);

        $old_len = -1;
        while ($old_len != strlen($search_text)) {
            $search_text = str_replace('  ', ' ', $search_text);
            $old_len = strlen($search_text);
        }

        //$search_text = str_replace(' ','%',$search_text);

        $Product = new Product();

        $list_params = $this->get_list_params();

        $options = array('page' => $list_params['pageNumber'],
            'perPage' => 20 * $list_params['pl'],
            'order' => $list_params['order'],
            'orderby' => $list_params['orderby']
        );
        $Product->get_products_by_search_text($search_text, $options, $rPaginator);
        $rPaginator->setPageRange(10);
        $this->view->toys = $rPaginator->getCurrentItems()->toArray();
        $this->view->paginator = $rPaginator;
        $this->view->h1 = "Результаты поиска";
        $this->view->descr = ' Результаты поиска.';

        $this->view->order = $list_params['order'];
        $this->view->orderby = $list_params['orderby'];
        $this->view->ar_order = $list_params['ar_order'];
        $this->view->pl = $list_params['pl'];
        $this->view->list_type = 'from_category';
        $this->view->show = $list_params['show'];

        //$this->view->toys = $Product->get_products_by_search_text($search_text);
        //$this->_helper->viewRenderer('list-toys');
        if ($this->view->show == 'list') {
            $this->_helper->viewRenderer('list-toys');
        } else {
            $this->_helper->viewRenderer('grid-toys');
        }

        $this->set_layout_vars();
    }

}
