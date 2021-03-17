<?php

namespace App\Controller;

use App\Entity\BeerFile;
use App\Entity\File;
use App\Form\BeerFileType;
use App\Form\UpdateBeerFileType;
use App\Repository\BeerFileRepository;
use App\Service\FileUploader;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/beer")
 */
class BeerFileController extends AbstractController
{
    /**
     * @Route("/", name="beer_file_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {

        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('name', TextColumn::class, [
            'label' => 'Nom'
        ])
        ->add('district', TextColumn::class, [
            'label' => 'Région'
        ])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $pictures = sprintf('<a href="%s" class="btn btn-primary">Les images</a>', $this->generateUrl('beer_file_files_index', ['id' => $value]));
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('beer_file_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('beer_file_edit', ['id' => $value]));
                return $pictures.$show.$edit;
            }, 
            'label' => 'Actions'
        ])
        ->createAdapter(ORMAdapter::class, [
            'entity' => BeerFile::class
        ])->handleRequest($request);

        if($table->isCallback()){
            return $table->getResponse();
        }

        return $this->render('admin/beer_file/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/new", name="beer_file_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $beerFile = new BeerFile();
        $form = $this->createForm(BeerFileType::class, $beerFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if($beerFile->getFiles()){
                foreach ($beerFile->getFiles() as $key => $value) {
                    $file = new File();
                    $filename = $fileUploader->upload($value);
                    $file->setName($filename);
                    unset($beerFile->getFiles()[$key]);
                    $beerFile->addFile($file);
                    $entityManager->persist($file);
                }
            }
            $entityManager->persist($beerFile);
            $entityManager->flush();

            return $this->redirectToRoute('beer_file_index');
        }

        return $this->render('admin/beer_file/new.html.twig', [
            'beer_file' => $beerFile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="beer_file_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(BeerFile $beerFile): Response
    {
        return $this->render('admin/beer_file/show.html.twig', [
            'beer_file' => $beerFile,
        ]);
    }

    private $beerFile;

    /**
     * @Route("/{id}/edit", name="beer_file_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, BeerFile $beerFile): Response
    {
        $form = $this->createForm(UpdateBeerFileType::class, $beerFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('beer_file_index');
        }

        return $this->render('admin/beer_file/edit.html.twig', [
            'beer_file' => $beerFile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="beer_file_delete", methods={"DELETE"})
     */
    public function delete(Request $request, BeerFile $beerFile): Response
    {
        if ($this->isCsrfTokenValid('delete'.$beerFile->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($beerFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('beer_file_index');
    }
}
