<?php

class Services_CommentsController extends Zend_Controller_Action {

    public function preDispatch() {
        
    }

    public function indexAction() {
        $toy_id = $this->_getParam('toy_id');
        $Comments = new Comments;

        $this->view->comments = $Comments->get_comments($toy_id);
        /*
          $this->view->comments = array(
          0 => array(
          'comm_name' => 'vit',
          'comm_text' => 'bla bla',
          'comm_time' => time()
          ),
          1 => array(
          'comm_name' => 'vlad',
          'comm_text' => 'who am i',
          'comm_time' => time()
          )
          );
         */
        //$this->_helper->viewRenderer->setNoRender();
        //$this->_helper->layout->disableLayout();
    }

    public function onecommentAction() {
        $comm_id = $this->_getParam('comm_id');

        $Comments = new Comments();
        $this->view->comm = $Comments->get($comm_id);

        //$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

    public function getformAction() {
        //$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

    public function sendformAction() {
        $toy_id = trim($this->_getParam('toy_id'));
        $comm_name = trim($this->_getParam('comm_name'));
        $comm_text = trim($this->_getParam('comm_text'));

        if (($comm_name != '') && ($comm_text != '')) {
            $Comments = new Comments();
            $comm_id = $Comments->insert(array(
                'toy_id' => $toy_id,
                'comm_name' => iconv('utf-8', 'cp1251', $comm_name),
                'comm_text' => iconv('utf-8', 'cp1251', $comm_text),
                'comm_time' => time() + 9 * 60 * 60
            ));

            if ($comm_id) {
                setcookie('comm_name', $comm_name, time() + 365 * 24 * 60 * 60, '/');
                $this->_forward('onecomment', null, null, array('comm_id' => $comm_id));
            }
        } else {
            echo 0;
        }

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

}

?>