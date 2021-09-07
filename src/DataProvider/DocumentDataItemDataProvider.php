<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Document;
use App\Entity\DocumentData;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use stdClass;

final class DocumentDataItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private DocumentRepository $documentRepository;

    /**
     * DocumentItemDataProvider constructor.
     *
     * @param EntityManagerInterface $manager
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $documentRepository = $manager->getRepository(Document::class);

        /* Check repository class. */
        if (!$documentRepository instanceof DocumentRepository) {
            throw new Exception(sprintf(
                'Expect class "%s" but got class "%s (%s:%d)"',
                'DocumentRepository',
                get_class($documentRepository),
                __FILE__,
                __LINE__
            ));
        }

        $this->documentRepository = $documentRepository;
    }

    /**
     * @param string $resourceClass
     * @param ?string $operationName
     * @param string[] $context
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return DocumentData::class === $resourceClass;
    }

    /**
     * @param string $resourceClass
     * @param int[]|int|object|string $id
     * @param ?string $operationName
     * @param string[] $context
     * @return ?stdClass
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?stdClass
    {
        /** @var DocumentData $document */
        $document = $this->documentRepository->find($id);

        // Create generic class
        $obj = new stdClass();
        foreach ($document->getData() as $key => $value) {
            $obj->{$key} = $value;
        }

        return $obj;
    }
}
