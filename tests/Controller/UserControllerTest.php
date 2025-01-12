<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/user/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(User::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'user[name]' => 'Testing',
            'user[email]' => 'Testing',
            'user[password]' => 'Testing',
            'user[validated]' => 'Testing',
            'user[roles]' => 'Testing',
            'user[custom_url]' => 'Testing',
            'user[created_at]' => 'Testing',
            'user[updated_at]' => 'Testing',
            'user[deleted_at]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setName('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPassword('My Title');
        $fixture->setValidated('My Title');
        $fixture->setRoles('My Title');
        $fixture->setCustom_url('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');
        $fixture->setDeleted_at('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setName('Value');
        $fixture->setEmail('Value');
        $fixture->setPassword('Value');
        $fixture->setValidated('Value');
        $fixture->setRoles('Value');
        $fixture->setCustom_url('Value');
        $fixture->setCreated_at('Value');
        $fixture->setUpdated_at('Value');
        $fixture->setDeleted_at('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user[name]' => 'Something New',
            'user[email]' => 'Something New',
            'user[password]' => 'Something New',
            'user[validated]' => 'Something New',
            'user[roles]' => 'Something New',
            'user[custom_url]' => 'Something New',
            'user[created_at]' => 'Something New',
            'user[updated_at]' => 'Something New',
            'user[deleted_at]' => 'Something New',
        ]);

        self::assertResponseRedirects('/user/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getValidated());
        self::assertSame('Something New', $fixture[0]->getRoles());
        self::assertSame('Something New', $fixture[0]->getCustom_url());
        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getUpdated_at());
        self::assertSame('Something New', $fixture[0]->getDeleted_at());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setName('Value');
        $fixture->setEmail('Value');
        $fixture->setPassword('Value');
        $fixture->setValidated('Value');
        $fixture->setRoles('Value');
        $fixture->setCustom_url('Value');
        $fixture->setCreated_at('Value');
        $fixture->setUpdated_at('Value');
        $fixture->setDeleted_at('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/user/');
        self::assertSame(0, $this->repository->count([]));
    }
}
