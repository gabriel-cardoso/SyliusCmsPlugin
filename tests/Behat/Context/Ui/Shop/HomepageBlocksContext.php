<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\CmsPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Tests\BitBag\CmsPlugin\Behat\Page\Shop\HomePageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class HomepageBlocksContext implements Context
{
    /**
     * @var HomePageInterface
     */
    private $blockHomePage;

    /**
     * @param HomePageInterface $blockHomePage
     */
    public function __construct(HomePageInterface $blockHomePage)
    {
        $this->blockHomePage = $blockHomePage;
    }

    /**
     * @When I go to the homepage
     */
    public function iGoToTheHomepage(): void
    {
        $this->blockHomePage->open();
    }

    /**
     * @Then I want to see an image block
     */
    public function iWantToSeeAnImageBlock(): void
    {
        Assert::true($this->blockHomePage->hasImageBlock());
    }

    /**
     * @Then I want to see a text block with :content content
     */
    public function iWantToSeeATextBlockWithContent(string $content): void
    {
        Assert::true($this->blockHomePage->hasBlockWithContent($content));
    }
}
