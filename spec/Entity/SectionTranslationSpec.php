<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace spec\BitBag\CmsPlugin\Entity;

use BitBag\CmsPlugin\Entity\SectionTranslation;
use BitBag\CmsPlugin\Entity\SectionTranslationInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class SectionTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SectionTranslation::class);
    }

    function it_is_a_resource()
    {
        $this->shouldHaveType(ResourceInterface::class);
    }

    function it_implements_frequently_asked_question_translation_interface()
    {
        $this->shouldHaveType(SectionTranslationInterface::class);
        $this->shouldHaveType(TranslationInterface::class);
    }

    function it_allows_access_via_properties()
    {
        $this->setName('Blog');
        $this->getName()->shouldReturn('Blog');
    }
}
