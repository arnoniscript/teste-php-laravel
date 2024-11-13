<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Document;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o campo 'contents' aceita uma string muito longa sem lançar exceção, visto que o campo content não possui limite de caracteres.
     *
     * @return void
     */
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
}
