<?php
/**
 * COPS (Calibre OPDS PHP Server) class file
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Sébastien Lucas <sebastien@slucas.fr>
 */

namespace SebLucas\Cops\Pages;

use SebLucas\Cops\Calibre\Book;
use SebLucas\Cops\Calibre\CustomColumn;

use function SebLucas\Cops\Request\getURLParam;

class PageCustomDetail extends Page
{
    public function InitializeContent()
    {
        $customId = getURLParam("custom", null);
        $custom = CustomColumn::createCustom($customId, $this->idGet);
        $this->idPage = $custom->getEntryId();
        $this->title = $custom->value;
        [$this->entryArray, $this->totalNumber] = Book::getBooksByCustom($custom, $this->idGet, $this->n);
    }
}