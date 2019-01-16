<?php
	$oArchiveView = getView('archive');

	class archiveMobile extends archiveView {

		function init()
		{
			$oDocumentModel = getModel('document');
			 Context::set('categories', $oDocumentModel->getCategoryList($this->module_srl));

			$template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
			if(!is_dir($template_path)||!$this->module_info->mskin) {
				$this->module_info->mskin = 'default';
				$template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
			}
			$this->setTemplatePath($template_path);

			Context::addJsFile($this->module_path.'tpl/js/archive.js');
		}

		function dispArchiveIndex()
		{
			parent::dispArchiveIndex();
			$this->setTemplateFile('index.html');
		}


		function dispArchiveCategory()
		{
			$oDocumentModel = getModel('document');
			Context::set('category_list', $oDocumentModel->getCategoryList($this->module_srl));
			$this->setTemplateFile('category.html');
		}
	}
?>
