<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Shop;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\RestBundle\Request\ParamFetcherInterface;

class ShopController extends FOSRestController
{
	/**
     * @Rest\Get("v2/shops", name="app_shops_list")
     */
    public function shopAction()
    {
		$url='https://www.leshabitues.fr/testapi/shops';
	
		$opts = array(
			'https'=>array(
			'method'=>'GET'));
		
		$ctx = stream_context_create($opts);
		$requete = file_get_contents($url,false,$ctx);
		$datas = json_decode($requete,true);

		$size = count($datas['data']);
		$size = $size-1;
		
		$em = $this->getDoctrine()->getManager();
		
		$i = 0;

		do {
			$shop = new Shop();
			$shop->setNameShop($datas['data'][$i]['chain']);
			$shop->setAdress($datas['data'][$i]['localisations'][0]['address']);
			$shop->setZipCode($datas['data'][$i]['localisations'][0]['zipcode']);
			$shop->setCity($datas['data'][$i]['localisations'][0]['city']);
			$shop->setImage($datas['data'][$i]['picture_url']);
			$shop->setOffer($datas['data'][$i]['offers'][0]['reduction']);
			$shop->setIdShop($datas['data'][$i]['objectID']);
			
			$em->persist($shop);
			$em->flush();
			
			$i++;
		} while ($i <= $size);
		return new Response('CREATED SHOPS', Response::HTTP_CREATED);
    }
}