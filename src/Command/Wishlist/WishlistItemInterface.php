<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;

interface WishlistItemInterface extends WishlistSyncCommandInterface
{
    public function getWishlistProduct(): ?WishlistProductInterface;

    public function setWishlistProduct(?WishlistProductInterface $wishlistProduct): void;

    public function isSelected(): ?bool;

    public function setSelected(?bool $selected): void;

    public function getCartItem(): ?AddToCartCommandInterface;

    public function setCartItem(?AddToCartCommandInterface $cartItem): void;
}
