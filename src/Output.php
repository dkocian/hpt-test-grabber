<?php
declare(strict_types=1);

namespace HPT;

use Symfony\Component\Serializer\SerializerInterface;

class Output implements OutputInterface
{
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getJson(array $data): string
    {
        return $this->serializer->serialize($data, "json");
    }
}