<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    // public function testExample()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
    use RefreshDatabase;

    public function test_store()
    {
        $this->withoutExceptionHandling();
        $response = $this->json('POST','/api/posts',[
            'title' => 'Post de prueba para el TDD.'
        ]);

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
        ->assertJson(['title' => 'Post de prueba para el TDD.'])
        //status que dice que se tiene una respuesta OK y que se creo un recurso
        ->assertStatus(201);

        $this->assertDatabaseHas('posts',['title' => 'Post de prueba para el TDD.']);
    }

    public function test_validate_title(){

        $response = $this->json('POST','/api/posts',[
            'title' => ''
        ]);

        //status que dice que no se completo la solicitud, se creo bien pero fue rechazado
        $response->assertStatus(422)
        ->assertJsonValidationErrors('title');
    }
}
