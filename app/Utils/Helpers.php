<?php

function moneyFormatHelper($value) {
	if ($value >= 0) {
		return '$' . number_format($value, 2);
	}
	return '-$' . number_format($value * -1, 2);
}

function numberFormatHelper($value) {
	return number_format($value, 2);
}

function wordFormatUWords($value) {
	if (strtolower($value) == 'mb financial group') {
		return 'MB Financial Group';
	}

	if (strtolower($value) == 'ml financial services') {
		return 'ML Financial Services';
	}

	return ucwords($value);
}

function getFirstName($value) {
	$nameArray = explode(' ', $value);

	return $nameArray[0];
}
