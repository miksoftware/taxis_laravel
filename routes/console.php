<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\VerificarSancionesVencidas;

// Verificar sanciones vencidas cada minuto
Schedule::job(new VerificarSancionesVencidas)->everyMinute();
