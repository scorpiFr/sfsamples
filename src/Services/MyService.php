<?php
// src/Services/MyService.php
namespace App\Services;

class MyService
{
    private $waitingTime;

    public function __construct(int $waitingTime)
    {
        $this->waitingTime = $waitingTime;
    }

    public function doSomething()
    {
        // sleep($this->waitingTime);
        return ($this->waitingTime);
    }
}
