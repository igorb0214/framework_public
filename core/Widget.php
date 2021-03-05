<?php

namespace core;

abstract class Widget
{

	/**
	 * @param $widgetName
	 * @param $data
	 * @param bool $return
	 * @return mixed
	 */
	public function runWidget(string $widgetName, array $data, $return = true) {

		$appInstance = Application::getInstance();

		$widgetNameSpace = str_replace("/", "\\", str_replace($appInstance->getAppRootPath(), '', $appInstance->getWidgetPath())) . "\\" . lcfirst($widgetName);

		$widgetClassName = $widgetNameSpace . "\\" . $widgetName;

		$widget = new $widgetClassName($data);
		$widget->viewPath  = $appInstance->getAppRootPath() . $widgetNameSpace . "/views/";

		foreach($data as $key=>$value){
			$widget->$key = $value;
		}

		return $widget->load($return);

	}

	/**
	 * @param string|null $viewFile
	 * @param array|null $data
	 * @param bool $return
	 * @param string|null $layout
	 * @return string|null
	 */
	protected function renderLayout(?string $viewFile = null, ?array $data= null, $return = false, string $layout = null) {
		if($viewFile === null) return false;

		$view = $this->renderInternal($this->viewPath . "/" . $viewFile, $data);
		if($layout){
			$data['nested'] = $view;
			$view = $this->renderInternal(Application::getInstance()->getAppRootPath() . $layout, $data);
		}
		if($return) {
			return $view;
		}
		echo $view;
	}

	/**
	 * @param string $viewFile
	 * @param array $data
	 * @return false|string
	 */
	private function renderInternal(string $viewFile, array $data = []) {
		extract($data);
		ob_start();
		require(replaceSlashes($viewFile));
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}


}
