<?php
namespace App\Command;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed-categories', description: 'Ajoute les catégories par défaut')]
class SeedCategoriesCommand extends Command
{
    private const NAMES = ['Boulangerie', 'Pâtisserie', 'Plats cuisinés', 'Fruits et légumes',
                           'Produits laitiers', 'Boissons', 'Épicerie', 'Autre'];

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(Category::class);
        $added = 0;
        foreach (self::NAMES as $name) {
            if (!$repo->findOneBy(['name' => $name])) {
                $this->em->persist((new Category())->setName($name));
                $added++;
            }
        }
        $this->em->flush();
        $output->writeln("$added catégorie(s) ajoutée(s).");

        return Command::SUCCESS;
    }
} 