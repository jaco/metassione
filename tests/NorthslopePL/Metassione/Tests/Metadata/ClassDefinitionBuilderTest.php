<?php
namespace NorthslopePL\Metassione\Tests\Metadata;

use LogicException;
use NorthslopePL\Metassione\Metadata\ClassDefinition;
use NorthslopePL\Metassione\Metadata\ClassDefinitionBuilder;
use NorthslopePL\Metassione\Metadata\ClassPropertyFinder;
use NorthslopePL\Metassione\Metadata\PropertyDefinition;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\ArrayPropertiesKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\ArrayPropertiesNullableKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\BasicTypesKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\BasicTypesNullableKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\ClassTypesTypeNullablePropertiesKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\ClassTypesTypePropertiesKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\SimpleKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\SubNamespace\OtherSimpleKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\TypeNotFoundKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\TypeNotFoundKlass2;
use NorthslopePL\Metassione\Tests\Fixtures\Builder\UndefinedTypeKlass;
use NorthslopePL\Metassione\Tests\Fixtures\Klasses\EmptyKlass;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;

class ClassDefinitionBuilderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var ClassDefinitionBuilder
	 */
	private $builder;

	protected function setUp()
	{
		$this->builder = new ClassDefinitionBuilder(new ClassPropertyFinder());
	}

	public function testEmptyClass()
	{
		$classDefinition = $this->builder->buildFromClass(EmptyKlass::class);
		$this->assertInstanceOf(ClassDefinition::class, $classDefinition);
		$this->assertEquals(EmptyKlass::class, $classDefinition->name);
		$this->assertEquals('NorthslopePL\Metassione\Tests\Fixtures\Klasses', $classDefinition->namespace);
		$this->assertEquals([], $classDefinition->properties);
	}

	public function testClassWithBasicTypeProperties()
	{
		$classDefinition = $this->builder->buildFromClass(BasicTypesKlass::class);
		$this->assertEquals(BasicTypesKlass::class, $classDefinition->name);
		$this->assertEquals('NorthslopePL\Metassione\Tests\Fixtures\Builder', $classDefinition->namespace);

		$this->assertCount(7, $classDefinition->properties);

		$this->assertEquals(new PropertyDefinition('stringValue', true, false, false, PropertyDefinition::BASIC_TYPE_STRING, false, new ReflectionProperty(BasicTypesKlass::class, 'stringValue')), $classDefinition->properties['stringValue']);
		$this->assertEquals(new PropertyDefinition('integerValue', true, false, false, PropertyDefinition::BASIC_TYPE_INTEGER, false, new ReflectionProperty(BasicTypesKlass::class, 'integerValue')), $classDefinition->properties['integerValue']);
		$this->assertEquals(new PropertyDefinition('intValue', true, false, false, PropertyDefinition::BASIC_TYPE_INTEGER, false, new ReflectionProperty(BasicTypesKlass::class, 'intValue')), $classDefinition->properties['intValue']);
		$this->assertEquals(new PropertyDefinition('floatValue', true, false, false, PropertyDefinition::BASIC_TYPE_FLOAT, false, new ReflectionProperty(BasicTypesKlass::class, 'floatValue')), $classDefinition->properties['floatValue']);
		$this->assertEquals(new PropertyDefinition('doubleValue', true, false, false, PropertyDefinition::BASIC_TYPE_FLOAT, false, new ReflectionProperty(BasicTypesKlass::class, 'doubleValue')), $classDefinition->properties['doubleValue']);
		$this->assertEquals(new PropertyDefinition('booleanValue', true, false, false, PropertyDefinition::BASIC_TYPE_BOOLEAN, false, new ReflectionProperty(BasicTypesKlass::class, 'booleanValue')), $classDefinition->properties['booleanValue']);
		$this->assertEquals(new PropertyDefinition('boolValue', true, false, false, PropertyDefinition::BASIC_TYPE_BOOLEAN, false, new ReflectionProperty(BasicTypesKlass::class, 'boolValue')), $classDefinition->properties['boolValue']);
	}

	public function testClassWithNullableBasicTypeProperties()
	{
		$classDefinition = $this->builder->buildFromClass(BasicTypesNullableKlass::class);
		$this->assertEquals(BasicTypesNullableKlass::class, $classDefinition->name);
		$this->assertEquals('NorthslopePL\Metassione\Tests\Fixtures\Builder', $classDefinition->namespace);

		$this->assertCount(7, $classDefinition->properties);

		$this->assertEquals(new PropertyDefinition('stringValue', true, false, false, PropertyDefinition::BASIC_TYPE_STRING, true, new ReflectionProperty(BasicTypesNullableKlass::class, 'stringValue')), $classDefinition->properties['stringValue']);
		$this->assertEquals(new PropertyDefinition('integerValue', true, false, false, PropertyDefinition::BASIC_TYPE_INTEGER, true, new ReflectionProperty(BasicTypesNullableKlass::class, 'integerValue')), $classDefinition->properties['integerValue']);
		$this->assertEquals(new PropertyDefinition('intValue', true, false, false, PropertyDefinition::BASIC_TYPE_INTEGER, true, new ReflectionProperty(BasicTypesNullableKlass::class, 'intValue')), $classDefinition->properties['intValue']);
		$this->assertEquals(new PropertyDefinition('floatValue', true, false, false, PropertyDefinition::BASIC_TYPE_FLOAT, true, new ReflectionProperty(BasicTypesNullableKlass::class, 'floatValue')), $classDefinition->properties['floatValue']);
		$this->assertEquals(new PropertyDefinition('doubleValue', true, false, false, PropertyDefinition::BASIC_TYPE_FLOAT, true, new ReflectionProperty(BasicTypesNullableKlass::class, 'doubleValue')), $classDefinition->properties['doubleValue']);
		$this->assertEquals(new PropertyDefinition('booleanValue', true, false, false, PropertyDefinition::BASIC_TYPE_BOOLEAN, true, new ReflectionProperty(BasicTypesNullableKlass::class, 'booleanValue')), $classDefinition->properties['booleanValue']);
		$this->assertEquals(new PropertyDefinition('boolValue', true, false, false, PropertyDefinition::BASIC_TYPE_BOOLEAN, true, new ReflectionProperty(BasicTypesNullableKlass::class, 'boolValue')), $classDefinition->properties['boolValue']);
	}

	public function testClassWithClassTypeProperties()
	{
		$classDefinition = $this->builder->buildFromClass(ClassTypesTypePropertiesKlass::class);
		$this->assertEquals(ClassTypesTypePropertiesKlass::class, $classDefinition->name);
		$this->assertEquals('NorthslopePL\Metassione\Tests\Fixtures\Builder', $classDefinition->namespace);

		$this->assertCount(4, $classDefinition->properties);

		// Fully Qualified Class name
		$this->assertEquals(new PropertyDefinition('propertyA', true, true, false, SimpleKlass::class, false, new ReflectionProperty(ClassTypesTypePropertiesKlass::class, 'propertyA')), $classDefinition->properties['propertyA']);
		// not Fully Qualified Class name
		$this->assertEquals(new PropertyDefinition('propertyB', true, true, false, SimpleKlass::class, false, new ReflectionProperty(ClassTypesTypePropertiesKlass::class, 'propertyB')), $classDefinition->properties['propertyB']);

		//

		// Fully Qualified Class name
		$this->assertEquals(new PropertyDefinition('propertyM', true, true, false, OtherSimpleKlass::class, false, new ReflectionProperty(ClassTypesTypePropertiesKlass::class, 'propertyM')), $classDefinition->properties['propertyM']);
		// partialy Fully Qualified Class name
		$this->assertEquals(new PropertyDefinition('propertyO', true, true, false, OtherSimpleKlass::class, false, new ReflectionProperty(ClassTypesTypePropertiesKlass::class, 'propertyO')), $classDefinition->properties['propertyO']);
	}

	public function testClassWithClassTypeNullableProperties()
	{
		$classDefinition = $this->builder->buildFromClass(ClassTypesTypeNullablePropertiesKlass::class);
		$this->assertEquals(ClassTypesTypeNullablePropertiesKlass::class, $classDefinition->name);
		$this->assertEquals('NorthslopePL\Metassione\Tests\Fixtures\Builder', $classDefinition->namespace);

		$this->assertCount(4, $classDefinition->properties);

		// Fully Qualified Class name
		$this->assertEquals(new PropertyDefinition('propertyA', true, true, false, SimpleKlass::class, true, new ReflectionProperty(ClassTypesTypeNullablePropertiesKlass::class, 'propertyA')), $classDefinition->properties['propertyA']);
		// not Fully Qualified Class name
		$this->assertEquals(new PropertyDefinition('propertyB', true, true, false, SimpleKlass::class, true, new ReflectionProperty(ClassTypesTypeNullablePropertiesKlass::class, 'propertyB')), $classDefinition->properties['propertyB']);

		//

		// Fully Qualified Class name
		$this->assertEquals(new PropertyDefinition('propertyM', true, true, false, OtherSimpleKlass::class, true, new ReflectionProperty(ClassTypesTypeNullablePropertiesKlass::class, 'propertyM')), $classDefinition->properties['propertyM']);
		// partialy Fully Qualified Class name
		$this->assertEquals(new PropertyDefinition('propertyO', true, true, false, OtherSimpleKlass::class, true, new ReflectionProperty(ClassTypesTypeNullablePropertiesKlass::class, 'propertyO')), $classDefinition->properties['propertyO']);
	}

	public function testWithArrayProperties()
	{
		$classDefinition = $this->builder->buildFromClass(ArrayPropertiesKlass::class);
		$this->assertEquals(ArrayPropertiesKlass::class, $classDefinition->name);
		$this->assertEquals('NorthslopePL\Metassione\Tests\Fixtures\Builder', $classDefinition->namespace);

		$this->assertCount(7, $classDefinition->properties);

		$this->assertEquals(new PropertyDefinition('stringArray_1', true, false, true, PropertyDefinition::BASIC_TYPE_STRING, false, new ReflectionProperty(ArrayPropertiesKlass::class, 'stringArray_1')), $classDefinition->properties['stringArray_1']);
		$this->assertEquals(new PropertyDefinition('stringArray_2', true, false, true, PropertyDefinition::BASIC_TYPE_STRING, false, new ReflectionProperty(ArrayPropertiesKlass::class, 'stringArray_2')), $classDefinition->properties['stringArray_2']);
		$this->assertEquals(new PropertyDefinition('stringArray_3', true, false, true, PropertyDefinition::BASIC_TYPE_STRING, false, new ReflectionProperty(ArrayPropertiesKlass::class, 'stringArray_3')), $classDefinition->properties['stringArray_3']);
		//
		$this->assertEquals(new PropertyDefinition('objectArray_1', true, true, true, SimpleKlass::class, false, new ReflectionProperty(ArrayPropertiesKlass::class, 'objectArray_1')), $classDefinition->properties['objectArray_1']);
		$this->assertEquals(new PropertyDefinition('objectArray_2', true, true, true, SimpleKlass::class, false, new ReflectionProperty(ArrayPropertiesKlass::class, 'objectArray_2')), $classDefinition->properties['objectArray_2']);
		$this->assertEquals(new PropertyDefinition('objectArray_3', true, true, true, SimpleKlass::class, false, new ReflectionProperty(ArrayPropertiesKlass::class, 'objectArray_3')), $classDefinition->properties['objectArray_3']);
		$this->assertEquals(new PropertyDefinition('objectArray_4', true, true, true, SimpleKlass::class, false, new ReflectionProperty(ArrayPropertiesKlass::class, 'objectArray_4')), $classDefinition->properties['objectArray_4']);
	}

	public function testWithNullableArrayProperties()
	{
		$classDefinition = $this->builder->buildFromClass(ArrayPropertiesNullableKlass::class);
		$this->assertEquals(ArrayPropertiesNullableKlass::class, $classDefinition->name);
		$this->assertEquals('NorthslopePL\Metassione\Tests\Fixtures\Builder', $classDefinition->namespace);

		$this->assertCount(7, $classDefinition->properties);

		$this->assertEquals(new PropertyDefinition('stringArray_1', true, false, true, PropertyDefinition::BASIC_TYPE_STRING, false, new ReflectionProperty(ArrayPropertiesNullableKlass::class, 'stringArray_1')), $classDefinition->properties['stringArray_1']);
		$this->assertEquals(new PropertyDefinition('stringArray_2', true, false, true, PropertyDefinition::BASIC_TYPE_STRING, false, new ReflectionProperty(ArrayPropertiesNullableKlass::class, 'stringArray_2')), $classDefinition->properties['stringArray_2']);
		$this->assertEquals(new PropertyDefinition('stringArray_3', true, false, true, PropertyDefinition::BASIC_TYPE_STRING, false, new ReflectionProperty(ArrayPropertiesNullableKlass::class, 'stringArray_3')), $classDefinition->properties['stringArray_3']);
		//
		$this->assertEquals(new PropertyDefinition('objectArray_1', true, true, true, SimpleKlass::class, false, new ReflectionProperty(ArrayPropertiesNullableKlass::class, 'objectArray_1')), $classDefinition->properties['objectArray_1']);
		$this->assertEquals(new PropertyDefinition('objectArray_2', true, true, true, SimpleKlass::class, false, new ReflectionProperty(ArrayPropertiesNullableKlass::class, 'objectArray_2')), $classDefinition->properties['objectArray_2']);
		$this->assertEquals(new PropertyDefinition('objectArray_3', true, true, true, SimpleKlass::class, false, new ReflectionProperty(ArrayPropertiesNullableKlass::class, 'objectArray_3')), $classDefinition->properties['objectArray_3']);
		$this->assertEquals(new PropertyDefinition('objectArray_4', true, true, true, SimpleKlass::class, false, new ReflectionProperty(ArrayPropertiesNullableKlass::class, 'objectArray_4')), $classDefinition->properties['objectArray_4']);
	}

	public function testWithUndefinedPropertyType()
	{
		$classDefinition = $this->builder->buildFromClass(UndefinedTypeKlass::class);
		$this->assertEquals(UndefinedTypeKlass::class, $classDefinition->name);

		$this->assertCount(6, $classDefinition->properties);

		$this->assertEquals(new PropertyDefinition('undefinedProperty_1', false, false, false, PropertyDefinition::BASIC_TYPE_NULL, true, new ReflectionProperty(UndefinedTypeKlass::class, 'undefinedProperty_1')), $classDefinition->properties['undefinedProperty_1']);
		$this->assertEquals(new PropertyDefinition('undefinedProperty_2', false, false, false, PropertyDefinition::BASIC_TYPE_NULL, true, new ReflectionProperty(UndefinedTypeKlass::class, 'undefinedProperty_2')), $classDefinition->properties['undefinedProperty_2']);
		$this->assertEquals(new PropertyDefinition('undefinedProperty_3', false, false, false, PropertyDefinition::BASIC_TYPE_NULL, true, new ReflectionProperty(UndefinedTypeKlass::class, 'undefinedProperty_3')), $classDefinition->properties['undefinedProperty_3']);
		$this->assertEquals(new PropertyDefinition('voidValue', false, false, false, PropertyDefinition::BASIC_TYPE_NULL, true, new ReflectionProperty(UndefinedTypeKlass::class, 'voidValue')), $classDefinition->properties['voidValue']);
		$this->assertEquals(new PropertyDefinition('mixedValue', false, false, false, PropertyDefinition::BASIC_TYPE_NULL, true, new ReflectionProperty(UndefinedTypeKlass::class, 'mixedValue')), $classDefinition->properties['mixedValue']);
		$this->assertEquals(new PropertyDefinition('nullValue', false, false, false, PropertyDefinition::BASIC_TYPE_NULL, true, new ReflectionProperty(UndefinedTypeKlass::class, 'nullValue')), $classDefinition->properties['nullValue']);
	}

	public function testWithNotFoundClassProperty()
	{
		$this->setExpectedException(
			LogicException::class,
			'Class Foo (NorthslopePL\Metassione\Tests\Fixtures\Builder\Foo) not found for property NorthslopePL\Metassione\Tests\Fixtures\Builder\TypeNotFoundKlass::fooValue'
		);

		$this->builder->buildFromClass(TypeNotFoundKlass::class);
	}

	public function testWithNotFoundClassProperty2()
	{
		$this->setExpectedException(
			LogicException::class,
			'Class NorthslopePL\Metassione\Tests\Fixtures\Builder\Foo (NorthslopePL\Metassione\Tests\Fixtures\Builder\NorthslopePL\Metassione\Tests\Fixtures\Builder\Foo) not found for property NorthslopePL\Metassione\Tests\Fixtures\Builder\TypeNotFoundKlass2::fooValue'
		// TODO duplicated namespaces in the error message
		);

		$this->builder->buildFromClass(TypeNotFoundKlass2::class);
	}
}
