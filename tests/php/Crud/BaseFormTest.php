<?php

namespace FjordTest\Crud;

use Mockery as m;
use Fjord\Crud\Field;
use Fjord\Crud\BaseForm;
use Fjord\Vue\Component;
use FjordTest\BackendTestCase;
use Fjord\Support\Facades\Fjord;
use Illuminate\Database\Eloquent\Model;
use Fjord\Exceptions\MethodNotFoundException;

class BaseFormTest extends BackendTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->form = new BaseForm(DummyModel::class);
    }

    /** @test */
    public function it_fails_when_not_existing_field_gets_called()
    {
        $this->expectException(MethodNotFoundException::class);
        $this->form->someFormField();
    }

    /** @test */
    public function it_sets_correct_route_prefix()
    {
        $this->form->setRoutePrefix('dummy_prefix');
        $this->assertEquals('dummy_prefix', $this->form->getRoutePrefix());
        $this->form->setRoutePrefix(Fjord::url('dummy_prefix'));
        $this->assertEquals('dummy_prefix', $this->form->getRoutePrefix());
    }

    /** @test */
    public function it_passes_route_prefix_to_field()
    {
        $this->form->setRoutePrefix('dummy_prefix');
        $field = $this->form->input('text');
        $this->assertEquals('dummy_prefix', $field->route_prefix);
    }

    /** @test */
    public function test_wrapper()
    {
        $this->assertFalse($this->form->inWrapper());
        $this->form->wrapper('dummy-wrapper', function ($form) {
            $this->assertTrue($form->inWrapper());
        });
        $this->assertFalse($this->form->inWrapper());
    }

    /** @test */
    public function wrapper_returns_wrapping_component()
    {
        $component = $this->form->wrapper('dummy-wrapper', function ($form) {
            //
        });

        $this->assertInstanceOf(Component::class, $component);
        $this->assertEquals('dummy-wrapper', $component->getName());
    }

    /** @test */
    public function test_wrapper_in_wrapper()
    {
        $this->assertFalse($this->form->inWrapper());
        $this->form->wrapper('dummy-wrapper', function ($form) {
            $this->form->wrapper('nested-dummy-wrapper', function ($form) {
                $this->assertTrue($form->inWrapper());
            });

            $this->assertTrue($form->inWrapper());
            $children = $form->getWrapper()->children;
            $this->assertEquals(1, $children->count());
        });
        $this->assertFalse($this->form->inWrapper());
    }

    /** @test */
    public function it_appends_fields_to_wrapper()
    {
        $this->form->wrapper('dummy-wrapper', function ($form) {
            $form->registerField(DummyBaseFormFieldMock::class, 'dummy-field');
            $form->registerField(DummyBaseFormFieldMock::class, 'other-field');

            $children = $form->getWrapper()->children;
            $this->assertCount(2, $children);
            $this->assertEquals("dummy-field", $children[0]->field->id);
            $this->assertEquals("other-field", $children[1]->field->id);
        });
    }

    /** @test */
    public function it_returns_registered_fields()
    {
        $this->form->registerField(DummyBaseFormFieldMock::class, 'dummy-field');
        $this->form->registerField(DummyBaseFormFieldMock::class, 'other-field');

        $fields = $this->form->getRegisteredFields();
        $this->assertEquals(2, $fields->count());
        $this->assertEquals('dummy-field', $fields[0]->id);
        $this->assertEquals('other-field', $fields[1]->id);
    }

    /** @test */
    public function it_returns_wrapped_registered_fields()
    {
        $this->form->registerField(DummyBaseFormFieldMock::class, 'dummy-field');

        $this->form->wrapper('dummy-wrapper', function ($form) {
            $this->form->registerField(DummyBaseFormFieldMock::class, 'wrapped-field');
        });

        $fields = $this->form->getRegisteredFields();

        $this->assertEquals(3, $fields->count());
        $this->assertEquals('dummy-field', $fields[0]->id);
        $this->assertEquals('wrapped-field', $fields[2]->id);
    }

    /** @test */
    public function findField_finds_field_by_id()
    {
        $this->form->registerField(DummyBaseFormFieldMock::class, 'dummy-field');
        $this->form->registerField(DummyBaseFormFieldMock::class, 'other-field');


        $field = $this->form->findField('dummy-field');
        $this->assertEquals('dummy-field', $field->id);
        $field = $this->form->findField('other-field');
        $this->assertEquals('other-field', $field->id);
    }

    /** @test */
    public function checking_registrar_before_registering_new_field()
    {
        // Assuming checkComplete is called on field instance in registrar when registering new field.
        $field = m::mock('field');
        $field->shouldReceive('checkComplete')->once();
        $this->setUnaccessibleProperty($this->form, 'registrar', $field);

        $this->form->registerField(DummyBaseFormFieldMock::class, 'new-field');
    }

    /** @test */
    public function checking_registrar_before_passing_to_vue()
    {
        $field = m::mock('field');
        $field->shouldReceive('checkComplete')->once();
        $this->setUnaccessibleProperty($this->form, 'registrar', $field);

        $this->form->toJson();
    }
}

class RelatedDummyModel extends Model
{
}

class DummyModel extends Model
{
    public function dummy_relation()
    {
        return $this->hasOne(RelatedDummyModel::class);
    }
}

class DummyBaseFormFieldMock extends Field
{
}
