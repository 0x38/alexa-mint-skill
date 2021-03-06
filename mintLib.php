<?php

/*
 * Some functions to use MintAPI by Mike Rooney:
 *   https://github.com/mrooney/mintapi
 *
 */


//
// Fetch Mint account balances and return array of account objects
//
function getMintDetail($configArray) {
	$toReturn = false;
	$cmd = $configArray['mintApi'] . ' --session ' . $configArray['ius_session'] . ' --thx_guid ' . $configArray['thx_guid'] . ' ' . $configArray['username'] . ' ' . $configArray['password'] . ' 2> /dev/null';

	$result = `$cmd`;
	$resultObjArray = json_decode($result);

	if (is_array($resultObjArray) && (sizeof($resultObjArray) > 0)) {
		$toReturn = $resultObjArray;
	}

	return $toReturn;
}

//
// Fetch Mint budgets
// NOTE: Budgets are not currently working, some data from mintapi seems to be missing?
//
function getMintBudgetDetail($configArray) {
	$toReturn = false;
	$cmd = $configArray['mintApi'] . ' --budgets --session ' . $configArray['ius_session'] . ' --thx_guid ' . $configArray['thx_guid'] . ' ' . $configArray['username'] . ' ' . $configArray['password'] . ' 2> /dev/null';

	$result = `$cmd`;

	$resultObj = json_decode($result);

	if (is_object($resultObj)) {
		$toReturn = $resultObj;
	}

	return $toReturn;
}

//
//  Generate text for Alexa to speak based on balances
//
function getBalString($mintDetailObjArray,$type=false) {
	if (!$mintDetailObjArray) {
		return false;
	}
	$toReturn = '';
	$filterArray = array('bank','credit','investment','loan');
	if ($type) {
		if ($type == 'mortgage') {
			$type = 'loan';
		}
		if ($type == 'credit card') {
			$type = 'credit';
		}
		$filterArray = array($type);
	}
	foreach($mintDetailObjArray as $accountObj) {
		if (in_array($accountObj->accountType,$filterArray)) {
			if ($accountObj->isActive && !$accountObj->isClosed) {
				$toReturn .= "Balance for " . $accountObj->fiLoginDisplayName . " "
						. $accountObj->yodleeName . " is "
						. '$' . $accountObj->value . ".\n";
			}
		}
	}
	return $toReturn;
}
?>
