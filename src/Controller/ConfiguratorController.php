<?php

namespace App\Controller;

use App\Entity\Configurator;
use App\Form\ConfiguratorType;
use App\Repository\ConfiguratorRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/configurator')]
class ConfiguratorController extends AbstractController
{

    #[Route('/', name: 'app_configurator_index', methods: ['GET'])]
    public function index(ConfiguratorRepository $configuratorRepository): Response
    {
        return $this->render('configurator/index.html.twig', [
            'configurators' => $configuratorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_configurator_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ConfiguratorRepository $configuratorRepository, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $configurator = new Configurator();
        $form = $this->createForm(ConfiguratorType::class, $configurator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 

            /// PRODUCTS ///
            $computer = $form->get('ordinateur')->getData();
            $configurator->addProduct($computer); 

            $bag = $form->get('sacoche')->getData();
            $configurator->addProduct($bag); 

            $mouse = $form->get('souris')->getData();
            $configurator->addProduct($mouse); 

            $keyboard = $form->get('clavier')->getData();
            $configurator->addProduct($keyboard); 

            /// TOTAL PRICE ///
            $products = $configurator->getProduct(); 
            $totalPrice = 0; 
            foreach ($products as $product) {
                $price = $product->getPrice();
                $totalPrice += $price;  
            }
            $configurator->setTotalPrice($totalPrice); 

            /// USER ///
            $user = $userRepository->find($this->getUser());
            $user->addConfigurator($configurator); 

            $em->persist($configurator);
            $em->flush();

            $configuratorID = $configurator->getId(); 

            return $this->redirectToRoute('app_configurator_show', ['id' => $configuratorID], Response::HTTP_SEE_OTHER);
        }

        return $this->render('configurator/new.html.twig', [
            'configurator' => $configurator,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_configurator_show', methods: ['GET'])]
    public function show(Configurator $configurator): Response
    {
        $products = $configurator->getProduct(); 

        return $this->render('configurator/show.html.twig', [
            'configurator' => $configurator,
            'products' => $products, 
        ]);
    }

    #[Route('/{id}/edit', name: 'app_configurator_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Configurator $configurator, ConfiguratorRepository $configuratorRepository): Response
    {
        $form = $this->createForm(ConfiguratorType::class, $configurator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $configuratorRepository->save($configurator, true);

            return $this->redirectToRoute('app_configurator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('configurator/edit.html.twig', [
            'configurator' => $configurator,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_configurator_delete', methods: ['POST'])]
    public function delete(Request $request, Configurator $configurator, ConfiguratorRepository $configuratorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $configurator->getId(), $request->request->get('_token'))) {
            $configuratorRepository->remove($configurator, true);
        }

        return $this->redirectToRoute('app_configurator_index', [], Response::HTTP_SEE_OTHER);
    }
}
