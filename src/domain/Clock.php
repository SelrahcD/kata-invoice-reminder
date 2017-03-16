<?php
namespace kata\invoicereminder\domain;

class Clock
{
    public function today()
    {
        return new \DateTimeImmutable();
    }
}