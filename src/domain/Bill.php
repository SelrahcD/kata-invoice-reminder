<?php
namespace kata\invoicereminder\domain;

final class Bill
{
    private $amount;
    private $emailAddress;
    private $dueDate;
    private $paymentDate;


    /**
     * Bill constructor.
     *
     * @param $amount
     * @param $dueDate
     * @param $paymentDate
     * @param $emailAddress
     */
    public function __construct($amount, $dueDate, $paymentDate, $emailAddress)
    {
        $this->amount = $amount;
        $this->emailAddress = $emailAddress;
        $this->dueDate = $dueDate;
        $this->paymentDate = $paymentDate;}

    public function dueDateIsIn($testDueDate)
    {
        return $this->dueDate == $testDueDate;
    }

    public function dueDate()
    {
        return $this->dueDate;
    }

    public function hasBeenPaidOn(\DateTimeImmutable $date)
    {
        return $this->paymentDate === null || $this->paymentDate > $date;
    }
}