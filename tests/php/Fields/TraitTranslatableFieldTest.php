<?php

namespace FjordTest\Fields;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Fjord\Crud\Field;
use Fjord\Crud\Fields\Input;
use FjordTest\BackendTestCase;
use Illuminate\Database\Eloquent\Model;
use FjordTest\Traits\InteractsWithFields;
use Fjord\Crud\Models\Traits\Translatable;
use Fjord\Crud\Fields\Concerns\FieldHasRules;
use Fjord\Crud\Fields\Concerns\TranslatableField as TranslatableFieldTrait;

class TraitTranslatableFieldTest extends BackendTestCase
{
    use InteractsWithFields;

    /** @test */
    public function it_is_not_translatable_by_default()
    {
        $field = $this->getField(TranslatableField::class);
        $this->assertFalse($field->translatable);
    }

    /** @test */
    public function it_notices_translatable_attributes_on_translatable_models()
    {
        $field = $this->getField(
            TranslatableField::class,
            'translated_attribute',
            TranslatableFieldModel::class
        );
        $this->assertTrue($field->translatable);

        $field = $this->getField(
            TranslatableField::class,
            'non_translated_attribute',
            TranslatableFieldModel::class
        );
        $this->assertFalse($field->translatable);
    }
}

class TranslatableField extends Field
{
    use TranslatableFieldTrait;
}

class TranslatableFieldModel extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['translated_attribute'];
}
