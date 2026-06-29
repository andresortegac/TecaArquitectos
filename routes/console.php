<?php

use App\Services\CierreCajaService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('caja:cerrar-diaria {--date= : Fecha a cerrar en formato YYYY-MM-DD}', function () {
    try {
        $fecha = $this->option('date')
            ? Carbon::parse($this->option('date'), 'America/Bogota')
            : now('America/Bogota');
    } catch (Throwable) {
        $this->error('La fecha debe tener un formato valido, por ejemplo: 2026-06-29.');

        return 1;
    }

    $result = app(CierreCajaService::class)->closeDaily(
        $fecha->copy()->startOfDay(),
        null,
        'Cierre automatico generado por el sistema.'
    );

    if (!$result['created']) {
        $this->warn($result['message']);

        return 0;
    }

    $this->info('Cierre diario automatico guardado para ' . $fecha->format('Y-m-d') . '.');

    return 0;
})->purpose('Genera el cierre diario automatico de caja');

Schedule::command('caja:cerrar-diaria')
    ->timezone('America/Bogota')
    ->dailyAt('19:00')
    ->withoutOverlapping();
