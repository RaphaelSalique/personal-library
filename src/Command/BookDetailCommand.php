<?php
// License proprietary
namespace App\Command;

use App\Services\GetBookDetail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class BookDetailCommand
 */
class BookDetailCommand extends Command
{
    /**
     * @var GetBookDetail
     */
    private $getBookDetail;

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
     * @var string
     */
    protected static $defaultName = 'app:book-detail';

    /**
     * configuration
     */
    protected function configure()
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
        $io = new SymfonyStyle($input, $output);
        $isbn = trim($input->getArgument('isbn'));

        if ('' !== $isbn) {
            $io->note(sprintf('ISBN demandé: %s', $isbn));
            try {
                $this->getBookDetail->disableSaveData();
                $book = $this->getBookDetail->isbnToBook($isbn);
                $io->success("Livre trouvé ".$book->getTitle());
            } catch (\Exception $exception) {
                $io->warning($exception->getMessage());
                $io->warning($exception->getTraceAsString());
                $io->warning($exception->getFile().' '.$exception->getLine());
            }
        }

        return 0;
    }
}
