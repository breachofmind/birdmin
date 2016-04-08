<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Birdmin\Permission;
use Birdmin\User;
use Birdmin\Page;
use Birdmin\Role;
use Illuminate\Support\Facades\Session;

class RESTTest extends TestCase
{
    /**
     * Model for testing
     * @var \Birdmin\Core\Model
     */
    private $model;


    public function setUp()
    {
        parent::setUp();
        \Birdmin\Product::where('name','Test Product')->delete();

        Session::start();
        $this->be(User::find(1));
    }

    public function test_throws404ifMissingModel()
    {
        $this->call('GET',url('api/v1/none'));

        $this->assertResponseStatus(404);
    }


    public function test_responseIsRESTResponseObject()
    {
        $this->call('GET',url('api/v1/products'));

        $this->assertResponseStatus(200);
        $this->assertInstanceOf('Birdmin\Support\RESTResponseObject', $this->response->getContent());
    }

    public function test_fetchAll()
    {
        $this->call('GET',url('api/v1/products'));

        $this->assertResponseStatus(200);
        $this->assertNotEmpty($this->response->getContent()->getData());
        $this->assertInstanceOf('Birdmin\Http\Responses\RESTResponse', $this->response);
        $this->assertEquals('OK', $this->response->getStatusText());
    }

    public function test_fetch()
    {
        $this->call('GET',url('api/v1/products/5'));

        $this->assertResponseStatus(200);
        $this->assertNotEmpty($this->response->getContent()->getData());
    }

    public function test_throws406withMissingFields()
    {
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'bundle_id' => 1,
            '_token' => csrf_token()
        ];
        $this->call('POST', url('api/v1/products'), $data);

        $this->assertInstanceOf('Birdmin\Support\RESTResponseObject', $this->response->getContent());
        $this->assertResponseStatus(406);
        $this->assertEquals(2, $this->response->getContent()->errorCount());
    }

    public function test_createObject()
    {
        // Slug and status are missing.
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'slug' => 'test',
            'status' => 'draft',
            'bundle_id' => 1,
            '_token' => csrf_token()
        ];
        $this->call('POST', url('api/v1/products'), $data);

        $this->assertResponseStatus(200);
        $model = $this->response->getContent()->getData();
        $this->assertInstanceOf(\Birdmin\Core\Model::class, $model);
        $this->assertEquals($data['slug'], $model->slug);

        $model->delete();
    }

    public function test_updateObject()
    {
        $create = [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'slug' => 'test',
            'status' => 'draft',
            'bundle_id' => 1,
        ];
        $created = \Birdmin\Product::create($create);

        $data = ['status' => 'publish', 'name'=>'Updated Product', '_token' => csrf_token()];

        $this->call('PUT', $created->objectUrl, $data);

        $this->assertResponseStatus(200);
        $model = $this->response->getContent()->getData();
        $this->assertInstanceOf(\Birdmin\Core\Model::class, $model);
        $this->assertEquals($data['status'], $model->status);
        $this->assertEquals($data['name'], $model->name);

        $model->delete();
    }


    public function test_deleteObject()
    {
        $create = [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'slug' => 'test',
            'status' => 'draft',
            'bundle_id' => 1,
        ];
        $created = \Birdmin\Product::create($create);

        $this->call('DELETE', $created->objectUrl, ['_token'=>csrf_token()]);

        $this->assertResponseStatus(200);
        $model = $this->response->getContent()->getData();
        $this->assertInstanceOf(\Birdmin\Core\Model::class, $model);
        $this->assertEquals($created->id, $model->id);

        // Attempt to fetch should get a 404 status
        $this->call('GET', $created->objectUrl);
        $this->assertResponseStatus(404);
    }

}
