<?php

namespace BluesoftBundle\Controller;

use BluesoftBundle\Form\SpreadsheetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $form = $this->createForm(SpreadsheetType::class);

        if ($form->isSubmitted()) {
            dump($form->getData());
        }

        return $this->render(
            'BluesoftBundle:Default:index.html.twig',
            ['form' => $form->createView()]
        );
    }
}
