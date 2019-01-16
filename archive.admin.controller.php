<?php
/**
 * @class  archiveAdminController
 * @author NAVER (developers@xpressengine.com)
 * @brief  archive admin controller class
 **/

class archiveAdminController extends archive {

	function init(){
	}

	function procArchiveAdminInsert(){
		$oModuleController = getController('module');
		$oModuleModel = getModel('module');

		$args = Context::getRequestVars();
		$args->module = 'archive';
		$args->mid = $args->archive_name;
		unset($args->body);
		unset($args->archive_name);

		$args->use_category = 'N';

		if($args->module_srl){
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
			if($module_info->module_srl != $args->module_srl) unset($args->module_srl);
		}

		if(!$args->module_srl){
			$output = $oModuleController->insertModule($args);
			$msg_code = 'success_registed';
		}
		else{
			$output = $oModuleController->updateModule($args);
			$msg_code = 'success_updated';
		}

		if(!$output->toBool()) return $output;

		if(Context::get('success_return_url')){
			changeValueInUrl('mid', $args->mid, $module_info->mid);
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else{
			$this->setRedirectUrl(getNotEncodedUrl('','module','admin','act','dispArchiveAdminInsert','module_srl',$output->get('module_srl')));
		}
		$this->setMessage($msg_code);
	}

	function procArchiveAdminDelete(){
		$oModuleController = getController('module');

		$args->module_srl = $module_srl = Context::get('module_srl');

		$output = executeQuery('archive.deleteDependency', $args);
		if(!$output->toBool()) return $output;

		$output = executeQuery('archive.deleteItems', $args);
		if(!$output->toBool()) return $output;

		$output = executeQuery('archive.deletePackages', $args);
		if(!$output->toBool()) return $output;

		$output = $oModuleController->deleteModule($module_srl);
		if(!$output->toBool()) return $output;

		if(Context::get('success_return_url')){
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else{
			$this->setRedirectUrl(getNotEncodedUrl('','module','admin','act','dispArchiveAdminList'));
		}
		$this->setMessage('success_deleted');
	}

	function procArchiveAdminDeletePackage(){
		$oArchiveModel = getModel('archive');
		$oDocumentController = getController('document');
		$oFileController = getController('file');

		$site_module_info = Context::get('site_module_info');
		if(!$this->module_srl) return new Object(-1,'msg_invalid_request');
		$package_srl = Context::get('package_srl');
		if(!$package_srl) return new Object(-1,'msg_invalid_request');
		$selected_package = $oArchiveModel->getPackage($this->module_srl, $package_srl);
		if(!$selected_package->package_srl) return new Object(-1,'msg_invalid_request');

		$args->package_srl = $package_srl;
		$args->module_srl = $this->module_srl;
		$output = executeQuery('archive.deletePackage', $args);
		if(!$output->toBool()) return $output;

		$output = executeQueryArray('archive.getItems', $args);
		if(!$output->toBool()) return $output;
		if($output->data){
			foreach($output->data as $key => $val){
			   if($val->document_srl) $oDocumentController->deleteDocument($val->document_srl,true);
			   $file = $oFileController->deleteFile($val->file_srl);
			}
		}

		$output = executeQuery('archive.deleteItems', $args);
		if(!$output->toBool()) return $output;

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'),'act','dispArchiveManage'));
	}
}
