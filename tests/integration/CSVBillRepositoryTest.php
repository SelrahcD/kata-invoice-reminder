<?php

use kata\invoicereminder\domain\Bill;
use kata\invoicereminder\domain\Clock;
use kata\invoicereminder\infra\CSVBillRepository;
use PHPUnit\Framework\TestCase;

class CSVBillRepositoryTest extends TestCase
{
    private $csvPath = __DIR__ . '/invoices.csv';

    private $repository;

    private $clock;

    protected function setUp()
    {
        $this->clock = $this->prophesize(Clock::class);
        $this->repository = new CSVBillRepository($this->clock->reveal(), $this->csvPath);
    }

    /**
     * @test
     * @dataProvider billsIn10Days
     * @param DateTimeImmutable $today
     * @param Bill[] $expectedBills
     */
    public function returns_bills_with_due_date_in_10_days($today, $expectedBills)
    {
        $this->clock->today()->willReturn($today);
        foreach($expectedBills as $expectedBill) {
            $this->assertContains($expectedBill, $this->repository->billsWithDueDateIn10Days(), '', false, false);
        }
    }

    /**
     * @test
     */
    public function doesnt_return_a_bill_with_a_due_date_not_in_10_days()
    {
        $this->clock->today()->willReturn(new DateTimeImmutable('2017-02-02'));

        $this->assertNotContains(new Bill(6540, new DateTimeImmutable('2017-02-12'), null, 'mary.ann@foobar.com'), $this->repository->billsWithDueDateIn10Days(), null, null, false, true);
    }


    public function billsin10Days()
    {
        return [
            [
                new DateTimeImmutable('2017-02-02'),
                [
                new Bill(2300, new DateTimeImmutable('2017-02-12'), null, 'john.doe@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2017-03-13'),
                [
                    new Bill(6540, new DateTimeImmutable('2017-03-23'), null, 'mary.ann@foobar.com')
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider overduedBillsWithReminderDate
     * @param DateTimeImmutable $today
     * @param $expectedBills
     */
    public function returns_bills_that_should_receive_a_reminder_on_date(DateTimeImmutable $today, array $expectedBills)
    {
        $this->clock->today()->willReturn($today);
        foreach($expectedBills as $expectedBill) {
            $this->assertContains($expectedBill, $this->repository->overduedBillsThatShouldReceiveAReminder(), '', false, false);
        }
    }

    /**
     * @test
     */
    public function doesnt_return_a_bill_for_a_reminder_when_its_payed()
    {
        $this->clock->today()->willReturn(new DateTimeImmutable('2017-08-22'));
        $this->assertNotContains(new Bill(540080,
            new DateTimeImmutable('2016-09-22'),
            null,
            'marius.back.pro@foobar.com'), $this->repository->overduedBillsThatShouldReceiveAReminder(), null, null, false, true);

    }

    public function overduedBillsWithReminderDate()
    {

        return [
            [
                new DateTimeImmutable('2017-02-12'),
                [
                    new Bill(2300, new DateTimeImmutable('2017-02-12'), null, 'john.doe@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2017-03-23'),
                [
                    new Bill(6540, new DateTimeImmutable('2017-03-23'), null, 'mary.ann@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2017-03-12'),
                [
                    new Bill(2300, new DateTimeImmutable('2017-02-12'), null, 'john.doe@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2017-04-23'),
                [
                    new Bill(6540, new DateTimeImmutable('2017-03-23'), null, 'mary.ann@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2017-04-12'),
                [
                    new Bill(2300, new DateTimeImmutable('2017-02-12'), null, 'john.doe@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2017-05-23'),
                [
                    new Bill(6540, new DateTimeImmutable('2017-03-23'), null, 'mary.ann@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2017-09-12'),
                [
                    new Bill(13600, new DateTimeImmutable('2017-08-12'), null, 'harry.cover@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2018-01-12'),
                [
                    new Bill(13600, new DateTimeImmutable('2017-08-12'), null, 'harry.cover@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2016-09-22'),
                [
                    new Bill(540080, new DateTimeImmutable('2016-09-22'), new DateTimeImmutable('2017-08-12'), 'marius.back.pro@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2016-10-22'),
                [
                    new Bill(540080, new DateTimeImmutable('2016-09-22'), new DateTimeImmutable('2017-08-12'), 'marius.back.pro@foobar.com')
                ]
            ],
            [
                new DateTimeImmutable('2017-07-22'),
                [
                    new Bill(540080, new DateTimeImmutable('2016-09-22'), new DateTimeImmutable('2017-08-12'), 'marius.back.pro@foobar.com')
                ]
            ]
        ];
    }
}
