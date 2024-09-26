<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Form\ProductType;
use App\Services\GlobalVariableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/instance/{instance}/products/', name: 'app_instance_product_')]
class ProductController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private GlobalVariableService $globalVariableService
    ) {
    }

    #[Route('create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {

        $current_instance = $this->globalVariableService->get('current_instance');

        if($this->getUser() == null) {
            return $this->redirectToRoute('app_login', ['instance' => $current_instance->getName() ]);
        }

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le téléchargement des fichiers
            foreach ($form->get('image')->getData() as $imageForm) {

                $uploadedFile = $imageForm->getPathName();

                $extension = image_type_to_extension(
                    getimagesize($uploadedFile)[2]
                );

                if ($uploadedFile) {
                    $newFilename = uniqid() . $extension;

                    // Déplace le fichier vers le répertoire des images
                    rename(
                        $uploadedFile,
                        $this->getParameter('images_directory') . '/' . $newFilename
                    );

                    // Crée une nouvelle image
                    $productImage = new ProductImage();
                    $productImage->setPath($newFilename);
                    $product->addImage($productImage);

                    $product->setInstance($current_instance);
                    $product->setRentBy($this->getUser());
                }
            }

            // Enregistrer le produit
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_instance', ['instance' => $current_instance->getName()]);
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
