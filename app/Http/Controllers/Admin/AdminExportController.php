<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\CsvExporter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminExportController extends Controller
{
    public function users(CsvExporter $csv): StreamedResponse
    {
        return $csv->download('users.csv', [
            'ID', 'Ism', 'Email', 'Telefon', 'Rol', 'GreenCoin', 'Ro‘yxatdan o‘tgan',
        ], User::query()->lazy()->map(fn (User $u) => [
            $u->id, $u->name, $u->email, $u->phone, $u->role->value,
            $u->greencoin_balance, $u->created_at->format('d.m.Y H:i'),
        ]));
    }

    public function applications(CsvExporter $csv): StreamedResponse
    {
        return $csv->download('applications.csv', [
            'ID', 'Ish', 'Kompaniya', 'Nomzod', 'Email', 'Holat', 'Yuborilgan',
        ], JobApplication::with(['job', 'user'])->lazy()->map(fn (JobApplication $a) => [
            $a->id, $a->job?->title, $a->job?->company,
            $a->user?->name, $a->user?->email, $a->status->value,
            $a->created_at->format('d.m.Y H:i'),
        ]));
    }
}
