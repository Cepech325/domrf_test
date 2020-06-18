<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;

if (!Loader::includeModule("iblock"))
	return;

$arIBlockTypes = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$iblockFilter = (
	!empty($arCurrentValues['IBLOCK_TYPE'])
	? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
	: array('ACTIVE' => 'Y')
);
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch())
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
unset($arr, $rsIBlock, $iblockFilter);

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MES_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockTypes,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"NAME" => GetMessage("MES_IBLOCK_ID"),
			"PARENT" => "BASE",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y"
		),
		"ELEMENT_ID" => array(
			"NAME" => GetMessage("MES_ELEMENT_ID"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"ELEMENT_CODE" => array(
			"NAME" => GetMessage("MES_ELEMENT_CODE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => ''
		),
		"WIDTH" => array(
			"NAME" => GetMessage("MES_WIDTH"),
			"TYPE" => "STRING",
			"PARENT" => "BASE"
		),
		"COMMENTS_COUNT" => array(
			"NAME" => GetMessage("MES_COMMENTS_COUNT"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "5"
		)
	)
);
