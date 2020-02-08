<?php

namespace AppBundle\Curl;

use AppBundle\Entity\Shop;
use Doctrine\ORM\EntityManager;
use \Symfony\Component\DependencyInjection\Container;

/**
 * Classe permettant de faire différents appels CURL
 *
 * @author Nicolas MARTINS
 */
class CurlHTTP
{
    /**
     *
     * @var Container
     */
    private $container;
	
	/**
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     *
     * @param EntityManager $entityManager
     * @param TransactionValidators $validators
     */
    public function __construct(Container $container, EntityManager $entityManager)
    {
		$this->container = $container;
        $this->entityManager = $entityManager; 
    }
	
	public function getHttpCode($httpCode, $request)
    {	
		$response = null;
		
		switch ($httpCode) {
			case 200:
				$response = new Response('ACTION SUCCESS', Response::HTTP_OK);
				break;
			case 201:
				$response = new Response('SHOP CREATED OR UPDATED', Response::HTTP_CREATED);
				break;
			case 404:
				$response = new Response('API NOT FOUND', Response::HTTP_NOT_FOUND);
				break;
			case 500:
				$response = new Response('INTERNAL SERVER ERROR', Response::HTTP_INTERNAL_SERVER_ERROR);
				break;
			default:
				$response = new Response('UNDOCUMENTED ERROR', Response::HTTP_INTERNAL_SERVER_ERROR);
				break;
		}
		return $response;
	}
}
