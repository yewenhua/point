<?php

	$REQUEST_URL = isset($_REQUEST['REQUEST_URL']) ? $_REQUEST['REQUEST_URL'] : '';
	$METHOD_TYPE = isset($_REQUEST['METHOD_TYPE']) ? $_REQUEST['METHOD_TYPE'] : '';

	if ($REQUEST_URL != '') {

		$data = $_REQUEST;

		// 去除自用的字段
		unset($data['REQUEST_URL']);
		unset($data['METHOD_TYPE']);

		if ($METHOD_TYPE == 'GET') { // get

			$result = file_get_contents($REQUEST_URL . '?' . http_build_query($data));

		} else if ($METHOD_TYPE == 'POST' || $METHOD_TYPE == 'PUT') { // post | put

			$postdata = '';
			foreach ($data as $key => $value) {
				if ($postdata != '') {
					$postdata .= '&';
				}
				$postdata .= $key . '=' . $value;
			}

			$opts = array(
				'http' => array(
					'method' => $METHOD_TYPE,
					'header' => 'Content-type:application/x-www-form-urlencoded;charset:UTF-8',
					'content' => $postdata
				)
			);

			$result = file_get_contents($REQUEST_URL, false, stream_context_create($opts));

		} else if ($METHOD_TYPE == 'DELETE') { // delete

			$opts = array (
				'http' => array (
					'method' => $METHOD_TYPE,
					'header' => 'Content-type:application/x-www-form-urlencoded;charset:UTF-8'
				)
			);

			$result = file_get_contents($REQUEST_URL, false, stream_context_create($opts));

		}

	}

	echo $result;

?>