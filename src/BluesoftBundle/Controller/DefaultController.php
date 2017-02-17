<?php

namespace BluesoftBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use BluesoftBundle\Form\SpreadsheetType;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $req)
    {
        $form = $this->createForm(SpreadsheetType::class);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $validator = $this->get('xls.validator');
            /** @var Form $validated_form */
            $form = $validator->validateFile($form);

            if ($form->isValid()) {
                $agent = $this->get('xls.agent');

                try {
                    $agent->validateAndParse($form);
                } catch(\Exception $e) {
                    dump($e);
                }
            }
        }

        return $this->render(
            'BluesoftBundle:Default:index.html.twig',
            ['form' => $form->createView()]
        );
    }
}
