<?php
declare(strict_types=1);

use HPT\Czc\Grabber;
use HPT\Dispatcher;
use HPT\Helper\NumberHelper;
use HPT\Output;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

require_once __DIR__ . "/vendor/autoload.php";

(new SingleCommandApplication())
    ->setName("czc_grabber")
    ->addArgument(
        "source-file",
        InputArgument::REQUIRED,
        "Cesta k souboru s kody produktu"
    )
    ->setCode(static function (InputInterface $consoleInput, OutputInterface $consoleOutput) {
        $inputSourceFile = $consoleInput->getArgument("source-file");

        $config = [
            "base_url" => "https://www.czc.cz",
            "search_url_part" => "/{PRODUCT_ID}/hledat",
            "filter" => [
                "detail_link" => "#tiles .tile-link",
                "product_price" => "#product-price-and-delivery-section .total-price .price-vatin",
                "product_number" => ".pd-info .pd-next-in-category__item-value",
            ]
        ];
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $output = new Output($serializer);
        $grabber = new Grabber($config, new NumberHelper());
        $dispatcher = new Dispatcher($grabber, $output);

        $consoleOutput->writeln(
            $dispatcher->run($inputSourceFile)
        );
    })
    ->run();
