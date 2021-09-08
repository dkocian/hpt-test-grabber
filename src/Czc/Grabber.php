<?php
declare(strict_types=1);

namespace HPT\Czc;

use Exception;
use HPT\GrabberInterface;
use HPT\Helper\NumberHelper;
use Symfony\Component\DomCrawler\Crawler;

class Grabber implements GrabberInterface
{
    private const SEARCH_PRODUCT_ID = "{PRODUCT_ID}";

    /** @var array */
    private $config;

    /** @var NumberHelper */
    private $numberHelper;

    public function __construct(array $config, NumberHelper $numberHelper)
    {
        $this->config = $config;
        $this->numberHelper = $numberHelper;
    }

    /**
     * Vyhleda mozne produkty podle ID. Pro kazdy nalezeny vysledek overi v detailu produktu, ze
     * se opravdu jedna o pozadovany produkt. Pokud je nalezen, tak se vraci
     * @param string $productId
     * @return Product|null
     * @throws Exception
     */
    public function findProduct(string $productId): ?Product
    {
        $productUrls = $this->findProductUrls($productId);
        foreach ($productUrls as $productUrl) {
            $productDetailUrl = $this->getBaseUrl() . $productUrl;
            $content = file_get_contents($productDetailUrl);

            if (!$content) {
                throw new Exception(sprintf(
                    "Nebyl nacten detail produktu na URL '%s'",
                    $productDetailUrl
                ));
            }

            $crawler = new Crawler($content);
            $productNumber = $this->grabProductNumber($crawler);
            // kontrola, zda kod produktu odpovida pozadovanemu kodu
            if ($productNumber !== $productId) {
                continue;
            }

            return (new Product())
                ->setPrice($this->grabPrice($crawler));
        }
        return null;
    }

    /**
     * Podle id produktu vyhleda odpovidajici produkty a vraci jejich url
     * @param string $productId
     * @return string[]
     * @throws Exception
     */
    private function findProductUrls(string $productId): array
    {
        $content = file_get_contents($this->getSearchUrl($productId));

        if (!$content) {
            throw new Exception(sprintf(
                "Nebyla nactena stranka s vysledky vyhledavani na URL '%s'",
                $this->getSearchUrl($productId)
            ));
        }

        $crawler = new Crawler($content);
        $productLinks = $crawler->filter($this->getDetailLinkFilter());
        if ($productLinks->count() === 0) {
            return [];
        }
        return $productLinks->extract(["href"]);
    }

    /**
     * @param string $productId
     * @return string
     */
    public function getSearchUrl(string $productId): string
    {
        return $this->getBaseUrl() . str_replace(
                self::SEARCH_PRODUCT_ID,
                $productId,
                $this->getSearchUrlPart()
            );
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->config["base_url"];
    }

    /**
     * @return string
     */
    public function getSearchUrlPart(): string
    {
        return $this->config["search_url_part"];
    }

    /**
     * @return string
     */
    public function getDetailLinkFilter(): string
    {
        return $this->config["filter"]["detail_link"];
    }

    /**
     * @param Crawler $crawler
     * @return string|null
     */
    private function grabProductNumber(Crawler $crawler): ?string
    {
        $node = $crawler->filter($this->getProductNumberFilter());
        if ($node->count() === 0) {
            return null;
        }
        return $node->text();
    }

    /**
     * @return string
     */
    public function getProductNumberFilter(): string
    {
        return $this->config["filter"]["product_number"];
    }

    /**
     * @param Crawler $crawler
     * @return float|null
     */
    private function grabPrice(Crawler $crawler): ?float
    {
        $node = $crawler->filter($this->getPriceFilter());
        if ($node->count() === 0) {
            return null;
        }
        return $this->numberHelper->parseFloat($node->text());
    }

    /**
     * @return string
     */
    public function getPriceFilter(): string
    {
        return $this->config["filter"]["product_price"];
    }
}