<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentRevision;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::updateOrCreate(
            ['email' => 'alice@docs.test'],
            ['name' => 'Alice', 'password' => Hash::make('password')]
        );

        $bob = User::updateOrCreate(
            ['email' => 'bob@docs.test'],
            ['name' => 'Bob', 'password' => Hash::make('password')]
        );

        $document = Document::updateOrCreate(
            ['title' => 'Dokumen Kolaboratif Demo'],
            [
                'user_id' => $alice->id,
                'content' => '<p>Selamat datang di <strong>Google Docs Clone</strong>!</p><p>Buka dokumen ini di 2 browser (login Alice & Bob) untuk uji realtime.</p>',
                'version' => 1,
            ]
        );

        DocumentRevision::updateOrCreate(
            [
                'document_id' => $document->id,
                'version_number' => 1,
            ],
            [
                'user_id' => $alice->id,
                'content' => $document->content,
                'change_summary' => 'Versi awal',
            ]
        );
    }
}
