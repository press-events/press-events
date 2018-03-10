<?php
/**
 * Press Events Data Exception Class
 *
 * @version 1.0.0
 * @package PressEvents/Exception
 * @author Burn Media Ltd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Data_Exception class.
 */
class BM_PE_Data_Exception extends Exception {

	/**
	 * Sanitized error code.
	 *
	 * @var string
	 */
	protected $error_code;

	/**
	 * Error extra data.
	 *
	 * @var array
	 */
	protected $error_data;

	/**
	 * Setup exception.
	 *
	 * @param string $code
	 * @param string $message
	 * @param int    $http_status_code
	 * @param array  $data
	 */
	public function __construct( $code, $message, $http_status_code = 400, $data = array() ) {
		$this->error_code = $code;
		$this->error_data = array_merge( array( 'status' => $http_status_code ), $data );

		parent::__construct( $message, $http_status_code );
	}

	/**
	 * Returns the error code.
	 *
	 * @return string
	 */
	public function getErrorCode() {
		return $this->error_code;
	}

	/**
	 * Returns error data.
	 *
	 * @return array
	 */
	public function getErrorData() {
		return $this->error_data;
	}
}
