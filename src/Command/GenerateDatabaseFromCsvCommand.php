<?php

namespace App\Command;

use PDO;
use App\Service\Connection;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class GenerateDatabaseFromCsvCommand extends Command
{
    protected static $defaultName = 'app:update-db';

    private PDO $pdo;
    
    private TransactionRepository $transactionRepository;
    
    private UserRepository $userRepository;
    
    private string $dataDirectory;

    public function __construct(UserRepository $userRepository, TransactionRepository $transactionRepository, Connection $connection, string $dataDirectory)
    {
        parent::__construct();
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        $this->dataDirectory = $dataDirectory;
        $this->pdo = $connection->getConnection();
    }

    protected function configure(): void
    {
        $this->setDescription('Update database from a csv file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateDatabase();

        $io = new SymfonyStyle($input, $output);
        $io->success('Database is updated !');

        return Command::SUCCESS;
    }

    private function updateDatabase(): void
    {
        // $this->clearDb();
        // $this->setMaxAllowedPacket();
        $this->transactionRepository->createTable();
        $this->userRepository->createTable();
        $this->getDataFromCsv();
    }

    private function getDataFromCsv(): void {
        $file = $this->dataDirectory . 'contact.csv';
        $fileString = file_get_contents($file);

        $encoders = [new CsvEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $data = $serializer->decode($fileString, 'csv', [CsvEncoder::DELIMITER_KEY => ';']);

        $formatedDataTransaction = [];
        $formatedDataUser = [];
        foreach ($data as $row) {
            $formatedRowTransaction = $this->formatRowForTransaction($row);
            array_push($formatedDataTransaction, $formatedRowTransaction);

            $formatedRow = $this->formatRowForUser($row);
            array_push($formatedDataUser, $formatedRow);
        }
        $this->transactionRepository->multipleInsert($formatedDataTransaction);
        $this->userRepository->multipleInsert($formatedDataUser);


        /* Cette première tentative était trop lente (environ 1h)
            $this->transactionRepository->insert([
                'phone' => $row['Tel'],
                'amount' => ($row['Montant'])
            ]);
            $this->userRepository->insert([
                'phone' => $row['Tel'],
                'date' => $row['Date'],
                'postal_code' => $row['Code postal']
            ]);
        */
    }

    /* Format a row for sql query */
    private function formatRowForTransaction($row): string {
        return '(' . $row['Tel'] . ','  . $row['Montant'] . ')';
    }

     /* Format a row for sql query */
     private function formatRowForUser($row): string {
        return '(' . $row['Tel'] . ','  . $row['Code postal'] . ",'"  . $row['Date'] . "')";
    }

    private function setMaxAllowedPacket() {
        $this->pdo->query('SET @@global.max_allowed_packet = ' . 500 * 1024 * 1024 );
    }

    private function clearDb() {
        $this->pdo->exec("DROP DATABASE highco");
    }
}
