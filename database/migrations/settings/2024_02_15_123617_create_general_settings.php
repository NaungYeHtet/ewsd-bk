<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.closure_date', '');
        $this->migrator->add('general.final_closure_date', '');
    }
};
