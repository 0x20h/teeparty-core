<?php

namespace Teeparty\Schema;

use JsonSchema\Uri\UriRetriever;
use JsonSchema\RefResolver;
use JsonSchema\Validator as JsonSchemaValidator;

class Validator {

    private $lastErrors = array();

    /**
     * Validate schema against the given JSON string.
     *
     * @param string $schema The schema to use.
     * @param mixed $data JSON decoded object to validate.
     *
     * @return bool True, if data validates. False otherwise.
     */
    public function validate($schema, $data)
    {
        $this->lastErrors = array();

        try {
            if (!is_string($schema) || !preg_match("#^[a-zA-Z0-9/]+$#", $schema)) {
                throw new Exception('Invalid schema requested. (must match [a-zA-Z0-9/]+)');
            }
            
            $file = realpath(__DIR__ . '/../../../../schema'. '/' . $schema . '.json');
            
            if (!$file) {
                throw new Exception('schema not found');
            }
            
            $file = 'file://' . $file;
            $retriever = new UriRetriever;
            $schema = $retriever->retrieve($file);
            
            $refResolver = new RefResolver($retriever);
            $refResolver->resolve($schema, $file);

            $validator = new JsonSchemaValidator();
            $validator->check($data, $schema);
            $valid = $validator->isValid();
            $this->lastErrors = $validator->getErrors();
            return $valid;
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }


    /**
     * Validate the given JSON string against the schema.
     *
     * @param string $schema The schema to validate.
     * @param string $json A JSON string.
     *
     * @return bool True, if validation is successful. False otherwise.
     */
    public function validateJSON($schema, $json)
    {
        return $this->validate($schema, json_decode($json));
    }


    /**
     * @return array errors from last validation.
     */
    public function getLastErrors()
    {
        return $this->lastErrors;
    }
}
