<?php 

namespace core;

abstract class Controller extends Widget
{
	/**
	 * @var Request
	 */
	protected Request $request;
	/**
	 * @var Response
	 */
	protected Response $response;
	/**
	 * @var string
	 */
	private string $controllerName;
	/**
	 * @var string
	 */
	private string $actionName;
	/**
	 * @var string
	 */
	protected string $viewPath;
	/**
	 * @var array
	 */
	protected array $scriptSrc = [];
	/**
	 * @var array
	 */
	protected array $styleSheetLink = [];


	/**
	 * Controller constructor.
	 */
	public function __construct() {}
	protected function beforeAction() {}
	protected function afterAction() {}

	/**
	 *
	 */
	public function run(): void {
		$this->beforeAction();
		$this->{$this->actionName}();
		$this->afterAction();
	}


	/**
	 * @param string $controllerName
	 */
	public function setControllerName(string $controllerName): void {
		$this->controllerName = $controllerName;
	}

	/**
	 * @param Request $request
	 */
	public function setRequest(Request $request): void {
		$this->request = $request;
	}

	/**
	 * @param Response $response
	 */
	public function setResponse(Response $response): void {
		$this->response = $response;
	}

	/**
	 * @param string $actionName
	 */
	public function setActionName(string $actionName): void {
		$this->actionName = $actionName;
	}

	/**
	 * @param string $viewPath
	 */
	public function setViewPath(string $viewPath): void {
		$this->viewPath = $viewPath;
	}

	/**
	 * @param string $scriptSrc
	 */
	public function setScriptSrc(string $scriptSrc): void {
		$this->scriptSrc[] = $scriptSrc;
	}

	/**
	 * @return array
	 */
	public function getScriptSrc(): array {
		return $this->scriptSrc;
	}

	/**
	 * @param string $link
	 */
	public function setStyleSheetLink(string $link): void {
		$this->styleSheetLink[] = $link;
	}

	/**
	 * @return array
	 */
	public function getStyleSheetLink(): array {
		return $this->styleSheetLink;
	}

}
