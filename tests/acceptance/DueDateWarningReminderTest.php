<?php

use kata\invoicereminder\domain\Bill;
use kata\invoicereminder\domain\BillRepository;
use kata\invoicereminder\domain\DueDateWarningEmailSender;
use kata\invoicereminder\domain\DueDateWarningReminder;
use PHPUnit\Framework\TestCase;

class DueDateWarningReminderTest extends TestCase
{

    /**
     * @test
     */
    public function a_reminder_is_sent_to_all_contacts_with_a_bill_having_a_due_date_in_less_than_10_days()
    {
        $bill1 = new Bill(null, null, null, null);
        $bill2 = new Bill(null, null, null, null);

        $dueDateWarningEmailSender = $this->prophesize(DueDateWarningEmailSender::class);
        $billRepository = $this->prophesize(BillRepository::class);
        
        $billRepository->billsWithDueDateIn10Days()->willReturn([$bill1, $bill2]);
        $dueDateWarningReminder = new DueDateWarningReminder($dueDateWarningEmailSender->reveal(), $billRepository->reveal());

        $dueDateWarningReminder->sendReminders();

        $dueDateWarningEmailSender->sendReminderFor($bill1)->shouldHaveBeenCalled();
        $dueDateWarningEmailSender->sendReminderFor($bill2)->shouldHaveBeenCalled();
    }
}
