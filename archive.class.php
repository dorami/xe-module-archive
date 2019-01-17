<?php
/**
 * @class  archive
 * @author NAVER (developers@xpressengine.com)
 * @brief  archive high class
 **/

class archive extends ModuleObject {

	var $licenses = array( 'GPL v2', 'LGPL v2', 'GPL v3', 'LGPL v3', 'New BSD License', 'MPL 1.1', 'Apache License 2.0', 'MIT/X License', 'zlib/libpng License', 'OFL 1.1', '기타 라이선스');

	function moduleInstall(){
		$oModuleController = getController('module');

		$oModuleController->insertTrigger('file.downloadFile', 'archive', 'controller', 'triggerUpdateDownloadedCount', 'after');
		$oModuleController->insertTrigger('menu.getModuleListInSitemap', 'archive', 'model', 'triggerModuleListInSitemap', 'after');
		return $this->makeObject();
	}

	function checkUpdate(){
		$oModuleModel = getModel('module');

		if(!$oModuleModel->getTrigger('file.downloadFile', 'archive', 'controller', 'triggerUpdateDownloadedCount', 'after')) return true;
		// 2012. 09. 11 when add new menu in sitemap, custom menu add
		if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'archive', 'model', 'triggerModuleListInSitemap', 'after')) return true;
		return false;
	}

	function moduleUpdate(){
		$oModuleController = getController('module');
		$oModuleModel = getModel('module');

		if(!$oModuleModel->getTrigger('file.downloadFile', 'archive', 'controller', 'triggerUpdateDownloadedCount', 'after'))
			$oModuleController->insertTrigger('file.downloadFile', 'archive', 'controller', 'triggerUpdateDownloadedCount', 'after');
		// 2012. 09. 11 when add new menu in sitemap, custom menu add
		if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'archive', 'model', 'triggerModuleListInSitemap', 'after'))
			$oModuleController->insertTrigger('menu.getModuleListInSitemap', 'archive', 'model', 'triggerModuleListInSitemap', 'after');

		return $this->makeObject(0, 'success_updated');
	}

	function moduleUninstall(){
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		if($oModuleModel->getTrigger('file.downloadFile', 'archive', 'controller', 'triggerUpdateDownloadedCount', 'after'))
			$oModuleController->deleteTrigger('file.downloadFile', 'archive', 'controller', 'triggerUpdateDownloadedCount', 'after');
		$output = executeQueryArray("archive.getAllArchives");
		if(!$output->data) return $this->makeObject();

		set_time_limit(0);
		foreach($output->data as $archive){
			$oModuleController->deleteModule($archive->module_srl);
		}

		return $this->makeObject();
	}

	function recompileCache(){
	}
	
	public function makeObject($code = 0, $message = 'success'){
		return class_exists('BaseObject') ? new BaseObject($code, $message) : new Object($code, $message);
	}
}
