<?php
    namespace App\API;

    class TroubleFreeException extends \Exception
    {
        /**
         * The response from the TroubleFree API
         * 
         * @var array $response
         */
        private $response;

        /**
         * Create a new TroubleFreeException
         * 
         * @param string $message
         * @param array $response
         * @return void
         */
        public function __construct($message, $response)
        {
            parent::__construct($message);

            $this->response = $response;
        }

        /**
         * Get the response from the TroubleFree API
         * 
         * @return array
         */
        public function getResponse()
        {
            return $this->response;
        }
    }