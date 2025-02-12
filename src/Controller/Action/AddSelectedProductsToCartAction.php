<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddSelectedProductsToCartAction extends BaseWishlistProductsAction
{
    public function __construct(
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        MessageBusInterface $messageBus,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        TranslatorInterface $translator,
    ) {
        parent::__construct(
            $cartContext,
            $formFactory,
            $requestStack,
            $wishlistCommandProcessor,
            $messageBus,
            $urlGenerator,
            $wishlistRepository,
            $translator,
        );
    }

    protected function handleCommand(FormInterface $form): void
    {
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        try {
            $command = new AddSelectedProductsToCart($form->getData());
            $this->messageBus->dispatch($command);
            $session->getFlashBag()->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
        } catch (HandlerFailedException $exception) {
            $session->getFlashBag()->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity'));
        }
    }
}
