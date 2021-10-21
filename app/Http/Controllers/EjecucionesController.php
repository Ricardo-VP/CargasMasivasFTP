<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\AutonomiJob;
use App\Jobs\MascostasBPJob;
use App\Jobs\PaligJob;
use App\Jobs\IntegralJob;
use App\Jobs\SagrarioJob;
use App\Jobs\SagrarioHJob;
use App\Jobs\LinkmaticJob;
use App\Jobs\AutoFenixJob;
use App\Jobs\EquinorteJob;
use App\Jobs\HyunmotorJob;
use App\Jobs\VallejoJob;

class EjecucionesController extends Controller
{
    //
    public function EjecutarAutonomi()
    {
        $j = new AutonomiJob();
        dispatch($j);
    }
    public function EjecutarMascotasBP()
    {
        $job = new MascostasBPJob();
        dispatch($job);
    }
    public function EjecutarPalig()
    {
        $job = new PaligJob();
        dispatch($job);
    }
    public function EjecutarIntegral()
    {
        $job = new IntegralJob();
        dispatch($job);
    }
    public function EjecutarSagrario()
    {
        $job = new SagrarioJob();
        dispatch($job);
    }
    public function EjecutarSagrarioHijos()
    {
        $job = new SagrarioHJob();
        dispatch($job);
    }
    public function EjecutarLinkmatic()
    {
        $job = new LinkmaticJob();
        dispatch($job);
    }
    public function EjecutarAutoFenix()
    {
        $job = new AutoFenixJob();
        dispatch($job);
    }
    public function EjecutarEquinorte()
    {
        $job = new EquinorteJob();
        dispatch($job);
    }
    public function EjecutarHyunmotor()
    {
        $job = new HyunmotorJob();
        dispatch($job);
    }
    public function EjecutarVallejoAraujo()
    {
        $job = new VallejoJob();
        dispatch($job);
    }
}
