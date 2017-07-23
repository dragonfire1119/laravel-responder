<?php

namespace Flugg\Responder\Http\Responses;

use BadMethodCallException;
use Flugg\Responder\Contracts\ResponseFactory;
use Flugg\Responder\TransformBuilder;
use InvalidArgumentException;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\SerializerAbstract;

/**
 * A builder class for building error responses.
 *
 * @package flugger/laravel-responder
 * @author  Alexander Tømmerås <flugged@gmail.com>
 * @license The MIT License
 *
 * @method self meta(array $meta)
 * @method self with(array | string $meta)
 * @method self without(array | string $meta)
 * @method self serializer(SerializerAbstract | string $serializer)
 * @method self paginator(IlluminatePaginatorAdapter $paginator)
 * @method self cursor(Cursor $cursor)
 */
class SuccessResponseBuilder extends ResponseBuilder
{
    /**
     * A builder for building transformed arrays.
     *
     * @var \Flugg\Responder\TransformBuilder
     */
    protected $transformBuilder;

    /**
     * A HTTP status code for the response.
     *
     * @var int
     */
    protected $status = 200;

    /**
     * Construct the builder class.
     *
     * @param \Flugg\Responder\Contracts\ResponseFactory $responseFactory
     * @param \Flugg\Responder\TransformBuilder          $transformBuilder
     */
    public function __construct(ResponseFactory $responseFactory, TransformBuilder $transformBuilder)
    {
        $this->transformBuilder = $transformBuilder;

        parent::__construct($responseFactory);
    }

    /**
     * Set resource data for the transformation.
     *
     * @param  mixed                                                          $data
     * @param  \Flugg\Responder\Transformers\Transformer|callable|string|null $transformer
     * @param  string|null                                                    $resourceKey
     * @return self
     */
    public function transform($data = null, $transformer = null, string $resourceKey = null): SuccessResponseBuilder
    {
        $this->transformBuilder->resource($data, $transformer, $resourceKey);

        return $this;
    }

    /**
     * Dynamically send calls to the transform builder.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return self|void
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, ['meta', 'with', 'without', 'serializer', 'paginator', 'cursor'])) {
            $this->transformBuilder->$name(...$arguments);

            return $this;
        }

        throw new BadMethodCallException;
    }

    /**
     * Get the serialized response output.
     *
     * @return array
     */
    protected function getOutput(): array
    {
        return $this->transformBuilder->transform();
    }

    /**
     * Validate the HTTP status code for the response.
     *
     * @param  int $status
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateStatusCode(int $status): void
    {
        if ($status < 100 || $status >= 400) {
            throw new InvalidArgumentException("{$status} is not a valid success HTTP status code.");
        }
    }
}