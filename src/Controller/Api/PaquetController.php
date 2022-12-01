<?php

namespace App\Controller\Api;

use App\Controller\Base\ApiController;
use App\Entity\Paquet;
use App\Service\Ebisu;
use App\Service\Uploader;
use DateInterval;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Ulid;

#[Route('/paquet', name: 'paquet_')]
class PaquetController extends ApiController {

    protected string $entity = Paquet::class;

    /**
     * Receive the uploaded item from musubi client, and returns the access code
     * @param Request $request
     * @param Ebisu $ebisu
     * @param Uploader $uploader
     * @return Response
     * @throws Exception
     */
    #[Route('/upload', name: 'upload', methods: ['GET', 'POST'])]
    public function upload(Request $request, Ebisu $ebisu, Uploader $uploader): Response {
        $em = $this->doctrine->getManager();
        $file = $request->files->get('data');

        $paquet = new Paquet();
        $paquet->setName($request->get('name'));
        $paquet->setSlug(new Ulid());
        $paquet->setCode(substr($paquet->getSlug(), -7));

        $now = new DateTimeImmutable('now');
        $lifetime = new DateInterval('P3D'); //By default, expires after 3 days

        $paquet->setCreated($now);
        $paquet->setExpiration($now->add($lifetime));

        if ($this->getParameter('use_space')) {
            $ebisu->upload($file, $paquet->getSlug());
        } else {
            $filename = $uploader->upload($file);
            $paquet->setSlug($filename);
        }

        $em->persist($paquet);
        $em->flush();

        return new Response($paquet->getCode());
    }

    /**
     * @param string $code
     * @param Ebisu $ebisu
     * @return BinaryFileResponse
     */
    #[Route('/retrieve/{code}', name: 'retrieve', methods: ['GET'])]
    public function retrieve(string $code, Ebisu $ebisu): BinaryFileResponse {
        $repository = $this->doctrine->getRepository(Paquet::class);
        $paquet = $repository->findOneByCode($code);
        $key = $paquet->getSlug();

        if ($this->getParameter('use_space')) {
            $uri = $ebisu->createUrl($key);
            return new BinaryFileResponse($uri);

        } else {
            $localFilepath = Path::join($this->getParameter('upload_directory'), $paquet->getSlug());
            $response = new BinaryFileResponse($localFilepath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'musubi_' . $paquet->getName()
            );
            return $response;
        }

        try {
            $this->client->deleteObject([
                'Bucket' => $this->getParameter('app.bucket.name'),
                'Key' => $resource->getFilename(),
            ]);

            $em->remove($resource);
            $em->flush();
        } catch (Exception $e) {
            var_dump($e);
        }

        $json = $serializer->serialize('ok', 'json');
        return new Response($json, Response::HTTP_OK);
    }
}
