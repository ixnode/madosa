<?php declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2021 Björn Hempel <bjoern@hempel.li>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Tests\Api\Entity;

use App\Context\BaseContext;
use App\Exception\YadsException;
use App\Tests\Api\Library\ApiTestCaseWorker;
use App\Tests\Api\Library\BaseApiTestCase;
use App\Utils\ArrayHolder;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class TagTest
 *
 * Tag tasks:
 * ----------
 * - Get entities expect empty list
 * - Create first entity
 * - Get entities expect one hit
 * - Get first entity
 * - Update first entity
 * - Get updated first entity
 * - Create second entity
 * - Get entities expect two hits
 * - Get second entity
 * - Delete first entity
 * - Get entities expect one hit
 *
 * @see Documentation at https://api-platform.com/docs/distribution/testing/.
 * @package App\Tests\Api
 */
class TagTest extends BaseApiTestCase
{
    /**
     * GET /api/v1/tags
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 1) Get tags (empty).
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function getEntitiesExpectEmptyList(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('list_tags_empty');

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * POST /api/v1/tags
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 2) Create first tag.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function createFirstEntity(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('create_tag_1')
            ->setRequestType(ApiTestCaseWorker::REQUEST_TYPE_CREATE)
            ->setBody($this->tagDataProvider->getEntityArray())
            ->setExpected($this->tagDataProvider->getEntityArray() + ['id' => new ArrayHolder('create_tag_1', 'id')])
            ->setUnset(['createdAt', 'updatedAt', ])
        ;

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * GET /api/v1/tags
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 3) Get tags (expect one hit).
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function getEntitiesExpectOneHit(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('list_tags_1')
            ->setUnset(['hydra:member' => ['createdAt', 'updatedAt', ]])
            ->setNamespaces(['create_tag_1'])
        ;

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * GET /api/v1/tags/[id]
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 4) Get first tag with id x.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function getFirstEntity(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('get_tag_1')
            ->setRequestType(ApiTestCaseWorker::REQUEST_TYPE_READ)
            ->setExpected($this->tagDataProvider->getEntityArray() + ['id' => new ArrayHolder('create_tag_1', 'id')])
            ->setUnset(['createdAt', 'updatedAt', ])
            ->addParameter(new ArrayHolder('create_tag_1', 'id'));

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * PUT /api/v1/tags/[id]
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 5) Update first tag with id x.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function updateFirstEntity(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('update_tag_1')
            ->setRequestType(ApiTestCaseWorker::REQUEST_TYPE_UPDATE)
            ->setBody($this->tagDataProvider->getEntityArray(recordNumber: 1))
            ->setExpected($this->tagDataProvider->getEntityArray(recordNumber: 1) + ['id' => new ArrayHolder('create_tag_1', 'id')])
            ->setUnset(['createdAt', 'updatedAt', ])
            ->addParameter(new ArrayHolder('create_tag_1', 'id'));

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * GET /api/v1/tags/[id]
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 6) Get updated first tag with id x.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function getUpdatedFirstEntity(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('get_tag_1_updated')
            ->setRequestType(ApiTestCaseWorker::REQUEST_TYPE_READ)
            ->setExpected($this->tagDataProvider->getEntityArray(recordNumber: 1) + ['id' => new ArrayHolder('create_tag_1', 'id')])
            ->setUnset(['createdAt', 'updatedAt', ])
            ->addParameter(new ArrayHolder('create_tag_1', 'id'));

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * POST /api/v1/tags
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 7) Create second tag.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function createSecondEntity(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('create_tag_2')
            ->setRequestType(ApiTestCaseWorker::REQUEST_TYPE_CREATE)
            ->setBody($this->tagDataProvider->getEntityArray(recordNumber: 1))
            ->setExpected($this->tagDataProvider->getEntityArray(recordNumber: 1) + ['id' => new ArrayHolder('create_tag_2', 'id')])
            ->setUnset(['createdAt', 'updatedAt', ])
        ;

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * GET /api/v1/tags
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 8) Get tags (expect two hits).
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function getEntitiesExpectTwoHits(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('list_tags_2')
            ->setUnset(['hydra:member' => ['createdAt', 'updatedAt', ]])
            ->setNamespaces(['update_tag_1', 'create_tag_2', ])
        ;

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * GET /api/v1/tags/[id]
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 9) Get second tag with id x.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function getSecondEntity(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('get_tag_2')
            ->setRequestType(ApiTestCaseWorker::REQUEST_TYPE_READ)
            ->setExpected($this->tagDataProvider->getEntityArray(recordNumber: 1) + ['id' => new ArrayHolder('create_tag_2', 'id')])
            ->setUnset(['createdAt', 'updatedAt', ])
            ->addParameter(new ArrayHolder('create_tag_2', 'id'));

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * DELETE /api/v1/tags/[id]
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 10) Delete first tag with id x.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function deleteFirstEntity(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('delete_tag_1')
            ->setRequestType(ApiTestCaseWorker::REQUEST_TYPE_DELETE)
            ->setExpected($this->tagDataProvider->getEntityArray() + ['id' => new ArrayHolder('create_tag_1', 'id')])
            ->setUnset(['createdAt', 'updatedAt', ])
            ->addParameter(new ArrayHolder('create_tag_1', 'id'));

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * GET /api/v1/tags
     * application/ld+json; charset=utf-8
     *
     * @test
     * @testdox Tag: 11) Get tags (expect one hit).
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws YadsException
     */
    public function getEntitiesExpectOneHit2(): void
    {
        /* Build API test case wrapper */
        $testCase = $this->getApiTestCaseWrapper('list_tags_1_2')
            ->setUnset(['hydra:member' => ['createdAt', 'updatedAt', ]])
            ->setNamespaces(['create_tag_2'])
        ;

        /* Make the test */
        $this->makeTest($testCase);
    }

    /**
     * Returns the context of this class.
     *
     * @return ?BaseContext
     */
    public function getContext(): ?BaseContext
    {
        return $this->tagContext;
    }
}
