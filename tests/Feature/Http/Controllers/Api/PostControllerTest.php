<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Post;

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

    public function test_show(){
        
        $this->withoutExceptionHandling();

        $post = factory(Post::class)->create();

        $response = $this->json('GET',"/api/posts/$post->id");

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
        ->assertJson(['title' => $post->title])
        //status OK
        ->assertStatus(200); 

    }

    public function test_404_show(){

        $response = $this->json('GET','/api/posts/1000');

        //Recurso no encontrado
        $response->assertStatus(404);
    }

    public function test_update(){
    
        $this->withoutExceptionHandling();

        $post = factory(Post::class)->create();

        $response = $this->json('PUT',"/api/posts/$post->id",[
            'title' => 'Titulo actualizado'
        ]);

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
        ->assertJson(['title' => 'Titulo actualizado'])
        ->assertStatus(200);

        $this->assertDatabaseHas('posts',['title' => 'Titulo actualizado']);
    }

    public function test_delete(){

        $post = factory(Post::class)->create();

        $response = $this->json('DELETE',"/api/posts/$post->id");

        $response->assertSee(null)->assertStatus(200);

        $this->assertDatabaseMissing('posts',['id' => $post->id]);
    }

    public function test_index(){

        $this->withoutExceptionHandling();

        factory(Post::class,2)->create();

        $response = $this->json('GET','/api/posts');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id','title','created_at','updated_at']
            ]
        ])->assertStatus(200);
    }
}
