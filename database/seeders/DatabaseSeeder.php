<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Eén account met een paar berichten, zodat de applicatie te bekijken is
     * zonder eerst zelf iets te typen. De gegevens staan op de inlogpagina.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@bylore.test'],
            ['name' => 'Demo', 'password' => 'portfolio123'],
        );

        if ($user->posts()->exists()) {
            return;
        }

        $user->posts()->createMany([
            [
                'title' => 'Waarom een eigen berichtenapplicatie',
                'body' => "Elke blog geeft je een databased met wat je er niet wilt hebben: analytics, advertenties, een ander uiterlijk.\n\nDeze applicatie heeft alleen wat nodig is. Een account, een bericht, en de zekerheid dat niemand anders je berichten kan zien.",
            ],
            [
                'title' => 'Hoe eigenaarschap hier werkt',
                'body' => "App\\Policies\\PostPolicy bepaalt wie een bericht mag wijzigen of verwijderen. De regel is simpel: alleen de auteur.\n\nEen bericht van iemand anders geeft een 403, geen stille redirect. Zo weet je als gebruiker wat er aan de hand is.",
            ],
            [
                'title' => 'Wat hier getest wordt',
                'body' => "Negentien tests, met de nadruk op één vraag: kan een account bij andermans berichten komen?\n\nPlaatsen, bewerken en verwijderen van andermans berichten moeten alle drie geweigerd worden. Dat zijn de drie tests die het waardevolst zijn.",
            ],
        ]);
    }
}
