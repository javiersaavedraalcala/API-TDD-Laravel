<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
     
    use RefreshDatabase;

    public function test_store()
    {
        // $this->withoutExceptionHandling();

        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user, 'api')->Json('POST', '/api/posts', [
            'title' => 'El post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'El post de prueba'])
            ->assertStatus(201); //Ok, resource created

        $this->assertDatabaseHas('posts', ['title' => 'El post de prueba']);
    }

    public function test_validate_title()
    {

        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => ''
        ]);
        
        //Status http 422
        $response->assertStatus(422) 
            ->assertJsonValidationErrors('title');

    }

    public function test_show()
    {
        $user = \App\Models\User::factory()->create();
        $post = \App\Models\Post::factory()->create();

        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts/$post->id"); //id = 1

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200); //Ok
    }

    public function test_404_show()
    {

        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts/1000'); 

        $response->assertStatus(404); //Ok
    }

    public function test_update()
    {
        // $this->withoutExceptionHandling();
        $user = \App\Models\User::factory()->create();
        $post = \App\Models\Post::factory()->create();
        
        $response = $this->actingAs($user, 'api')->json('PUT', "/api/posts/$post->id", [
            'title' => 'Nuevo'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'Nuevo'])
            ->assertStatus(200); //Ok, resource created

        $this->assertDatabaseHas('posts', ['title' => 'Nuevo']);
    }

    public function test_delete()
    {
        // $this->withoutExceptionHandling();
        $user = \App\Models\User::factory()->create();
        $post = \App\Models\Post::factory()->create();
        
        $response = $this->actingAs($user, 'api')->json('DELETE', "/api/posts/$post->id");

        $response->assertSee(null)
            ->assertStatus(204); //Whithout content

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_index()
    {

        $user = \App\Models\User::factory()->create();
        $post = \App\Models\Post::factory(5)->create();

        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'created_at', 'updated_at']
            ]
        ])->assertStatus(200);
    }

    public function test_guest()
    {

        $this->json('GET', '/api/posts')->assertStatus(401);
        $this->json('POST', '/api/posts')->assertStatus(401);
        $this->json('GET', '/api/posts/1000')->assertStatus(401);
        $this->json('PUT', '/api/posts/1000')->assertStatus(401);
        $this->json('DELETE', '/api/posts/1000')->assertStatus(401);
    }

}
