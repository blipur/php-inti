<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Imadepurnamayasa\PhpInti\Entity\Accounting\Account;
use Imadepurnamayasa\PhpInti\Entity\Accounting\Journal;
use Imadepurnamayasa\PhpInti\Entity\Accounting\JournalItem;
use Imadepurnamayasa\PhpInti\Entity\Accounting\ReportGroup;
use Imadepurnamayasa\PhpInti\Entity\Accounting\TransactionType;

// 1. Create a Report Group (e.g., Current Assets for Balance Sheet)
$currentAssetsGroup = new ReportGroup();
$currentAssetsGroup->setId(1);
$currentAssetsGroup->setCode('1.1');
$currentAssetsGroup->setName('Current Assets');
$currentAssetsGroup->setReportType(ReportGroup::TYPE_BALANCE_SHEET);
$currentAssetsGroup->setSortOrder(10);

// 2. Create an Account (e.g., Cash in Bank)
$cashAccount = new Account();
$cashAccount->setId(101);
$cashAccount->setCode('1110');
$cashAccount->setName('Cash in Bank');
$cashAccount->setType(Account::TYPE_ASSET);
$cashAccount->setReportGroupId($currentAssetsGroup->getId());

// Create another account (e.g., Accounts Receivable)
$arAccount = new Account();
$arAccount->setId(102);
$arAccount->setCode('1120');
$arAccount->setName('Accounts Receivable');
$arAccount->setType(Account::TYPE_ASSET);
$arAccount->setReportGroupId($currentAssetsGroup->getId());

// 3. Define a Transaction Type
$paymentType = new TransactionType();
$paymentType->setId(1);
$paymentType->setCode(TransactionType::TYPE_RECEIPT);
$paymentType->setName('Customer Payment Receipt');
$paymentType->setDescription('Payment received from customer invoices.');

// 4. Create a Journal Header
$journal = new Journal();
$journal->setId(1001);
$journal->setTransactionDate(new DateTime());
$journal->setReferenceNumber('RCPT-2023-001');
$journal->setDescription('Received payment from Customer A');
$journal->setTotalAmount(5000.00);
$journal->setStatus(Journal::STATUS_POSTED);

// 5. Create Journal Items (Debit & Credit)
$debitItem = new JournalItem();
$debitItem->setId(1);
$debitItem->setJournalId($journal->getId());
$debitItem->setAccountId($cashAccount->getId());
$debitItem->setDebit(5000.00);
$debitItem->setCredit(0.00);
$debitItem->setDescription('Cash received');

$creditItem = new JournalItem();
$creditItem->setId(2);
$creditItem->setJournalId($journal->getId());
$creditItem->setAccountId($arAccount->getId());
$creditItem->setDebit(0.00);
$creditItem->setCredit(5000.00);
$creditItem->setDescription('Decrease in AR');

// Outputting the demonstration
echo "--- ACCOUNTING SYSTEM EXAMPLE ---\n\n";

echo "Report Group: {$currentAssetsGroup->getName()} (Type: {$currentAssetsGroup->getReportType()})\n";
echo "Account: [{$cashAccount->getCode()}] {$cashAccount->getName()} -> Group ID: {$cashAccount->getReportGroupId()}\n";
echo "Account: [{$arAccount->getCode()}] {$arAccount->getName()} -> Group ID: {$arAccount->getReportGroupId()}\n\n";

echo "Transaction Type: {$paymentType->getName()}\n\n";

echo "Journal Entry: {$journal->getReferenceNumber()} | Status: {$journal->getStatus()}\n";
echo "Date: {$journal->getTransactionDate()->format('Y-m-d H:i:s')}\n";
echo "Description: {$journal->getDescription()}\n";
echo "Total Amount: {$journal->getTotalAmount()}\n\n";

echo "Journal Lines:\n";
echo "1. Account ID {$debitItem->getAccountId()} | Debit: {$debitItem->getDebit()} | Credit: {$debitItem->getCredit()} | {$debitItem->getDescription()}\n";
echo "2. Account ID {$creditItem->getAccountId()} | Debit: {$creditItem->getDebit()} | Credit: {$creditItem->getCredit()} | {$creditItem->getDescription()}\n";

echo "\n--- END OF EXAMPLE ---\n";
