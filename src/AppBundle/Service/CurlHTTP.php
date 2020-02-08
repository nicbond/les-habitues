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
	
	public function curlPost(\AppBundle\Entity\Shop $shop, $url, $data = NULL, $headers = NULL)
	{		
		$method = 'POST';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		if (!empty($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, true)
		}
		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$request = curl_exec($ch);
		$error = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
    
		if ($error !== '') {
			throw new \Exception($error);
		}
		
		$response = $this->getHttpCode(\AppBundle\Entity\Shop $shop, $httpCode, $request, $method);

		return $response;
	}
	
	public function getHttpCode($httpCode, $request, $method)
    {	
		$data = json_decode($request);
		$response = null;
		
		switch ($httpCode) {
			case 200:
				$response = new Response('ACTION ON SHOP SUCCEED', Response::HTTP_OK);
				break;
			case 201:
				if ($method == 'POST') {
						//Ici je recupérerai l'id_shop de votre base de données.
						$shop->setIdShop($data['data'][0]['objectID']);
						$this->entityManager->merge($shop);
						$this->entityManager->flush();
					}
				$response = new Response('SHOP CREATED', Response::HTTP_CREATED);
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
