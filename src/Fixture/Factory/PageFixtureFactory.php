<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\CmsPlugin\Fixture\Factory;

use BitBag\CmsPlugin\Entity\PageInterface;
use BitBag\CmsPlugin\Entity\PageTranslationInterface;
use BitBag\CmsPlugin\Entity\SectionInterface;
use BitBag\CmsPlugin\Repository\PageRepositoryInterface;
use BitBag\CmsPlugin\Repository\SectionRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class PageFixtureFactory implements FixtureFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $pageFactory;

    /**
     * @var FactoryInterface
     */
    private $pageTranslationFactory;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SectionRepositoryInterface
     */
    private $sectionRepository;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param FactoryInterface $pageFactory
     * @param FactoryInterface $pageTranslationFactory
     * @param PageRepositoryInterface $pageRepository
     * @param ProductRepositoryInterface $productRepository
     * @param SectionRepositoryInterface $sectionRepository
     * @param ChannelContextInterface $channelContext
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        FactoryInterface $pageFactory,
        FactoryInterface $pageTranslationFactory,
        PageRepositoryInterface $pageRepository,
        ProductRepositoryInterface $productRepository,
        SectionRepositoryInterface $sectionRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext
    )
    {
        $this->pageFactory = $pageFactory;
        $this->pageTranslationFactory = $pageTranslationFactory;
        $this->pageRepository = $pageRepository;
        $this->productRepository = $productRepository;
        $this->sectionRepository = $sectionRepository;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $data): void
    {
        foreach ($data as $code => $fields) {
            if (
                true === $fields['remove_existing'] &&
                null !== $page = $this->pageRepository->findOneBy(['code' => $code])
            ) {
                $this->pageRepository->remove($page);
            }

            if (null !== $fields['number']) {
                for ($i = 0; $i < $fields['number']; $i++) {
                    $this->createPage(md5(uniqid()), $fields,true);
                }
            } else {
                $this->createPage($code, $fields);
            }
        }
    }

    /**
     * @param string $code
     * @param array $pageData
     * @param bool $generateSlug
     */
    private function createPage(string $code, array $pageData, bool $generateSlug = false): void
    {
        /** @var PageInterface $page */
        $page = $this->pageFactory->createNew();
        $products = $pageData['products'];

        if (null !== $products) {
            $this->resolveProducts($page, $products);
        }

        $this->resolveSections($page, $pageData['sections']);

        $page->setCode($code);
        $page->setEnabled($pageData['enabled']);

        foreach ($pageData['translations'] as $localeCode => $translation) {
            /** @var PageTranslationInterface $pageTranslation */
            $pageTranslation = $this->pageTranslationFactory->createNew();
            $slug = true === $generateSlug ? md5(uniqid()) : $translation['slug'];

            $pageTranslation->setLocale($localeCode);
            $pageTranslation->setSlug($slug);
            $pageTranslation->setName($translation['name']);
            $pageTranslation->setMetaKeywords($translation['meta_keywords']);
            $pageTranslation->setMetaDescription($translation['meta_description']);
            $pageTranslation->setContent($translation['content']);

            $page->addTranslation($pageTranslation);
        }

        $this->pageRepository->add($page);
    }

    /**
     * @param int $limit
     * @param PageInterface $page
     */
    private function resolveProducts(PageInterface $page, int $limit): void
    {
        $products = $this->productRepository->findLatestByChannel(
            $this->channelContext->getChannel(),
            $this->localeContext->getLocaleCode(),
            $limit
        );

        foreach ($products as $product) {
            $page->addProduct($product);
        }
    }

    /**
     * @param PageInterface $page
     * @param array $sections
     */
    private function resolveSections(PageInterface $page, array $sections): void
    {
        foreach ($sections as $sectionCode) {
            /** @var SectionInterface $section */
            $section = $this->sectionRepository->findOneBy(['code' => $sectionCode]);

            $page->addSection($section);
        }
    }
}
