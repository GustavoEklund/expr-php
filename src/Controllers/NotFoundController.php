<?php

namespace Controllers;

use Classes\Controller;
use Classes\Request;
use Classes\Response;
use RuntimeException;

/**
 * Class NotFoundController
 * @package Controllers
 */
class NotFoundController extends Controller
{
	/**
	 * @param Request $req
	 * @param Response $res
	 * @return string
	 * @throws \JsonException
	 */
    public static function index(Request $req, Response $res): string
    {
    	return $res->status(404)->send('404');
//		throw new RuntimeException('Api endpoint not recognized.', 404);
    } // index
} // NotFoundController
