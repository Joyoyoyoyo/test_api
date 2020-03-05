<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Subscription;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends AbstractFOSRestController
{


     /**
     * @Rest\Get(
     *     path = "/subscription/{id}",
     *     name = "app_subscription_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200)
     * @ParamConverter("subscription", class="AppBundle:Subscription", options={"mapping": {"id":"contact"}})
     */
    public function showAction(Subscription $subscription)
    {      
        return $subscription;
    }

        /**
     * @Rest\Post(
     *    path = "/subscription",
     *    name = "app_subscription_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("subscription", converter="fos_rest.request_body")
     */
    public function createAction(Subscription $subscription, Request $request)
    {
        
        $contactId = $request->get("contact")['id'];
        $productId = $request->get("product")['id'];

        $subscription = $this->modifyContactAndProduct($subscription,$contactId,$productId);

        $em = $this->getDoctrine()->getManager();
        $em->persist($subscription);
        $em->flush();
        
    }

    /**
     * @Rest\Put(
     *    path = "/subscription/{id}",
     *    name = "app_subscription_modify",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("subscription", converter="fos_rest.request_body")
     */
    public function putAction(Subscription $subscription, int $id,  Request $request)
    {
       
        $contactId = $request->get("contact")['id'];
        $productId = $request->get("product")['id'];

        if($this->getDoctrine()->getRepository(Subscription::class)->find($id)){
            $initialSubscription = $this->getDoctrine()->getRepository(Subscription::class)->find($id);

            $initialSubscription = $this->modifyContactAndProduct($initialSubscription,$contactId,$productId);

            $initialSubscription->setBeginDate($subscription->getBeginDate());
            $initialSubscription->setEndDate($subscription->getEndDate());
        } else {
            $initialSubscription=$subscription;
            $initialSubscription = $this->modifyContactAndProduct($initialSubscription,$contactId,$productId);
        }
     
        $em = $this->getDoctrine()->getManager();

        $em->persist($initialSubscription);
        $em->flush();
        
    }

    /**
     * @Rest\Delete(
     *     path = "/subscription/{id}",
     *     name = "app_subscription_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Subscription $subscription)
    {      
        if($subscription){
            $em = $this->getDoctrine()->getManager();

            $em->remove($subscription);
            $em->flush();

        }
       
    }

    public function modifyContactAndProduct(Subscription $subscription, int $contactID, int $productID)
    {
        $contactRepo = $this->getDoctrine()->getRepository(Contact::class);
        $productRepo = $this->getDoctrine()->getRepository(Product::class);
        if($contactRepo->find($contactID)){
            $subscription->setContact($contactRepo->find($contactID));
        } else {
            return new Response(
                "Le contact n'existe pas"
            );
        }

        if($productRepo->find($productID)){
            $subscription->setProduct($productRepo->find($productID));
        } else {
            return new Response(
              "Le produit n'existe pas"
            );
        }

        return $subscription;
    }

}
