<?php
/**
 * @class  archiveAPI
 * @author NAVER (developers@xpressengine.com)
 * @brief  archive API class
 **/

class archiveAPI extends archive {

	function getArchiveItems(){
		$package_srl = Context::get('package_srl');
		$module_srl = Context::get('module_srl');
		$mid = Context::get('mid');
		$list_count = Context::get('list_count');
		$site_srl = Context::get('site_srl');

		if(!$package_srl) return;
		if(!$site_srl) $site_srl = 0;
		if(!$module_srl && $mid){
			$oModuleModel = getModel('module');
			$module_info = $oModuleModel->getModuleInfoByMid($mid, $site_srl);
			$module_srl = $module_info->module_srl;
		}

		if(!$module_srl) return;

		$oModel = getModel('archive');
		$args->module_srl = $module_srl;
		$args->package_srl = $package_srl;
		if($list_count) $args->list_count = $list_count;

		$output = executeQueryArray('archive.getItemsWithDocument', $args);
		$this->add('items', $output->data);
	}

	function dispArchiveIndex(&$oModule){
		$package_categories = Context::get('package_categories');
		$oModule->add('package_categories',$package_categories);

		$categories = Context::get('categories');
		$oModule->add('categories',$categories);

		$latest_package = Context::get('latest_package');
		if($latest_package){
			$oModule->add('latest_package',$latest_package);
			$package = Context::get('package');
			$oModule->add('package',$package);

			$items = Context::get('items');
			if($items){
				$oModule->add('items',$items);
			}
		}
		else{
			$item_list = Context::get('item_list');
			$page_navigation = Context::get('page_navigation');
			$order_target = Context::get('order_target');
			$order_type = Context::get('order_type');

			$oModule->add('item_list',$item_list);
			$oModule->add('page_navigation',$page_navigation);
			$oModule->add('order_type',$order_type);
			$oModule->add('order_target',$order_target);
		}
	}
}
