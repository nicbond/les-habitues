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
     * @ParamConverter("shop", converter="fos_rest.request_body")
     */
    public function createShopAction(Shop $shop, Request $request)
    {
		$data = $request->getContent();
		$url = 'url non donnée';
		
		$em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($ch);
		$datas = json_decode($response);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		switch ($httpCode) {
			case 200:
				$error_status = "200: Success";
				return ($datas);
				break;
			case 404:
				$error_status = "404: API Not found";
				break;
			case 500:
				$error_status = "500: servers replied with an error.";
				break;
			case 502:
				$error_status = "502: servers may be down or being upgraded. Hopefully they'll be OK soon!";
				break;
			case 503:
				$error_status = "503: service unavailable. Hopefully they'll be OK soon!";
				break;
			default:
				$error_status = "Undocumented error: " . $httpCode . " : " . curl_error($curl);
				break;
		}
		curl_close($curl);
		//echo $error_status;
		//die;
		
		if ($httpCode < 400) {
			// ici je récupére votre id_shop dans les datas et je ferai un merge de mon entité déjà créé.
			$em->merge($shop);
			$em->flush();
			$response = new Response('SHOP CREATED', Response::HTTP_CREATED);
		} else {
			$em->remove($shop);
			$em->flush();
			$response = new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		return $response;
		
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