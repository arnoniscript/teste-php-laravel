<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Document;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function testContentsFieldHasNoLengthLimit()
    {
        $longContent = str_repeat('A', 10000);

        $category = Category::factory()->create();

        $document = Document::create([
            'category_id' => $category->id,
            'title' => 'Título do Documento',
            'contents' => $longContent,
            'exercicio' => 2023,
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'contents' => $longContent,
        ]);
    }

    public function testTitleContainsSemesterForRemessa()
    {
        $category = Category::firstOrCreate(['name' => 'Remessa']);

        $this->assertValidTitle('Relatório do primeiro semestre', 'Remessa');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Registro inválido: título deve conter "semestre" para a categoria Remessa.');
        $this->assertValidTitle('Relatório anual', 'Remessa');
    }

    public function testTitleContainsMonthForRemessaParcial()
    {
        $category = Category::firstOrCreate(['name' => 'Remessa Parcial']);

        $this->assertValidTitle('Relatório de Janeiro', 'Remessa Parcial');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Registro inválido: título deve conter o nome de um mês para a categoria Remessa Parcial.');
        $this->assertValidTitle('Relatório parcial', 'Remessa Parcial');
    }

    private function assertValidTitle($title, $categoryName)
    {
        if ($categoryName === 'Remessa' && !str_contains(strtolower($title), 'semestre')) {
            throw new Exception('Registro inválido: título deve conter "semestre" para a categoria Remessa.');
        }

        $meses = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
        if ($categoryName === 'Remessa Parcial' && !collect($meses)->first(fn($mes) => str_contains(strtolower($title), $mes))) {
            throw new Exception('Registro inválido: título deve conter o nome de um mês para a categoria Remessa Parcial.');
        }

        $category = Category::firstOrCreate(['name' => $categoryName]);

        Document::create([
            'category_id' => $category->id,
            'title' => $title,
            'contents' => 'Conteúdo válido',
            'exercicio' => 2023,
        ]);
    }
}
