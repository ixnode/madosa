<?php

namespace App\DataProvider;

use App\Entity\BaseEntity;
use App\Utils\NamingConventionsConverter;
use Exception;
use ReflectionException;
use ReflectionObject;

/**
 * Class BaseDataProvider
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-08-07)
 * @package App\DataProvider
 */
abstract class BaseDataProvider
{
    const NAME_ID = 'id';

    /**
     * @param object $object
     * @param int $id
     * @return object
     * @throws ReflectionException
     * @throws Exception
     */
    protected function setId(object $object, int $id): object
    {
        $reflector = new ReflectionObject($object);

        if (!$reflector->hasProperty(self::NAME_ID)) {
            throw new Exception(sprintf('The given object "%s" must have the property "%s".', get_class($object), self::NAME_ID));
        }

        $property = $reflector->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($object, $id);

        return $object;
    }

    /**
     * Returns an entity as array.
     *
     * @return mixed[]
     */
    abstract public function getArray(): array;

    /**
     * Returns new empty entity.
     *
     * @return BaseEntity
     */
    abstract public function getObject(): BaseEntity;

    /**
     * Returns an entity as array.
     *
     * @param mixed[] $add
     * @param mixed[] $filter
     * @return mixed[]
     */
    public function getEntityArray(array $add = [], array $filter = []): array
    {
        $array = $this->getArray();

        foreach ($filter as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
            }
        }

        return array_merge_recursive($array, $add);
    }

    /**
     * Returns an entity as json.
     *
     * @param mixed[] $add
     * @param mixed[] $filter
     * @return string
     */
    public function getEntityJson(array $add = [], array $filter = []): string
    {
        $json = json_encode($this->getEntityArray($add, $filter));

        if ($json === false) {
            return '{}';
        }

        return $json;
    }

    /**
     * Returns a entity example class.
     *
     * @see: https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/working-with-objects.html
     *
     * @param mixed[] $add
     * @param mixed[] $filter
     * @return BaseEntity
     * @throws ReflectionException
     * @throws Exception
     */
    public function getEntityObject(array $add = [], array $filter = []): BaseEntity
    {
        $object = $this->getObject();
        $data = $this->getEntityArray($add, $filter);

        foreach ($data as $name => $value) {

            if ($name == self::NAME_ID) {
                $this->setId($object, intval($value));
                continue;
            }

            $namingConventionsConverter = new NamingConventionsConverter($name);
            $method = sprintf('%s%s', 'set', $namingConventionsConverter->getCamelCase());
            $callable = array($object, $method);

            if (!is_callable($callable)) {
                throw new Exception(sprintf('The given method "%s" is not callable.', $method));
            }

            call_user_func($callable, $value);
        }

        return $object;
    }
}
