<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Iblock;
use Bitrix\Main;

if (!isset($arParams['CACHE_TIME']))
	$arParams['CACHE_TIME'] = 36000000;
$arParams['CACHE_GROUPS'] = trim($arParams['CACHE_GROUPS']);
if (!isset($arParams['CACHE_GROUPS']) || $arParams['CACHE_GROUPS'] != 'N')
	$arParams['CACHE_GROUPS'] = 'Y';

$arParams['IBLOCK_TYPE']= trim($arParams['IBLOCK_TYPE']);
$arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);
$arParams['ELEMENT_ID'] = intval($arParams['ELEMENT_ID']);
$arParams['ELEMENT_CODE'] = ($arParams['ELEMENT_ID'] > 0 ? '' : trim($arParams['ELEMENT_CODE']));
$arParams['WIDTH'] = intval($arParams["WIDTH"]);
$arParams['COMMENTS_COUNT'] = intval($arParams['COMMENTS_COUNT']);

if ($this->StartResultCache(false, ($arParams['CACHE_GROUPS'] === 'N' ? false: $USER->GetGroups())))
{
	if (!Loader::includeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError("Ќет модул€ јйблок");
		return 0;
	}

	$arResultModules = array(
		'iblock' => true,
	);

	$arResult['ELEMENT'] = array();
	$arResult['ERRORS'] = array();
	$arResult['MODULES'] = $arResultModules;

	if ($arParams["ELEMENT_ID"] <= 0)
	{
		if ($arParams["ELEMENT_CODE"] !== '')
		{
			$findFilter = array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"IBLOCK_LID" => SITE_ID,
				"IBLOCK_ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
				"MIN_PERMISSION" => 'R'
			);
			
			$findFilter['ACTIVE_DATE'] = 'Y';
			
			$arParams["ELEMENT_ID"] = CIBlockFindTools::GetElementID(
				$arParams["ELEMENT_ID"],
				$arParams["~ELEMENT_CODE"],
				false,
				false,
				$findFilter
			);
		}
	}
	if($arParams["ELEMENT_ID"] > 0)
	{
		$blogGroupID = 0;
		$blogID = 0;
		$propBlogPostID = 0;
		$propBlogCommentsCountID = 0;
		$arResult['BLOG_DATA'] = array(
			'BLOG_URL' => $arParams['BLOG_URL'],
			'BLOG_ID' => 0,
			'BLOG_POST_ID_PROP' => 0,
			'BLOG_COMMENTS_COUNT_PROP' => 0,
			'BLOG_POST_ID' => 0,
			'IBLOCK_SITES' => array()
		);

		$arSelect = array(
			"ID",
			"IBLOCK_ID",
			"NAME",
			"PREVIEW_TEXT",
			"DETAIL_PAGE_URL",
			"PREVIEW_TEXT_TYPE",
			"DATE_CREATE",
			"CREATED_BY"
		);

		$arFilter = array(
			"ID" => $arParams["ELEMENT_ID"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"IBLOCK_LID" => SITE_ID,
			"IBLOCK_ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
			"MIN_PERMISSION" => 'R',
			"SHOW_HISTORY" => "Y"
		);
		$arFilter['ACTIVE_DATE'] = 'Y';

		$arFilter["ACTIVE"] = "Y";

		$rsElement = CIBlockElement::GetList(
			array(),
			$arFilter,
			false,
			false,
			$arSelect
		);
		if ($arElement = $rsElement->GetNext())
		{
			$arResult['ELEMENT'] = $arElement;

			$protocol = (CMain::IsHTTPS()) ? 'https://' : 'http://';

			$arResult['URL_TO_COMMENT'] = $protocol.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

			if($arParams["WIDTH"] > 0)
				$arResult["WIDTH"] = $arParams["WIDTH"];

			$this->IncludeComponentTemplate();
		}
		else
		{
			$this->AbortResultCache();
			ShowError("Ёлемент не найден");
			return 0;
		}
	}
	else
	{
		$this->AbortResultCache();
		ShowError("Ёлемент не найден");
		return 0;
	}
}
