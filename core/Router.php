<?php

namespace core;

use core\models\MainFunc;

class Router
{
	/**
	 * @var string
	 */
    public string $controller = '';

	/**
	 * @var string
	 */
    public string $controllerClassName = '';
	/**
	 * @var string
	 */
    public string $action = '';
	/**
	 * @var Request
	 */
    public Request $request;
	/**
	 * @var Response
	 */
    public Response $response;

	/**
	 * @return $this
	 */
	public function route(): self
	{
		$this->setApplicationType();
		$this->setRequest();
		$this->setResponse();
		$this->setController();
		$this->setAction();

		return $this;
	}

	/**
	 *
	 */
	private function setController(): void {

		$parsedPath = explode('/', $_SERVER['PHP_SELF']);

		$this->controller = $parsedPath[1] ?? '';
		$this->controllerClassName = $this->getControllerClassName();

	}

	/**
	 * Converts controller's file name into an appropriate class name
	 */
	private function getControllerClassName(): string {

		$appInstance = Application::getInstance();
		$moduleNameSpace = str_replace('/', '\\', str_replace($appInstance->getAppRootPath(), '', $appInstance->getModulePath()));
		$nameSpace = $moduleNameSpace . '\\' . $this->convertToConventional($this->controller) . '\\controllers';
		return MainFunc::isAjax() || $appInstance->getAppType() == Application::APP_TYPE_REST_API ? "{$nameSpace}\ServiceController" : "{$nameSpace}\ActionController";
	}

	/**
	 * @param string $str
	 * @return string
	 */
	private function convertToConventional(string $str): string {

		// Convert action name into a conventional method name
		$str = str_replace('-', ' ', $str);
		$str = ucwords($str);
		$str = lcfirst($str);
		return str_replace(' ', '', $str);
	}

	/**
	 *
	 */
	private function setAction(): void {

		$parsedPath = explode('/', $_SERVER['PHP_SELF']);

		$rawActionName = $parsedPath[2] ?? '';

		if(Application::getInstance()->getAppType() == Application::APP_TYPE_REST_API) {
			$actionName = strtolower($this->request->requestMethod) . "-" . $rawActionName;
		}
		else { //mvc
			$actionName = $rawActionName ?: 'index';
		}

		$this->action = $this->convertToConventional($actionName);

	}

	/**
	 *
	 */
	private function setRequest(): void {
		$this->request = Request::getInstance();
	}

	/**
	 *
	 */
	private function setResponse(): void {
		$this->response = Response::getInstance();
	}

	/**
	 *
	 */
	private function setApplicationType(): void {
		if(MainFunc::isAjax() || Application::getInstance()->getAppType() == Application::APP_TYPE_REST_API) {
			$applicationType = "application/json";
		}
		else {
			$applicationType = "text/html";
		}

		header("Content-type: {$applicationType}; charset=UTF-8");
	}

}