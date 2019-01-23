<?php

namespace FroshProfiler\Components\Tracleable;

use ProxyManager\Generator\ClassGenerator;
use ProxyManager\Generator\MethodGenerator;
use ProxyManager\Generator\Util\ClassGeneratorUtils;
use ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use ReflectionClass;
use ReflectionMethod;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Reflection\MethodReflection;

class TraceableGenerator
{
    /**
     * Returns proxy class name
     *
     * @param string $class
     *
     * @return string
     */
    public function getProxyClassName($class)
    {
        return 'FroshProfilerProxy\\' . $class;
    }

    public function generateProxyClass($class)
    {
        $reflectionClass = new ReflectionClass($class);

        // Make sure the we can create a proxy of the class
        CanProxyAssertion::assertClassCanBeProxied($reflectionClass, false);

        // Generate the base class
        $proxyClassName = $this->getProxyClassName($class);
        $classGenerator = new ClassGenerator($proxyClassName);

        $classGenerator->setExtendedClass($reflectionClass->getName());

        // Prepare generators for the hooked methods
        $hookMethods = $this->getHookedMethods($reflectionClass);
        $hookMethodGenerators = [];
        foreach ($hookMethods as $method) {
            $hookMethodGenerators[$method->getName()] = $this->createMethodGenerator($method);
        }

        $getHookMethodsGenerator = MethodGenerator::fromArray([
            'name' => '__construct',
            'parameters' => [
                'parent',
                'stopwatch',
            ],
            'body' => '$this->parent = $parent;' . PHP_EOL . '$this->stopwatch = $stopwatch;',
        ]);
        ClassGeneratorUtils::addMethodIfNotFinal($reflectionClass, $classGenerator, $getHookMethodsGenerator);

        // Add the hooked methods
        foreach ($hookMethodGenerators as $methodGenerator) {
            ClassGeneratorUtils::addMethodIfNotFinal($reflectionClass, $classGenerator, $methodGenerator);
        }

        $classGenerator->addProperty('parent', null, PropertyGenerator::FLAG_PRIVATE);
        $classGenerator->addProperty('stopwatch', null, PropertyGenerator::FLAG_PRIVATE);

        // Generate the proxy file contents
        return [$proxyClassName, "<?php\n" . $classGenerator->generate()];
    }

    /**
     * @param ReflectionClass $class
     *
     * @return ReflectionMethod[]
     */
    protected function getHookedMethods(ReflectionClass $class)
    {
        return array_filter(
            $class->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED),
            function (ReflectionMethod $method) {
                return !$method->isConstructor()
                    && !$method->isFinal()
                    && !$method->isStatic()
                    && strpos($method->getName(), '__') !== 0;
            }
        );
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return \Zend\Code\Generator\MethodGenerator
     * @throws \ReflectionException
     */
    protected function createMethodGenerator(ReflectionMethod $method)
    {
        $originalMethod = new MethodReflection(
            $method->getDeclaringClass()->getName(),
            $method->getName()
        );

        // Prepare parameters for the hook manager
        $params = array_map(
            function ($parameter) {
                return '$' . $parameter->getName();
            },
            $originalMethod->getParameters()
        );

        $eventName = $method->getDeclaringClass()->getName() . '::' . $method->getName();

        // Create the method
        $methodGenerator = MethodGenerator::fromReflection($originalMethod);
        $methodGenerator->setDocblock('@inheritdoc');
        $methodGenerator->setBody(
            '$this->stopwatch->start("' . $eventName . '");' . PHP_EOL .
            '$returnValue = $this->parent->' . $method->name . '(' . implode(', ', $params) .
            ');' . PHP_EOL . '$this->stopwatch->stop("' . $eventName . '");' . PHP_EOL . 'return $returnValue;'
        );

        return $methodGenerator;
    }
}
