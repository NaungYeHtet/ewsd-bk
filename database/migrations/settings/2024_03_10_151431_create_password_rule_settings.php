<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('password_rule.min', 8);
        $this->migrator->add('password_rule.max', 32);
        $this->migrator->add('password_rule.letters', true);
        $this->migrator->add('password_rule.mixed_case', false);
        $this->migrator->add('password_rule.numbers', true);
        $this->migrator->add('password_rule.symbols', false);
    }
};
