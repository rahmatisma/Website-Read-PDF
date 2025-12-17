<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SPKDataSaved
{
    use Dispatchable, SerializesModels;

    public int $idSpk;
    public string $noJaringan;

    /**
     * Create a new event instance.
     *
     * @param int $idSpk
     * @param string $noJaringan
     */
    public function __construct(int $idSpk, string $noJaringan)
    {
        $this->idSpk = $idSpk;
        $this->noJaringan = $noJaringan;
    }
}