<?php
/**
 * COPS (Calibre OPDS PHP Server) main script
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Sébastien Lucas <sebastien@slucas.fr>
 * @author     mikespub
 *
 */
use SebLucas\Cops\Input\Config;
use SebLucas\Cops\Input\Request;
//use SebLucas\Cops\Output\OPDSRenderer;
use SebLucas\Cops\Output\KiwilanOPDS as OPDSRenderer;
use SebLucas\Cops\Pages\PageId;

require_once __DIR__ . '/config.php';

$request = new Request();
$page = $request->get('page', PageId::INDEX);
// @checkme set page based on path info here
$path = $_SERVER["PATH_INFO"] ?? "";
if ($page == PageId::INDEX && $path == "/search") {
    $page = PageId::OPENSEARCH;
}
$query = $request->get('query');  // 'q' by default for php-opds
if ($query) {
    $page = PageId::OPENSEARCH_QUERY;
}

if (Config::get('fetch_protect') == '1') {
    session_start();
    if (!isset($_SESSION['connected'])) {
        $_SESSION['connected'] = 0;
    }
}

header('Content-Type:application/xml');

$OPDSRender = new OPDSRenderer();

switch ($page) {
    case PageId::OPENSEARCH :
        echo $OPDSRender->getOpenSearch($request);
        return;
    default:
        $currentPage = PageId::getPage($page, $request);
        $currentPage->InitializeContent();
        echo $OPDSRender->render($currentPage, $request);
        return;
}
