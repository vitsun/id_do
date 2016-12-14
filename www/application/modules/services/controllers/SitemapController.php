<?php
class Services_SitemapController extends Zend_Controller_Action
{
	var $cur_res_str = "";
	var $cur_i = 0;
	var $max_i = 40000;
	var $file_name = "";
	var $cur_ind = 0;

	public function preDispatch()
	{
	}

	public function indexAction()
	{
	}

	private function xml_f_write($str)
	{
		$f = fopen($this->file_name.($this->cur_ind ? '_'.$this->cur_ind : '').'.xml','w');
		fwrite($f,'<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
		fwrite($f,$str);
		fwrite($f,'</urlset>');
		fclose($f);
	}

	private function make_res($ar,$need_save=false)
	{
		$this->cur_i ++;

		$this->cur_res_str .= '<url>';
		$this->cur_res_str .= '<loc>'.$ar['loc'].'/</loc>';
		$this->cur_res_str .= '<lastmod>'.$ar['lastmod'].'</lastmod>';
		$this->cur_res_str .= '<changefreq>weekly</changefreq>';
		$this->cur_res_str .= '<priority>'.$ar['priority'].'</priority>';
		$this->cur_res_str .= '</url>';

		if ( ($this->cur_i >= $this->max_i) || ($need_save) ) {
			$this->xml_f_write($this->cur_res_str);
			$this->cur_res_str = "";
			$this->cur_i = 0;
			$this->cur_ind ++;
		}
	}

	public function getAction()
	{
		$this->file_name = FTP_PATH."/sitemap";
		$c_count = 1000;
		$Product = new Product();
		$Category = new Category();
		$i=0;

		$Tempval = new Tempval();
		$last_update = $Tempval->get('last-update');
		$last_update = $last_update['val'];
		$last_update = ($last_update ? $last_update : time()-24*60*60);

		$res = '';
		/*$res .= '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';*/

		// ------------------- TOYS -------------------------------
		$off = 0;

		do {
			$toys = $Product->fetchAll(null,null,$c_count,$off)->toArray();
			$i+=count($toys);
			$off+=$c_count;

			foreach ($toys as $toy) {
				$this->make_res(array(
					'loc' => DOMAIN_PATH.'/toy/detail/toy_id/'.$toy['id'],
					'lastmod' => date('Y-m-d',$last_update),
					'priority' => '0.8'
					));
				/*
				$res .= '<url>';
				$res .= '<loc>'.DOMAIN_PATH.'/toy/detail/toy_id/'.$toy['id'].'/</loc>';
				$res .= '<lastmod>'.date('Y-m-d',$last_update).'</lastmod>';
				$res .= '<changefreq>weekly</changefreq>';
				$res .= '<priority>0.8</priority>';
				$res .= '</url>';
				*/
			}
		} while (count($toys) > 0);

		// ----------------------------------------------------------

		// ------------------ CATEGORIES ----------------------------
		$off = 0;
		do {
			$cats = $Category->fetchAll(null,null,$c_count,$off)->toArray();
			$off+=$c_count;

			foreach ($cats as $cat) {
				$this->make_res(array(
					'loc' => DOMAIN_PATH.'/category/detail-list/cat_id/'.$cat['id'],
					'lastmod' => date('Y-m-d',$last_update),
					'priority' => '0.5'
					));
				/*
				$res .= '<url>';
				$res .= '<loc>'.DOMAIN_PATH.'/category/detail-list/cat_id/'.$cat['id'].'/</loc>';
				$res .= '<lastmod>'.date('Y-m-d',$last_update).'</lastmod>';
				$res .= '<changefreq>weekly</changefreq>';
				$res .= '<priority>0.5</priority>';
				$res .= '</url>';
				*/
			}
		} while (count($cats) > 0);
		// ----------------------------------------------------------

		// ----------- MAIN PAGE ------------------------------------
			$this->make_res(array(
				'loc' => DOMAIN_PATH,
				'lastmod' => date('Y-m-d',$last_update),
				'priority' => '0.9'
				),true);
				/*
				$res .= '<url>';
				$res .= '<loc>'.DOMAIN_PATH.'/</loc>';
				$res .= '<lastmod>'.date('Y-m-d',$last_update).'</lastmod>';
				$res .= '<changefreq>weekly</changefreq>';
				$res .= '<priority>0.9</priority>';
				$res .= '</url>';
				*/
		// ----------------------------------------------------------

		//$res .='</urlset>';

		/*
		$f = fopen(FTP_PATH.'/sitemap.xml','wb');
		fwrite($f,$res);
		fclose($f);
		*/

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		print_r('OK');
	}
}
?>