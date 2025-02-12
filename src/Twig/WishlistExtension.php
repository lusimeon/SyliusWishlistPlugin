<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Twig;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WishlistExtension extends AbstractExtension
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getWishlists', [$this, 'getWishlists']),
            new TwigFunction('findAllByShopUser', [$this, 'findAllByShopUser']),
            new TwigFunction('findAllByAnonymous', [$this, 'findAllByAnonymous']),
            new TwigFunction('findAllByShopUserAndToken', [$this, 'findAllByShopUserAndToken']),
            new TwigFunction('findAllByShopUserAndChannel', [$this, 'findAllByShopUserAndChannel']),
            new TwigFunction('findAllByAnonymousAndChannel', [$this, 'findAllByAnonymousAndChannel']),
        ];
    }

    public function getWishlists(): ?array
    {
        /** @var WishlistInterface[] $wishlists */
        $wishlists = $this->wishlistRepository->findAll();

        return $wishlists;
    }

    public function findAllByShopUser(UserInterface $user = null): ?array
    {
        if (!$user instanceof ShopUserInterface) {
            throw new UnsupportedUserException();
        }

        return $this->wishlistRepository->findAllByShopUser($user->getId());
    }

    public function findAllByShopUserAndToken(UserInterface $user = null): ?array
    {
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if (!$user instanceof ShopUserInterface) {
            throw new UnsupportedUserException();
        }

        return $this->wishlistRepository->findAllByShopUserAndToken($user->getId(), $wishlistCookieToken);
    }

    public function findAllByAnonymous(): ?array
    {
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        return $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
    }

    public function findAllByShopUserAndChannel(UserInterface $user = null, ChannelInterface $channel = null): ?array
    {
        if (!$user instanceof ShopUserInterface) {
            throw new UnsupportedUserException();
        }
        if (!$channel instanceof ChannelInterface) {
            throw new ChannelNotFoundException();
        }

        return $this->wishlistRepository->findAllByShopUser($user->getId());
    }

    public function findAllByAnonymousAndChannel(ChannelInterface $channel): ?array
    {
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        return $this->wishlistRepository->findAllByAnonymousAndChannel($wishlistCookieToken, $channel);
    }
}
