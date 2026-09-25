<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const KEYS = [
        'company_legal_name',
        'company_legal_form',
        'company_share_capital',
        'company_address',
        'company_tax_id',
        'company_director',
        'company_identifiers',
        'company_host',
    ];

    // Their Settings sections are gone, so nothing could edit or clear these rows any more.
    public function up(): void
    {
        DB::table('settings')->whereIn('key', self::KEYS)->delete();

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        // The deleted values are not recoverable.
    }
};
