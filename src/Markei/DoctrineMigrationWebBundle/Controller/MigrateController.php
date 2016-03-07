<?php

namespace Markei\DoctrineMigrationWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Markei\DoctrineMigrationWebBundle\DoctrineMigrations\MemoryOutputWriter;
use Doctrine\DBAL\Migrations\Migration;

class MigrateController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('MarkeiDoctrineMigrationWebBundle:Migrate:index.html.twig', [
            'connections' => $this->getDoctrine()->getConnections(),
            'name' => $this->container->getParameter('doctrine_migrations.name')
        ]);
    }

    /**
     * @Route("/{connectionName}/")
     */
    public function viewAction($connectionName)
    {
        $connection = $this->getDoctrine()->getConnection($connectionName);

        $outputWriter = new MemoryOutputWriter();

        $configuration = new Configuration($connection);
        $configuration->setName($this->container->getParameter('doctrine_migrations.name'));
        $configuration->setOutputWriter($outputWriter);
        $configuration->setMigrationsTableName($this->container->getParameter('doctrine_migrations.table_name'));
        $configuration->setMigrationsDirectory($this->container->getParameter('doctrine_migrations.dir_name'));
        $configuration->setMigrationsNamespace($this->container->getParameter('doctrine_migrations.namespace'));

        $migration = new Migration($configuration);

        $executedMigrations = $configuration->getMigratedVersions();
        $availableMigrations = $configuration->getAvailableVersions();

        $executedUnavailableMigrations = array_diff($executedMigrations, $availableMigrations);
        foreach ($executedUnavailableMigrations as $i => $executedUnavailableMigration) {
            $executedUnavailableMigrations[$i] = $executedUnavailableMigration . ' (' . $configuration->getDateTime($executedUnavailableMigration) . ')';
        }

        $dryRun = true;
        $migration->setNoMigrationException(true);
        $sql = $migration->migrate(null, $dryRun);

        return $this->render('MarkeiDoctrineMigrationWebBundle:Migrate:view.html.twig', [
            'connectionName' => $connectionName,
            'executedUnavailableMigrations' => $executedUnavailableMigrations,
            'to' => $configuration->getLatestVersion(),
            'from' => $configuration->getCurrentVersion(),
            'migrations' => $sql,
            'name' => $this->container->getParameter('doctrine_migrations.name')
        ]);
    }

    /**
     * @Route("/{connectionName}/execute/{version}")
     * @Method("POST")
     */
    public function executeAction($connectionName, $version)
    {
        $connection = $this->getDoctrine()->getConnection($connectionName);

        $outputWriter = new MemoryOutputWriter();

        $configuration = new Configuration($connection);
        $configuration->setName($this->container->getParameter('doctrine_migrations.name'));
        $configuration->setOutputWriter($outputWriter);
        $configuration->setMigrationsTableName($this->container->getParameter('doctrine_migrations.table_name'));
        $configuration->setMigrationsDirectory($this->container->getParameter('doctrine_migrations.dir_name'));
        $configuration->setMigrationsNamespace($this->container->getParameter('doctrine_migrations.namespace'));

        $migration = new Migration($configuration);

        $currentVersion = $configuration->getCurrentVersion();

        $dryRun = false;
        $migration->setNoMigrationException(true);
        $sql = $migration->migrate($version, $dryRun, true);

        return $this->render('MarkeiDoctrineMigrationWebBundle:Migrate:execute.html.twig', [
            'connectionName' => $connectionName,
            'to' => $version,
            'from' => $currentVersion,
            'output' => $outputWriter->getMemory(),
            'name' => $this->container->getParameter('doctrine_migrations.name')
        ]);
    }
}
