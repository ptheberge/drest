<?php
namespace Drest\Service;


use Drest\DrestException;

use Doctrine\ORM\EntityManager,
	Drest\Response,
	Drest\Request,
	Drest\Mapping\RouteMetaData;

class AbstractService
{

    /**
     * Doctrine Entity Manager
     * @var \Doctrine\ORM\EntityManager $em
     */
    protected $em;

	/**
	 * Drest request object
	 * @var \Drest\Request $request
	 */
	protected $request;

	/**
	 * Drest response object
	 * @var \Drest\Response $response
	 */
	protected $response;

	/**
	 * When a route object is matched, it's injected into the service class
	 * @var Drest\Mapping\RouteMetaData $route
	 */
	protected $matched_route;


    /**
     * Initialise a new instance of a Drest service
     * @param \Doctrine\ORM\EntityManager $em The EntityManager to use.
     * @param Drest\Request $request
     * @param Drest\Response $response
     */
    public function __construct(EntityManager $em, Request $request, Response $response)
    {
        $this->em = $em;

        $this->setRequest($request);
        $this->setResponse($response);
    }

	/**
	 * Inspects the request object and returns the default service method based on the entity type and verb used
	 * Eg. a GET request to a single element will return getElement()
	 * 	   a GET request to a collection element will return getCollection()
	 * 	   a POST request to a single element will return postElement()
	 * @return string $methodName
	 */
	public function getDefaultMethod()
	{
	    if (!$this->matched_route instanceof RouteMetaData)
	    {
            DrestException::noMatchedRouteSet();
	    }

	    $functionName = '';
	    $httpMethod = $this->request->getHttpMethod();
	    switch ($httpMethod)
	    {
	        case Request::METHOD_OPTIONS:
            case Request::METHOD_TRACE:
                $functionName = strtolower($this->request->getHttpMethod()) . 'Request';
	            break;
            case Request::METHOD_CONNECT:
            case Request::METHOD_PATCH:
            case Request::METHOD_PROPFIND:
            case Request::METHOD_HEAD:
                //@todo: support implementation for these
                break;
            default:
                $functionName = strtolower($this->request->getHttpMethod());
                $functionName .= ucfirst(strtolower(RouteMetaData::$contentTypes[$this->matched_service->getContentType()]));
                break;
	    }
	    return $functionName;
	}

	/**
	 * Inject the request object into the service
	 * @param Drest\Request $request
	 */
	public function setRequest(Request $request)
	{
	    $this->request = $request;
	}

	/**
	 * Inject the response object into the service
	 * @param Drest\Response $response
	 */
	public function setResponse(Response $response)
	{
	    $this->response = $response;
	}

	/**
	 * Set the matched route object
	 * @param Drest\Mapping\RouteMetaData $matched_route
	 */
	public function setMatchedRoute(RouteMetaData $matched_route)
	{
        $this->matched_route = $matched_route;
	}

	/**
	 * Get the route object that was matched
	 * @return Drest\Mapping\RouteMetaData $matched_route
	 */
	public function getMatchedRoute()
	{
	    return $this->matched_route;
	}

}