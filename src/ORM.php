<?php

namespace App;

use App\Contracts\ORM as ORMContract;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM as CycleORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema as ORMSchema;
use Cycle\Schema;
use Cycle\Annotated;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Lemon\Contracts\Config\Config;
use Lemon\Contracts\Support\Env;

class ORM implements ORMContract
{
    public readonly DatabaseManager $dbal;

    public readonly EntityManagerInterface $manager;

    private readonly ORMInterface $orm;

    public function __construct(
        public readonly Config $config,
        Env $env
    ) {
        // Maybe consider some bootloader class
        $this->dbal = new DatabaseManager(new DatabaseConfig($this->config->get('database')));

        $finder = (new \Symfony\Component\Finder\Finder())->files()->in([__DIR__]);
        $locator = new \Spiral\Tokenizer\ClassLocator($finder);

        AnnotationRegistry::registerLoader('class_exists');

        $schema = (new Schema\Compiler())->compile(new Schema\Registry($this->dbal), [
            new Schema\Generator\ResetTables(),             
            new Annotated\Embeddings($classLocator),        
            new Annotated\Entities($classLocator),          
            new Annotated\TableInheritance(),               
            new Annotated\MergeColumns(),                   
            new Schema\Generator\GenerateRelations(),       
            new Schema\Generator\GenerateModifiers(),       
            new Schema\Generator\ValidateEntities(),        
            new Schema\Generator\RenderTables(),            
            new Schema\Generator\RenderRelations(),         
            new Schema\Generator\RenderModifiers(),         
            new Annotated\MergeIndexes(),                   
            $env->get('DEBUG', false) ? new Schema\Generator\SyncTables() : null,
            new Schema\Generator\GenerateTypecast(),        
        ]);

        $this->orm = new CycleORM(new Factory($dbal), new ORMSchema($schema));
        $this->manager = new EntityManager($this->orm);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->manager->run();
    }

    public function getORM(): ORMInterface
    {
        return $this->orm;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->manager;
    }
}
