<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('po_key')->nullable()->after('title');
        });

        $defaultPoKey = 'cahaya-bone';

        if (Schema::hasTable('settings')) {
            $rawPoNames = DB::table('settings')->where('key', 'gallery_po_names')->value('value');

            if (is_string($rawPoNames) && trim($rawPoNames) !== '') {
                $decoded = json_decode($rawPoNames, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    foreach ($decoded as $poName) {
                        $candidateKey = strtolower(trim((string) ($poName['key'] ?? '')));
                        $candidateKey = str_replace('_', '-', $candidateKey);
                        $candidateKey = preg_replace('/[^a-z0-9-]+/', '-', $candidateKey) ?? '';
                        $candidateKey = preg_replace('/-+/', '-', $candidateKey) ?? '';
                        $candidateKey = trim($candidateKey, '-');

                        if ($candidateKey !== '') {
                            $defaultPoKey = $candidateKey;
                            break;
                        }
                    }
                }
            }
        }

        DB::table('galleries')
            ->whereNull('po_key')
            ->update(['po_key' => $defaultPoKey]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('po_key');
        });
    }
};
