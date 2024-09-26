<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Form\ProductType;
use App\Services\GlobalVariableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/instance/{instance}/user/products/', name: 'app_instance_admin_product_')]
class ProductController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private GlobalVariableService $globalVariableService
    ) {
    }




    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {

        dd('list');

        return $this->redirectToRoute('app_instance', ['instance' => $current_instance->getName()]);
    }


    #[Route('create', name: 'create', methods: ['GET', 'POST'])]
    #[Route('edit/{product}', name: 'edit', methods: ['GET', 'POST'])]
    public function create_or_edit(Request $request, ?Product $product): Response
    {

        $current_instance = $this->globalVariableService->get('current_instance');

        if($this->getUser() == null) {
            return $this->redirectToRoute('app_login', ['instance' => $current_instance->getName() ]);
        }

        if($product == null) {
            $product = new Product();
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            foreach ($form->get('image')->getData() as $imageForm) {

                $uploadedFile = $imageForm->getPathName();

                $extension = image_type_to_extension(
                    getimagesize($uploadedFile)[2]
                );

                if ($uploadedFile) {
                    $newFilename = uniqid() . $extension;

                    rename(
                        $uploadedFile,
                        $this->getParameter('images_directory') . '/' . $newFilename
                    );

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

    #[Route('delete/{product}', name: 'delete', methods: ['GET'])]
    public function delete(Product $product): Response
    {

        $current_instance = $this->globalVariableService->get('current_instance');

        if($this->getUser() == null) {
            return $this->redirectToRoute('app_login', ['instance' => $current_instance->getName() ]);
        }

        if($product->getRentBy() != $this->getUser()) {
            return $this->redirectToRoute('app_instance', ['instance' => $current_instance->getName()]);
        }

        foreach ($product->getImages() as $image) {
            if(file_exists($this->getParameter('images_directory') . '/' . $image->getPath())) {
                unlink($this->getParameter('images_directory') . '/' . $image->getPath());
            }
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_instance', ['instance' => $current_instance->getName()]);
    }
}
