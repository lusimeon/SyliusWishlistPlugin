<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop\ProductIndexPageInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop\WishlistPageInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Service\LoginerInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Service\WishlistCreatorInterface;
use Webmozart\Assert\Assert;

final class WishlistContext implements Context
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductIndexPageInterface */
    private $productIndexPage;

    /** @var WishlistPageInterface */
    private $wishlistPage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var LoginerInterface */
    private $loginer;

    /** @var WishlistCreatorInterface */
    private $wishlistCreator;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductIndexPageInterface $productIndexPage,
        WishlistPageInterface $wishlistPage,
        NotificationCheckerInterface $notificationChecker,
        LoginerInterface $loginer,
        WishlistCreatorInterface $wishlistCreator
    )
    {
        $this->productRepository = $productRepository;
        $this->productIndexPage = $productIndexPage;
        $this->wishlistPage = $wishlistPage;
        $this->notificationChecker = $notificationChecker;
        $this->loginer = $loginer;
        $this->wishlistCreator = $wishlistCreator;
    }

    /**
     * @When I add this product to wishlist
     */
    public function iAddThisProductToWishlist(): void
    {
        $this->productIndexPage->open(['slug' => 'main']);

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy([]);

        $this->productIndexPage->addProductToWishlist($product->getName());
    }

    /**
     * @When I add :productName product to my wishlist
     */
    public function iAddProductToMyWishlist(string $productName): void
    {
        $this->productIndexPage->addProductToWishlist($productName);
    }

    /**
     * @When I log in to my account which already has :productName product in the wishlist
     */
    public function iLogInToMyAccountWhichAlreadyHasProductInTheWishlist(string $productName): void
    {
        $user = $this->loginer->logInAndGetUser();

        $this->wishlistCreator->createWishlistWithProductAndUser($user, $productName);
    }

    /**
     * @When I log in
     */
    public function iLogIn(): void
    {
        $this->loginer->logIn();
    }

    /**
     * @When I log out
     */
    public function iLogOut(): void
    {
        $this->loginer->logOut();
    }

    /**
     * @When I go to the wishlist page
     */
    public function iGoToTheWishlistPage(): void
    {
        $this->wishlistPage->open();
    }

    /**
     * @When I select :quantity quantity of :productName product
     */
    public function iSelectQuantityOfProduct(int $quantity, string $productName): void
    {
        $this->wishlistPage->selectProductQuantity($productName, $quantity);
    }

    /**
     * @When I add my wishlist products to cart
     */
    public function iAddMyWishlistProductsToCart(): void
    {
        /** @var ProductInterface $product */
        foreach ($this->productRepository->findAll() as $product) {
            $this->productIndexPage->addProductToWishlist($product->getName());
        }
    }

    /**
     * @When I remove this product
     */
    public function iRemoveThisProduct(): void
    {
        $this->wishlistPage->removeProduct($this->productRepository->findOneBy([])->getName());
    }

    /**
     * @Then I should be on my wishlist page
     */
    public function iShouldBeOnMyWishlistPage(): void
    {
        $this->wishlistPage->verify();
    }

    /**
     * @Then I should be notified that the product has been successfully added to my wishlist
     */
    public function iShouldBeNotifiedThatTheProductHasBeenSuccessfullyAddedToMyWishlist(): void
    {
        $this->notificationChecker->checkNotification('Product has been added to your wishlist.', NotificationType::success());
    }

    /**
     * @Then I should be notified that the product has been removed from my wishlist
     */
    public function iShouldBeNotifiedThatTheProductHasBeenRemovedFromMyWishlist(): void
    {
        $this->notificationChecker->checkNotification('Product has been removed from your wishlist.', NotificationType::success());
    }

    /**
     * @Then there should be one item in my wishlist
     */
    public function thereShouldBeOneItemInMyWishlist(): void
    {
        Assert::eq(1, $this->wishlistPage->getItemsCount());
    }

    /**
     * @Then I should have :count products in my wishlist
     */
    public function iShouldHaveProductsInMyWishlist(int $count): void
    {
        Assert::eq($count, $this->wishlistPage->getItemsCount());
    }

    /**
     * @Then I should have :productName product in my wishlist
     */
    public function iShouldHaveProductInMyWishlist(string $productName): void
    {
        Assert::true($this->wishlistPage->hasProduct($productName));
    }
}
