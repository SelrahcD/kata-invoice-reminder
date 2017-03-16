<?php
namespace kata\invoicereminder\domain;

interface BillRepository
{

    /**
     * @return Bill[]
     */
    public function billsWithDueDateIn10Days();

    public function overduedBillsThatShouldReceiveAReminder();

}