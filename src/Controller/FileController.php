<?php

namespace App\Controller;

use App\Entity\BeerFile;
use App\Entity\File;
use App\Form\FilesType;
use App\Service\FileUploader;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FileController extends AbstractController
{
    /**
     * @Route("/beer/{id}/files", name="beer_file_files_index", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function index(Request $request, BeerFile $beerFile, DataTableFactory $dataTableFactory){
        $this->beerFile = $beerFile;
        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('preview', TextColumn::class, [
            'label' => 'Aperçu',
            'data' => function($context){
                return $context->getName();
            },
            'render' => function($value){
                return sprintf("<embed src='/uploads/files/$value' width='200px'/>");
            }
        ])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('file_show', ['id' => $value]));
                // $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('file_edit', ['id' => $value]));
                return $show;
            }, 
            'label' => 'Actions'
        ])
        ->createAdapter(ORMAdapter::class, [
            'entity' => File::class,
            'query' => function(QueryBuilder $queryBuilder){
                return $queryBuilder
                ->select('f')
                ->from(File::class, 'f')
                ->leftJoin('f.beerFile', 'beerFile')
                ->where('beerFile = :value')
                ->setParameter('value', $this->beerFile);
            }
        ])->handleRequest($request);

        if($table->isCallback()){
            return $table->getResponse();
        }
        return $this->render('admin/file/index.html.twig', [
            'datatable' => $table
        ]);
    }
    /**
     * @Route("/file/{id}", name="file_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(File $file){
        return $this->render('admin/file/show.html.twig', [
            'file' => $file
        ]);
    }

    /**
     * @Route("/file/{id}/edit", name="file_edit", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, File $file, FileUploader $fileUploader){
        $form = $this->createForm(FilesType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldFile = $file->getName();
            $uploadedFile = $form->get('name')->getData();
            if ($uploadedFile instanceof UploadedFile) {
                $fileName = $fileUploader->upload($uploadedFile);
                $file->setName($fileName);
                $fileUploader->delete($oldFile);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('file_show', [
                'id' => $file->getId()
            ]);
        }

        return $this->render('admin/file/edit.html.twig', [
            'file' => $file,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="file_delete", methods={"DELETE"})
     */
    public function delete(Request $request, File $file): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('beer_file_index');
    }
}
