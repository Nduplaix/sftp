<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
        $files = [];
        $dir = scandir($this->getParameter('kernel.project_dir'). '/public/images/');
        foreach ($dir as $file)
        {
            if (!is_dir($file))
            {
                $files[] = $file;
            }
        }
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
        $response = new BinaryFileResponse($publicResourcesFolderPath);
        $response->headers->set('Content-Type', 'image/jpg');

        return $response;
    }
}
