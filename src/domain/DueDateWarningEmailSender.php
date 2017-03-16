<?php
namespace kata\invoicereminder\domain;

interface DueDateWarningEmailSender
{
    public function sendReminderFor(Bill $bill);
}