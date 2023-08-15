<?php

// License proprietary
namespace App\Command;

use App\Services\GetBookDetail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:book-detail')]
class BookDetailCommand extends Command
{
    /**
     * @var GetBookDetail
     */
    private GetBookDetail $getBookDetail;

    /**
     * BookDetailCommand constructor.
     * @param GetBookDetail $getBookDetail
     */
    public function __construct(GetBookDetail $getBookDetail)
    {
        parent::__construct();
        $this->getBookDetail = $getBookDetail;
    }

    /**
     * configuration
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Renvoie les détails d\'un livre selon ISBN')
            ->addArgument('isbn', InputArgument::REQUIRED, 'ISBN')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $isbn = trim($input->getArgument('isbn'));

        if ('' !== $isbn) {
            $style->note(sprintf('ISBN demandé: %s', $isbn));
            try {
                $this->getBookDetail->disableSaveData();
                $book = $this->getBookDetail->isbnToBook($isbn);
                $style->success("Livre trouvé " . $book->getTitle());
            } catch (\Exception $exception) {
                $style->warning($exception->getMessage());
                $style->warning($exception->getTraceAsString());
                $style->warning($exception->getFile() . ' ' . $exception->getLine());
            }
        }

        return 0;
    }
}
