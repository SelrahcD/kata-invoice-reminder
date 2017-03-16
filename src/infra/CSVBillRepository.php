<?php
namespace kata\invoicereminder\infra;

use DateTimeImmutable;
use kata\invoicereminder\domain\Bill;
use kata\invoicereminder\domain\BillRepository;
use kata\invoicereminder\domain\Clock;
use League\Csv\Reader;

final class CSVBillRepository implements BillRepository
{
    /**
     * @var Clock
     */
    private $clock;

    /**
     * CSVBillRepository constructor.
     *
     * @param Clock $clock
     * @param string $csvPath
     */
    public function __construct(Clock $clock, $csvPath)
    {
        $csv = Reader::createFromPath($csvPath);

        $this->bills = array_map(function($lineData) {
            return new Bill((int)$lineData[1], $this->decodeDate($lineData[2]), $this->decodeDate($lineData[3]), trim($lineData[6]));
        }, $csv->setOffset(1)->fetchAll());
        $this->clock = $clock;
    }

    public function billsWithDueDateIn10Days()
    {
        return array_filter($this->bills, function(Bill $bill) {
           return $bill->dueDateIsIn($this->clock->today()->add(new \DateInterval('P10D')));
        });
    }

    private function decodeDate($a)
    {
        if($a === '') {
            return null;
        }

        list($day, $month, $year) = sscanf($a, "D%dM%dY%d");

        return new DateTimeImmutable(sprintf('%s-%s-%s', $year, $month, $day));
    }

    public function overduedBillsThatShouldReceiveAReminder()
    {
        return array_filter($this->bills, function(Bill $bill) {
            return $bill->dueDate()->format('d') === $this->clock->today()->format('d') &&
            $bill->hasBeenPaidOn($this->clock->today());
        });
    }
}