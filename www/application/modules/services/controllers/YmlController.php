<?php
class Services_YmlController extends Zend_Controller_Action
{
	var $f;

	public function preDispatch()
	{
	}

	public function indexAction()
	{
	}

	private function write_to_file($s)
	{
		fwrite($this->f,$s);
	}

	public function getAction()
	{
		$c_count = 1000;
		$Product = new Product();
		$Category = new Category();
		$i=0;

		$Tempval = new Tempval();
		$last_update = $Tempval->get('last-update');
		$last_update = $last_update['val'];
		$last_update = ($last_update ? $last_update : time()-24*60*60);

		$this->f = fopen(FTP_PATH.'/ymlcatalog.xml','wb');

		$this->write_to_file('<?xml version="1.0" encoding="windows-1251"?><!DOCTYPE yml_catalog SYSTEM "http://partner.market.yandex.ru/pages/help/shops.dtd">');
		$this->write_to_file('<yml_catalog date="'.date('Y-m-d H:i',$last_update).'">');
		$this->write_to_file('<shop>');
		$this->write_to_file('<name>Интернет-магазин Игрушки деткам.РУ</name>');
		$this->write_to_file('<company>Игрушки деткам.РУ</company>');
		$this->write_to_file('<url>http://igrushki-detkam.ru</url>');
		$this->write_to_file('<email>knife_81@mail.ru</email>');

		// ------------------- CURRENCY ----------------------------------------
		$this->write_to_file('<currencies><currency id="RUR" rate="1"/></currencies>');

		// ------------------ CATEGORIES ---------------------------------------
		$this->write_to_file('<categories>');
		$cats = $Category->fetchAll(null,null)->toArray();

		foreach ($cats as $cat) {
			$this->write_to_file('<category id="'.$cat['id'].'"'.($cat['parentId'] ? ' parentId="'.$cat['parentId'].'"' : '').'>'.$cat['name'].'</category>');
		}
		$this->write_to_file('</categories>');
		// ---------------------------------------------------------------------

		// ------------------- OFFERS ------------------------------------------
		$this->write_to_file('<offers>');

		$st = $Product->get_all_by_fetch_each();

		while ($toy = $st->fetch()) {
			$this->write_to_file('<offer id="'.$toy['id'].'" type="vendor.model" available="true">');
				$this->write_to_file('<url>'.DOMAIN_PATH.'/toy/detail/toy_id/'.$toy['id'].'</url>');
				$this->write_to_file('<price>'.$toy['price'].'</price>');
				$this->write_to_file('<currencyId>RUR</currencyId>');
				$this->write_to_file('<categoryId>'.$toy['categoryId'].'</categoryId>');
				$this->write_to_file('<picture>'.$toy['picture'].'</picture>');
				$this->write_to_file('<vendor><![CDATA['.$toy['producer'].']]></vendor>');
				$this->write_to_file('<model><![CDATA['.$toy['name'].']]></model>');
				$this->write_to_file('<description><![CDATA['.$toy['description'].']]></description>');
			$this->write_to_file('</offer>');
		}
		$this->write_to_file('</offers>');
		// ----------------------------------------------------------

		$this->write_to_file('</shop>');
		$this->write_to_file('</yml_catalog>');

		fclose($this->f);

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		print_r('OK');
	}

	public function oldgetAction()
	{
		$c_count = 1000;
		$Product = new Product();
		$Category = new Category();
		$i=0;

		$Tempval = new Tempval();
		$last_update = $Tempval->get('last-update');
		$last_update = $last_update['val'];
		$last_update = ($last_update ? $last_update : time()-24*60*60);

		$res = '';
		$res .= '<?xml version="1.0" encoding="windows-1251"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd">';
		$res .= '<yml_catalog date="'.date('Y-m-d H:i',$last_update).'">';
		$res .= '<shop>';
		$res .= '<name>Интернет-магазин Игрушки деткам.РУ</name>';
		$res .= '<url>http://igrushki-detkam.ru</url>';

		// ------------------- CURRENCY ----------------------------------------
		$res .= '<currencies><currency id="RUR" rate="1"/></currencies>';

		// ------------------ CATEGORIES ---------------------------------------
		$res .= '<categories>';
		$cats = $Category->fetchAll(null,null)->toArray();

		foreach ($cats as $cat) {
			$res .= '<category id="'.$cat['id'].'"'.($cat['parentId'] ? ' parentId="'.$cat['parentId'].'"' : '').'>'.$cat['name'].'</category>';
		}
		$res .= '</categories>';
		// ---------------------------------------------------------------------

		// ------------------- OFFERS ------------------------------------------
		$res .= '<offers>';

		$st = $Product->get_all_by_fetch_each();

		while ($toy = $st->fetch()) {
			$res .= '<offer id="'.$toy['id'].'" type="vendor.model" available="true">';
				$res .= '<url>'.DOMAIN_PATH.'/toy/detail/toy_id/'.$toy['id'].'/</url>';
				$res .= '<price>'.$toy['price'].'</price>';
				$res .= '<currencyId>RUR</currencyId>';
				$res .= '<categoryId>'.$toy['categoryId'].'</categoryId>';
				$res .= '<picture>'.$toy['picture'].'</picture>';
				$res .= '<vendor><![CDATA['.$toy['producer'].']]></vendor>';
				$res .= '<model><![CDATA['.$toy['name'].']]></model>';
				$res .= '<description><![CDATA['.$toy['description'].']]></description>';
			$res .= '</offer>';
		}
		$res .= '</offers>';
		// ----------------------------------------------------------

		$res .='</shop>';
		$res .= '</yml_catalog>';

		$f = fopen(FTP_PATH.'/ymlcatalog.xml','wb');
		fwrite($f,$res);
		fclose($f);

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		print_r('OK');
	}
}
?>