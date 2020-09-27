<?php
declare(strict_types=1);

namespace App\Console;

use App\Entity\Category;
use App\Entity\Product;
use App\Event\CategoryCreatedInvalidateListEvent;
use App\Event\ProductCreatedEvent;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateNewProductCommand extends Command
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('product:create')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('categories', InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = strval($input->getArgument('name'));
        $categoryNames = $input->getArgument('categories');
        $events = [];
        $newCategoryCreated = false;

        if ($this->productRepository->findByName($name)) {
            throw new \LogicException(sprintf('Product with name %s already exists', $name));
        }

        $categories = [];
        // Fetch or create new categories entities and events
        foreach ($categoryNames as $categoryName) {
            $category = $this->categoryRepository->findOneByName($categoryName);
            if (!$category instanceof Category) {
                $category = (new Category())->setName($categoryName);
                $newCategoryCreated = true;
            }

            $categories[] = $category;
        }

        $product = (new Product())
            ->setName($name)
            ->setCategories(new ArrayCollection($categories));

        $this->productRepository->save($product);
        $events[] = new ProductCreatedEvent($product->getId());
        if ($newCategoryCreated) {
            $events[] = new CategoryCreatedInvalidateListEvent();
        }

        $this->dispatchEvents($events);
    }

    private function dispatchEvents(array $events): void
    {
        foreach ($events as $event) {
            if (!$event instanceof Event) {
                throw new \LogicException("Invalid resource supplied for event dispatched");
            }

            $this->eventDispatcher->dispatch($event);
        }
    }
}