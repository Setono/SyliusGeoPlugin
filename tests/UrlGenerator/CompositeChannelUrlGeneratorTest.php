<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGeoPlugin\UrlGenerator;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusGeoPlugin\UrlGenerator\AbstractChannelUrlGenerator;
use Setono\SyliusGeoPlugin\UrlGenerator\CompositeChannelUrlGenerator;
use Setono\SyliusGeoPlugin\UrlGenerator\SlugAwareChannelUrlGenerator;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

final class CompositeChannelUrlGeneratorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_generates_product_url(): void
    {
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate('sylius_shop_product_show', [
            'slug' => 'blue-jeans',
            '_locale' => 'en_US',
        ])->willReturn('https://example.com/products/blue-jeans');
        $urlGenerator->getContext()->willReturn(new RequestContext());
        $urlGenerator->setContext(Argument::type(RequestContext::class))->shouldBeCalledTimes(2);

        $channel = new Channel();
        $channel->setHostname('example.com');

        $request = new Request();
        $request->setLocale('da_DK');
        $request->attributes->set(AbstractChannelUrlGenerator::ATTRIBUTE_ROUTE, 'sylius_shop_product_show');
        $request->attributes->set(AbstractChannelUrlGenerator::ATTRIBUTE_ROUTE_PARAMETERS, [
            'slug' => 'blaa-bukser',
        ]);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $product = new Product();

        $danishProductTranslation = new ProductTranslation();
        $danishProductTranslation->setTranslatable($product);

        $productRepository = $this->prophesize(ProductRepositoryInterface::class);
        $productRepository->findOneBy([
            'locale' => 'da_DK',
            'slug' => 'blaa-bukser',
        ])->willReturn($danishProductTranslation);

        $englishProductTranslation = new ProductTranslation();
        $englishProductTranslation->setSlug('blue-jeans');

        $productRepository->findOneBy([
            'translatable' => $product,
            'locale' => 'en_US',
        ])->willReturn($englishProductTranslation);

        $channelUrlGenerator = new CompositeChannelUrlGenerator($urlGenerator->reveal());
        $channelUrlGenerator->add(new SlugAwareChannelUrlGenerator($urlGenerator->reveal(), $requestStack, $productRepository->reveal()));

        self::assertSame('https://example.com/products/blue-jeans', $channelUrlGenerator->generate($channel, 'en_US', $request));
    }
}
