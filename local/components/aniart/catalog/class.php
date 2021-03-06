<?php

class AniartCatalogComponent extends CBitrixComponent
{

    public function __construct($component = null)
    {
        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams)
    {
        return parent::onPrepareComponentParams($arParams); // TODO: Change the autogenerated stub
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $smartBase = ($this->arParams["SEF_URL_TEMPLATES"]["section"] ? $this->arParams["SEF_URL_TEMPLATES"]["section"] : "#SECTION_ID#/");
        $arDefaultUrlTemplates404 = [
            "sections" => "",
            "section" => "#SECTION_ID#/",
            "element" => "#SECTION_ID#/#ELEMENT_ID#/",
            "compare" => "compare.php?action=COMPARE",
            "smart_filter" => $smartBase . "filter/#SMART_FILTER_PATH#/apply/"
        ];

        $arDefaultVariableAliases404 = [];

        $arDefaultVariableAliases = [];

        $arComponentVariables = [
            "SECTION_ID",
            "SECTION_CODE",
            "ELEMENT_ID",
            "ELEMENT_CODE",
            "action",
        ];

        $arVariables = [];
        $sectionsUrl = '';
        $searchPage = false;
        $rootDir = false;

        $engine = new CComponentEngine($this);
        if(\Bitrix\Main\Loader::includeModule('iblock'))
        {
            $engine->addGreedyPart("#SECTION_CODE_PATH#");
            $engine->setResolveCallback(["CIBlockFindTools", "resolveComponentEngine"]);
        }
        $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $this->arParams["SEF_URL_TEMPLATES"]);
        $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);

        $type = i18n()->getRawDir($this->arParams['SEF_FOLDER']) == '/' ? 'collections' : 'catalog';

        $componentPage = $engine->guessComponentPath(
                $this->arParams["SEF_FOLDER"], $arUrlTemplates, $arVariables
        );
        
        $requestUrl = i18n()->getRawDir($APPLICATION->GetCurPage(true));
        if(substr_count($requestUrl, '/nashy_trendy/') > 0 || substr_count($requestUrl, '/sale/') > 0)
        {
            $componentPage = 'uniq';
            $type = str_replace('/', '',  $this->arParams["SEF_FOLDER"]);
            $sefController = '\Aniart\Main\Seo\CustomFilterSEFController';
            $sectionsUrl = $sefController::removeSefUrlPart($requestUrl);
            if($requestUrl == '/nashy_trendy/index.php')
            {
                $showPanel = \COption::GetOptionString("aniart.main", "show_trends_sort");
                if($showPanel == 'Y')
                {
                    $buttonText = 'Не показывать сортировку';
                }
                else
                {
                    $buttonText = 'Показывать сортировку';
                }
                global $APPLICATION;
                global $USER;
			    $arGroups = CUser::GetUserGroup($USER->GetID());
			    if(in_array(6, $arGroups) || in_array(1, $arGroups))
                {
                    $APPLICATION->AddPanelButton(
                        Array(
                            "ID" => "setShowSort", //определяет уникальность кнопки
                            "TEXT" => $buttonText,
                            "TYPE" => "SMALL", //BIG - большая кнопка, иначе маленькая
                            "MAIN_SORT" => 2000, //индекс сортировки для групп кнопок
                            "SORT" => 10, //сортировка внутри группы
                            "HREF" => "javascript:App.Panel.setShowSortTrends({'val':'" . $showPanel . "'})", //или javascript:MyJSFunction())
                            "ICON" => "bx-panel-site-structure-icon", //название CSS-класса с иконкой кнопки
                        ),
                        $bReplace = false //заменить существующую кнопку?
                    );
                }
            }
        }
        if($requestUrl == '/sale/index.php' || $requestUrl == '/catalog/index.php')
        {
            $showPanel = \COption::GetOptionString("aniart.main", "show_filter_".$type);
            if($showPanel == 'Y' || empty($showPanel))
            {
                $buttonText = 'Не показывать фильтр и листинг';
            }
            else
            {
                $buttonText = 'Показывать фильтр и листинг';
            }
            global $APPLICATION;
            global $USER;
            $arGroups = CUser::GetUserGroup($USER->GetID());
            if(in_array(6, $arGroups) || in_array(1, $arGroups))
            {
                $APPLICATION->AddPanelButton(
                    Array(
                        "ID" => "setShowSort", //определяет уникальность кнопки
                        "TEXT" => $buttonText,
                        "TYPE" => "SMALL", //BIG - большая кнопка, иначе маленькая
                        "MAIN_SORT" => 2000, //индекс сортировки для групп кнопок
                        "SORT" => 10, //сортировка внутри группы
                        "HREF" => "javascript:App.Panel.setShowFilter({'val':'" . $showPanel . "', 'dir': '".$type."'})", //или javascript:MyJSFunction())
                        "ICON" => "bx-panel-site-structure-icon", //название CSS-класса с иконкой кнопки
                    ),
                    $bReplace = false //заменить существующую кнопку?
                );
            }
            $rootDir = true;
        }
        if($componentPage == 'section' && $type !== 'collections')
        {
            $section = false;
            $codes = explode('/', $arVariables['SECTION_CODE_PATH']);
            if(is_array($codes))
            {
                while($code = array_pop($codes))
                {
                    /*
                    * На страницу поиска нужно исключение для 404
                    */
                    if($code == 'search') $searchPage = true;

                    $section = $this->getSectionByCode($code);
                    if($section)
                    {
                        break;
                    }
                }
            }
            if(!$section)
            { //считаем, что это фильтр для корня каталога
                $componentPage = false;
            }
        }

        if(!$componentPage)
        {
            $componentPage = 'sections';
            $arVariables = [
                'SECTION_ID' => 0,
                'SECTION_CODE' => '',
                'SECTION_CODE_PATH' => '',
                'ROOT' => true
            ];
        }
        if(in_array($componentPage, ['section', 'sections']))
        {
            /**
             * @var \Aniart\Main\Seo\CustomFilterSEFController $sefController
             */
            $sefController = '\Aniart\Main\Seo\CustomFilterSEFController';
            if($componentPage == 'sections')
            {
                //случай для фильтрация относительно корня каталога
                $sectionsUrl = '/catalog/index.php';
                /*
                 * Исключение для раздела распродажи
                 */

                if(in_array($requestUrl, $arUniqPage)) $uniqPage = true;
                if(
                        ($requestUrl !== $sectionsUrl) &&
                        !$sefController::determineSefUrl($sectionsUrl, $requestUrl) &&
                        !$searchPage && !$uniqPage && !$trend
                )
                {
                    $this->process404();
                }
            }
            else
            {
                $sectionsUrl = $sefController::removeSefUrlPart($requestUrl);
            }
            if(
                    !$sefController::determineSefUrl($sectionsUrl, $requestUrl) &&
                    !$sefController::determineSefUrl('/', $requestUrl)
            )
            {
                $sefController::bindFilteredPropsToUrl('/', 0);
                if($sefController::determineSefUrl('/', $requestUrl))
                {
                    registry('sef_redirect', $_SERVER['REQUEST_URI']);
                }
            }
            $componentPage = $engine->guessComponentPath(
                    $this->arParams["SEF_FOLDER"], $arUrlTemplates, $arVariables, i18n()->getLangDir($sectionsUrl)
            );
        }

        if($searchPage)
            $componentPage = "search";

        $b404 = false;
        if(!$componentPage)
        {
            $componentPage = "sections";
            if(!isset($arVariables['ROOT']))
            {
                $b404 = true;
            }
        }

        if($componentPage == "section")
        {
            if(isset($arVariables["SECTION_ID"]))
                $b404 |= (intval($arVariables["SECTION_ID"]) . "" !== $arVariables["SECTION_ID"]);
            else
            {
                $b404 |= !(isset($arVariables["SECTION_CODE"]) || isset($arVariables['SECTION_CODE_PATH']));
            }
        }
        if($b404 && CModule::IncludeModule('iblock'))
        {
            $folder404 = str_replace("\\", "/", $this->arParams["SEF_FOLDER"]);
            if($folder404 != "/")
                $folder404 = "/" . trim($folder404, "/ \t\n\r\0\x0B") . "/";
            if(substr($folder404, -1) == "/")
                $folder404 .= "index.php";

            if($folder404 != $APPLICATION->GetCurPage(true))
            {
                $this->process404();
            }
        }

        CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
        $this->arResult = [
            "FOLDER" => $this->arParams["SEF_FOLDER"],
            "URL_TEMPLATES" => $arUrlTemplates,
            "VARIABLES" => $arVariables,
            "ALIASES" => $arVariableAliases,
            "SEF_CONTROLLER_CLASS" => $sefController,
            "SEF_PAGE_URL" => $sectionsUrl,
            "TYPE" => $type,
            "ROOT_DIR" => $rootDir
        ];
        
        //товары коллекции
        if($componentPage == 'collection')
        {
            if(!($this->arResult['COLLECTION'] = $this->getCurrentCollection()))
            {
                $this->process404();
            }
            $this->arResult['VARIABLES']['SECTION_CODE'] = $this->arResult['VARIABLES']['SECTION_CODE_PATH'] = '';
        }
        //товары каталога
        if(!($this->arResult['SECTION'] = $this->getCurrentSection()))
        {
            $this->process404();
        }
        $this->IncludeComponentTemplate($componentPage);
    }

    private function process404()
    {
        \Bitrix\Iblock\Component\Tools::process404('', true, true, true);
    }
    
    private function getCurrentSection()
    {
        $sectionCode = $this->arResult['VARIABLES']['SECTION_CODE'] ?: '';
        if(!$sectionCode && $sectionPath = $this->arResult['VARIABLES']['SECTION_CODE_PATH'])
        {
            $path = array_reverse(explode('/', $sectionPath));
            foreach($path as $part)
            {
                if($section = $this->getSectionByCode($part)) return $section;
            }
        }
        if(!$section) return $this->setEmptySection();
    }
    private function getSectionByCode($sectionCode)
    {
        $sectionCode = trim($sectionCode);
        /**
         * @var \Aniart\Main\Repositories\ProductSectionsRepository $sectionsRepository
         */
        $sectionsRepository = app('ProductSectionsRepository');
        if($sectionCode)
        {
            /**
             * @var \Aniart\Main\Cacher\AbstractCacheCell $cacheCell
             */
            $cacheCell = app('CacheCell', ['catalog_section_' . $sectionCode, $this->arParams['CACHE_TIME']]);
            $sectionData = $cacheCell->load();
            if(is_null($sectionData))
            {
                $section = $sectionsRepository->getByCode($sectionCode);
                if($section)
                {
                    $cacheCell->save($section->toArray());
                }
            }
            else
            {
                $section = $sectionsRepository->newInstance($sectionData);
            }
        }
        return $section;
    }
    private function setEmptySection()
    {
        $sectionsRepository = app('ProductSectionsRepository');
        return $sectionsRepository->newInstance([
            'ID' => 0,
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => PRODUCTS_IBLOCK_ID,
            'NAME' => 'Каталог',
            'SECTION_PAGE_URL' => $this->arParams['SEF_FOLDER']
        ]);        
    }
}