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
}
