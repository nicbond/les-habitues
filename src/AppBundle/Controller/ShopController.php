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
use Symfony\Component\HttpFoundation\Request;

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
			$id_shop = $datas['data'][$i]['objectID'];
			$shopSearch = $em->getRepository('AppBundle:Shop')->findOneBy(array('id_shop' => $id_shop));

				try {
						if (is_null($shopSearch)) {
							$shop = new Shop();
							$shop->setNameShop($datas['data'][$i]['chain']);
							$shop->setAdress($datas['data'][$i]['localisations'][0]['address']);
							$shop->setZipCode($datas['data'][$i]['localisations'][0]['zipcode']);
							$shop->setCity($datas['data'][$i]['localisations'][0]['city']);
							$shop->setImage($datas['data'][$i]['picture_url']);
							$shop->setOffer($datas['data'][$i]['offers'][0]['reduction']);
							$shop->setIdShop($datas['data'][$i]['objectID']);
							$em->persist($shop);
						} else {
							$shopBDD = $em->getRepository('AppBundle:Shop')->find($shopSearch->getId());
							$shopBDD->setNameShop($datas['data'][$i]['chain']);
							$shopBDD->setAdress($datas['data'][$i]['localisations'][0]['address']);
							$shopBDD->setZipCode($datas['data'][$i]['localisations'][0]['zipcode']);
							$shopBDD->setCity($datas['data'][$i]['localisations'][0]['city']);
							$shopBDD->setImage($datas['data'][$i]['picture_url']);
							$shopBDD->setOffer($datas['data'][$i]['offers'][0]['reduction']);
							$shopBDD->setIdShop($datas['data'][$i]['objectID']);
							$em->merge($shopBDD);
						}
						$em->flush();
					} catch (\Doctrine\ORM\ORMException $e) {
						$errorMsg = 'Error Doctrine for the id_shop '.$datas['data'][$i]['objectID'].'<br/>'.$e->getMessage();
					}
			$i++;
		} while ($i <= $size);
		return new Response('CREATED / UPDATED SHOPS', Response::HTTP_CREATED);
    }
	
	/**
     * @Rest\Delete("v2/shops/{id}", name="app_shop_delete")
     */
    public function deleteAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$shop = $em->getRepository('AppBundle:Shop')->find($id); //afin de récupérer le id_shop !
		
		if (empty($shop)) {
            return new Response('SHOP NOT FOUND', Response::HTTP_NOT_FOUND);
        } else {
			$em->remove($shop);
			$em->flush();
			return new Response('SHOP DELETED', Response::HTTP_OK);
		}
	}
	
	/**
     * @Rest\Post(
     *    path = "v2/shops",
     *    name = "app_shops_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("shop", converter="fos_rest.request_body")
     */
    public function createShopAction(Shop $shop)
    {
		$em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();
		
		return $shop;
    }
	
	/**
     * @Rest\Put("v2/shops/{id}", name="app_shop_update")
     */
	public function updateAction($id, Request $request)
	{
		$data = json_decode($request->getContent());
		$em = $this->getDoctrine()->getManager();

		$shop = $em->getRepository('AppBundle:Shop')->find($id);
		
		if (empty($shop)) {
            return new Response('SHOP NOT FOUND', Response::HTTP_NOT_FOUND);
        } else {	
			if (!empty($data->name_shop)){
				$shop->setNameShop($data->name_shop);
			}	
			if (!empty($data->adress)){
				$shop->setAdress($data->adress);
			}
			if (!empty($data->zip_code)){
				$shop->setZipCode($data->zip_code);
			}
			if (!empty($data->city)){
				$shop->setCity($data->city);
			}
			if (!empty($data->image)){
				$shop->setImage($data->image);
			}
			if (!empty($data->offer)){
				$shop->setOffer($data->offer);
			}
			$em->merge($shop);
			$em->flush();
			return new Response('SHOP UPDATED', Response::HTTP_OK);
		}
	}
}