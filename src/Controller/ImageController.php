<?php


namespace App\Controller;


use App\Entity\Directory;
use App\Entity\Image;
use App\Form\DirectoryType;
use App\Form\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/img")
 * Class ImageController
 * @package App\Controller
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/home", name="image_home")
     */
    public function home()
    {
        return $this->render('img/home.html.twig');
    }

    public function menu()
    {
        $files = $this->getImages();
        return $this->render('img/menu.html.twig', ['files' => $files]);
    }

    /**
     * @Route("/data/{imgName}", name="img_download");
     * @param $imgName
     * @return BinaryFileResponse
     */
    public function download($imgName)
    {
        $publicResourcesFolderPath = $this->getParameter('kernel.project_dir'). '/public/images/'.$imgName;

        if (file_exists($publicResourcesFolderPath))
        {
            return $this->file($publicResourcesFolderPath);
        }

        throw $this->createNotFoundException('l\'image n\'existe pas');
    }

    /**
     * @Route("/create", name="image_create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addImage(Request $request)
    {
        $dir = new Directory();

        $image = new Image();

        $formImage = $this->createForm(ImageType::class, $image);

        $formImage->handleRequest($request);

        $form = $this->createForm(DirectoryType::class, $dir);

        $form->handleRequest($request);

        if ($formImage->isSubmitted() && $formImage->isValid())
        {
            $image = $formImage['name']->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
            }
            return $this->redirectToRoute('image_create');

        }

        return $this->render('form/image.html.twig', [
            'form' => $form->createView(),
            'formImage' => $formImage->createView()
            ]);
    }

    /**
     * @Route("/delete/{name}", name="image_delete")
     */
    public function delete($name)
    {
        $fileSysteme = new Filesystem();

        $fileSysteme->remove($this->getParameter('images_directory').'/'.$name);
        return $this->redirectToRoute('image_create');
    }

    protected function getImages()
    {
        $files = [];
        $dir = scandir($this->getParameter('kernel.project_dir'). '/public/images/');
        foreach ($dir as $file)
        {
            if (!is_dir($file))
            {
                $files[] = $file;
            }
        }
        return $files;
    }
}
