<?php
namespace kata\invoicereminder\domain;

final class DueDateWarningReminder
{
    /**
     * @var DueDateWarningEmailSender
     */
    private $sender;
    /**
     * @var BillRepository
     */
    private $billRepository;

    /**
     * DueDateWarningReminder constructor.
     *
     * @param DueDateWarningEmailSender $sender
     * @param BillRepository $billRepository
     */
    public function __construct(DueDateWarningEmailSender $sender, BillRepository $billRepository)
    {
        $this->sender = $sender;
        $this->billRepository = $billRepository;
    }

    public function sendReminders()
    {
        $bills = $this->billRepository->billsWithDueDateIn10Days();

        foreach ($bills as $bill) {
            $this->sender->sendReminderFor($bill);
        }

    }
}