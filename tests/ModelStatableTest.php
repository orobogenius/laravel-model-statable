<?php

namespace Orobogenius\Statable\Tests;

use Mockery as m;
use Orobogenius\Statable\Statable;
use Orobogenius\Statable\Tests\Fixtures\User;
use Orobogenius\Statable\Tests\Fixtures\Invoice;
use Orobogenius\Statable\Tests\Fixtures\InvoiceItem;

class ModelStatableTest extends \Orchestra\Testbench\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');

        $this->createFixturesMigrations();
    }

    /** @test */    
    public function callable_attributes_can_be_expanded()
    {
        $user = new User;
        $user->name = 'Baz';

        $user->states('superAdmin');

        $this->assertEquals(($user->is_admin && $user->is_moderator), $user->is_super_admin);
    }

    /** @test */
    public function can_apply_states_to_relations()
    {
        $invoice = new Invoice;
        $invoice->save();
    
        $invoice->items()->saveMany([new InvoiceItem, new InvoiceItem]);

        $invoice->states('paid');

        $this->assertEquals('paid', $invoice->status);

        $this->assertDatabaseHas('invoices', $invoice->toArray());

        foreach ($invoice->items as $item) {
            $this->assertEquals('processed', $item->status);

            $this->assertDatabaseHas('invoice_items', $item->toArray());
        }
    }

    /** @test */
    public function it_errors_when_state_is_not_defined()
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = new User;
        $user->name = 'Foo';

        $user->states('invalidState');
    }

    /** @test */
    public function can_apply_states_to_model()
    {
        $user = new User;
        $user->name = 'Bar';

        $user->states(['admin', 'moderator']);

        $this->assertTrue($user->is_admin);

        $this->assertTrue($user->is_moderator);

        $this->assertDatabaseHas('users', [
            'name' => 'Bar',
            'is_admin' => true,
            'is_moderator' => true
        ]);
    }

    protected function createFixturesMigrations()
    {
        \Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_super_admin')->default(false)->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_moderator')->default(false);
            $table->timestamps();
        });

        \Schema::create('invoices', function ($table) {
            $table->increments('id');
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamps();
        });

        \Schema::create('invoice_items', function ($table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->enum('status', ['processed', 'unprocessed'])->default('unprocessed');
            $table->timestamps();
        });
    }
}
