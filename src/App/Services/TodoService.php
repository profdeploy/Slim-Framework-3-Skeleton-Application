<?php namespace App\Services;

use App\Controllers\Api\v1\TodoController\ResponseMessageConstants;
use App\Entities\Todo;
use App\Repositories\TodoRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use ExtendedSlim\Exceptions\RecordNotFoundException;
use ExtendedSlim\Http\HttpCodeConstants;
use ExtendedSlim\Http\RestApiResponse;
use Exception;

class TodoService
{
    /** @var TodoRepository */
    private $todoRepository;

    /** @var Connection */
    private $connection;

    /**
     * @param TodoRepository $todoRepository
     * @param Connection     $connection
     */
    public function __construct(TodoRepository $todoRepository, Connection $connection)
    {
        $this->todoRepository = $todoRepository;
        $this->connection     = $connection;
    }

    /**
     * @param string $name
     * @param int    $userId
     *
     * @return RestApiResponse
     * @throws ConnectionException
     */
    public function create(string $name, int $userId)
    {
        $this->connection->beginTransaction();

        try
        {
            $this->todoRepository->create(new Todo(null, $name, $userId));

            $this->connection->commit();

            return new RestApiResponse();
        }
        catch (Exception $e)
        {
            $this->connection->rollBack();

            return new RestApiResponse(
                null,
                ResponseMessageConstants::UNKNOWN_ERROR_ID,
                ResponseMessageConstants::UNKNOWN_ERROR_MESSAGE,
                HttpCodeConstants::BAD_REQUEST
            );
        }
    }

    /**
     * @return Todo[]
     */
    public function search(): array
    {
        return $this->todoRepository->search();
    }

    /**
     * @param integer $id
     *
     * @return Todo
     * @throws RecordNotFoundException
     */
    public function getById(int $id): Todo
    {
        return $this->todoRepository->getById($id);
    }
}