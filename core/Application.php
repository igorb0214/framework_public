<?php

namespace core;

use core\models\MainFunc;
use core\traits\Singleton;

class Application
{

	use Singleton;

	public const APP_TYPE_MVC      = "mvc";
	public const APP_TYPE_REST_API = "rest_api";

	/**
	 * @var string
	 */
	private string $appType = self::APP_TYPE_MVC;
	/**
	 * @var string
	 */
	private string $appName = '';
	/**
	 * @var Router
	 */
	private Router $appRouter;
	/**
	 * @var string
	 */
	private string $appRootPath = '';
	/**
	 * @var string
	 */
	private string $publicPath = '';
	/**
	 * @var string
	 */
	private string $modulePath = '';
	/**
	 * @var string
	 */
	private string $widgetPath = '';

	/**
	 *
	 */
	public function run(): void {

		// App path must be declared in application's configurations level
		if ($this->modulePath == '' || $this->appRootPath == '') {
			die('Application::run() : Error! Application paths hasn\'t been initialized. Be sure you\'ve initialized it within app\'s configuration file.');
		}

		$this->dispatch();

	}

	public function dispatch(): void {

		$controllerName      = $this->appRouter->controller;
		$actionName          = $this->appRouter->action;
		$controllerClassName = $this->appRouter->controllerClassName;
		$controller          = new $controllerClassName();

		// Check if the requested action is defined for the controller
		if (!method_exists($controller, $actionName)) {
			die('Error! Method ' . $actionName. ' is not defined for a controller ' . $controllerName);
		}

		$controller->setControllerName($controllerClassName);
		$controller->setActionName($actionName);
		$controller->setRequest($this->appRouter->request);
		$controller->setResponse($this->appRouter->response);
		$controller->setViewPath(Application::getInstance()->getModulePath() . "/"  . $controllerName . "/views");

		$this->includeHeader($controller);
		$controller->run();
		$this->includeFooter();

	}

	/**
	 * mvc/rest_api
	 * @param string $appType
	 */
	public function setAppType(string $appType): void {
		$this->appType = $appType;
	}

	/**
	 * @param string $appName
	 */
	public function setAppName(string $appName): void {
		$this->appName = $appName;
	}

	/**
	 * @param string $path
	 */
	public function setAppRootPath(string $path): void {
		$this->appRootPath = $path;
	}

	/**
	 * @param Router $appRouter
	 */
	public function setRouter(Router $appRouter): void {
		$this->appRouter = $appRouter;
	}

	/**
	 * @param string $publicPath
	 */
	public function setPublicPath($publicPath = ''): void {
		$this->publicPath = $publicPath;
	}

	/**
	 * @param string $modulePath
	 */
	public function setModulePath($modulePath = ''): void {
		$this->modulePath = $modulePath;
	}

	/**
	 * @param string $widgetPath
	 */
	public function setWidgetPath($widgetPath = ''): void {
		$this->widgetPath = $widgetPath;
	}

	/**
	 * @return string
	 */
	public function getAppType(): string {
		return $this->appType;
	}

	/**
	 * @return string
	 */
	public function getAppName(): string {
		return $this->appName;
	}

	/**
	 * @return Router|null
	 */
    public function getAppRouter(): ?Router{
        return $this->appRouter;
    }

	/**
	 * @return string
	 */
	public function getAppRootPath(): string {
		return $this->appRootPath;
	}

	/**
	 * @return string
	 */
	public function getPublicPath(): string {
		return $this->publicPath;
	}

	/**
	 * @return string
	 */
	public function getModulePath(): string {
		return $this->modulePath;
	}

	/**
	 * @return string
	 */
	public function getWidgetPath(): string {
		return $this->widgetPath;
	}

	/**
	 * @param Controller $controller - usage inside header
	 */
	private function includeHeader(Controller $controller): void {
		if(!MainFunc::isAjax() && $this->appType==self::APP_TYPE_MVC) {
			$header = Application::getPublicPath() . '/templates/header.php';
			if (file_exists($header)) {
				include_once $header;
			}
		}
	}

	/**
	 *
	 */
	private function includeFooter(): void {
		if (!MainFunc::isAjax() && $this->appType==self::APP_TYPE_MVC) {
			$footer = Application::getPublicPath() . '/templates/footer.php';
			if (file_exists($footer)) {
				include_once $footer;
			}
		}
	}

}