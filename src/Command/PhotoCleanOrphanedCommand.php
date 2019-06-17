<?php

namespace App\Command;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PhotoCleanOrphanedCommand extends Command
{
    protected static $defaultName = 'app:photo-clean-orphaned';

    private $entityManager;
    private $uploadDir;
    private $fileSystem;

    public function __construct(string $uploadDir, Filesystem $fileSystem, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->uploadDir = $uploadDir;
        $this->fileSystem = $fileSystem;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Удаляет осиротевшие фотографии из файловой системы')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $removedFiles = [];

        $query = $this->entityManager->createQuery('SELECT 1 FROM App\Entity\Photo p WHERE p.path = :path');
        /** @var \AppendIterator $files */
        $files = Finder::create()->files()->in($this->uploadDir)->notName('index.html')->getIterator();

        // считаем кол-во файлов в БД, для красивого прогрессбара
        $countFiles = $this->entityManager->createQuery('SELECT COUNT(p) FROM App\Entity\Photo p')->execute([], AbstractQuery::HYDRATE_SINGLE_SCALAR);
        $progressBar = $io->createProgressBar($countFiles);

        /** @var \SplFileInfo $fileInfo */
        foreach ($files as $fileInfo) {
            // поиск файла по path
            $path = '/upload/'.\str_replace('\\', '/', \basename($fileInfo->getPath())).'/'.$fileInfo->getFilename();

            $progressBar->advance();

            try {
                $query->execute(['path' => $path], AbstractQuery::HYDRATE_SINGLE_SCALAR);
            } catch (NoResultException $e) {
                // если файла нету в БД

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
    }
}
