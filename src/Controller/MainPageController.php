<?php

namespace App\Controller;

use App\Form\CityType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class MainPageController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function getData(Request $request)
    {
        $form = $this->createForm(CityType::class);


        return $this->render('app/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
