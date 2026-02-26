<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'app:photo-clean-orphaned',
    description: 'Удаляет осиротевшие фотографии из файловой системы',
)]
final class PhotoCleanOrphanedCommand extends Command
{
    public function __construct(
        private readonly string $uploadDir,
        private readonly Filesystem $fileSystem,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $removedFiles = [];

        $query = $this->entityManager->createQuery('SELECT 1 FROM App\Entity\Photo p WHERE p.path = :path');
        /** @var \AppendIterator $files */
        $files = Finder::create()->files()->in($this->uploadDir)->notName('index.html')->getIterator();

        $countFiles = $this->entityManager->createQuery('SELECT COUNT(p) FROM App\Entity\Photo p')->execute([], AbstractQuery::HYDRATE_SINGLE_SCALAR);
        $progressBar = $io->createProgressBar($countFiles);
        $progressBar->start();

        /** @var \SplFileInfo $fileInfo */
        foreach ($files as $fileInfo) {
            $progressBar->advance();

            try {
                $path = '/upload/'.\str_replace('\\', '/', \basename($fileInfo->getPath())).'/'.$fileInfo->getFilename();
                $query->execute(['path' => $path], AbstractQuery::HYDRATE_SINGLE_SCALAR);
            } catch (NoResultException $e) {
                $this->fileSystem->remove($fileInfo->getPathname());
                $removedFiles[] = $path;
            }
        }
        $progressBar->finish();

        if ($removedFiles) {
            $io->success('БД и файловая система синхронизированы.');
            $io->text('Были удалены следующие файлы:');
            $io->listing($removedFiles);
        } else {
            $io->success('БД и файловая система синхронизированы, удалять нечего.');
        }

        return self::SUCCESS;
    }
}
