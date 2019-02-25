<?php

namespace Convenia\GraphQLClient\Traits;

use \Convenia\GraphQLClient\Field;
use \Convenia\GraphQLClient\Query;

/**
 * Sumary
 * @method aray graphqlMutationCall(string name, array params, array fields) Make a GraphQL mutation call
 * @method null assertGraphQLFields(array fields) Check if the response contains all desired fields
 */
trait MakeGraphQLRequests
{
	protected $endpoint = '/graphql';

	protected $graphql;

    private $query;

    protected function graphqlMutate(string $name, array $params, array $fields): array
    {
        $this->makeRequest($name, $params, $fields)

       	return $this->graphql->mutate($this->query)->getData();
    }

    protected function graphqlQuery(string $name, array $params, array $fields): array
    {
        $this->makeRequest($name, $params, $fields)

        return $this->graphql->mutate($this->query)->getData();
    }

    private function makeRequest(string $name, array $params, array $fields) {
        $this->graphql = new \Convenia\GraphQLClient\LaravelTestGraphQLClient(
          $this->app,
            $this->endpoint
        );

        $params = $params;

        $fields = $this->mapFields($fields);

        $this->query = new Query($name, $params, $fields);
    }

    protected function assertGraphQLFields($fields)
    {
        $this->graphql->assertGraphQLFields($fields, $this->query);
    }

    protected function createQuery($name, $params, $fields): Query {
    	return new Query($name, $params, $this->mapFields($fields));
    }

    private function mapFields(array $fields): array {
    	$f = [];

    	foreach ($fields as $key => $value) {
    		if(is_array($value)) {
    			// Sub Query
    			$params = $value['params'];
    			unset($value['params']);
       	 		$f[] = $this->createQuery($key, $params, $value);
    	 	}
    	 	$f[] = new Field($value);
    	}

    	return $f;
    }
}
