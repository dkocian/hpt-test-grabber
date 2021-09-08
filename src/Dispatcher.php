<?php
declare(strict_types=1);

namespace HPT;

use Exception;

class Dispatcher
{
    /** @var GrabberInterface */
    private $grabber;

    /** @var OutputInterface */
    private $output;

    public function __construct(GrabberInterface $grabber, OutputInterface $output)
    {
        $this->grabber = $grabber;
        $this->output = $output;
    }

    /**
     * @param string $sourceFilePath
     * @return string JSON
     * @throws Exception
     */
    public function run(string $sourceFilePath): string
    {
        $productIds = $this->loadProductIdsFromFile($sourceFilePath);
        $products = [];
        foreach ($productIds as $productId) {
            $product = $this->grabber->findProduct($productId);
            if ($product !== null) {
                $products[$productId] = $product;
            }
        }

        return $this->output->getJson($products);
    }

    /**
     * @param string $sourceFilePath
     * @return string[]
     * @throws Exception
     */
    private function loadProductIdsFromFile(string $sourceFilePath): array
    {
        $handle = fopen($sourceFilePath, "rb");
        $productIds = [];

        if (!$handle) {
            throw new Exception(sprintf("Soubor '%s' nenalezen", $sourceFilePath));
        }

        while (($line = fgets($handle)) !== false) {
            $productId = trim($line);
            if (!empty($productId)) {
                $productIds[] = $productId;
            }
        }
        fclose($handle);
        return $productIds;
    }
}
