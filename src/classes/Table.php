<?php

namespace Danupe\Core\Classes;

class Table
{

    public array $options = [];
    public string $url = "";
    public bool $ajax = false;
    public array $data = [];

    public array $links = [];

    public function setLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }

    public function setAjax(bool $ajax): self
    {
        $this->ajax = $ajax;
        return $this;
    }

    public function setData(array $data = []): self
    {
        $this->data = $data;
        return $this;
    }

    public function setOptions(array $options = []): self
    {
        $this->options = $options;
        return $this;
    }

    public function setUrl(string $url = ""): self
    {
        $this->url = $url;
        return $this;
    }

    public function render(): string
    {
        if ($this->ajax) {
            return $this->buildAjax();
        } else {
            return $this->buildHtml();
        }
    }

    private function buildHtml(): string
    {

        if (empty($this->data)) {
            echo danupe()->view()->render('core', 'components/alert', [
                'type' => 'warning',
                'title' => 'Warning',
                'text' => 'no data found',
            ]);
            exit;
        }



        $html = "<div class='flex w-full overflow-x-auto'>
                <table class='table'>";

        $headers = array_keys(danupe()->data()->get($this->data, 0, []));

        if ($this->links) {
            $headers[] = "actions";
        }


        $html .= "<tr>";
        foreach ($headers as $header) {
            $html .= "<th>" . htmlspecialchars($header) . "</th>";
        }
        $html .= "</tr>";

        foreach ($this->data as $row) {
            $html .= "<tr>";
            foreach ($row as $cell) {
                if ($edit = danupe()->data()->get($this->links, 'edit')) {
                    $html .= "<td><a href='" . danupe()->data()->get($edit, 'url') . $row[danupe()->data()->get($this->links, 'edit.key')]."' >" . htmlspecialchars($cell) . "</a></td>";
                } else {
                    $html .= "<td>" . htmlspecialchars($cell) . "</td>";
                }
            }

            if ($this->links) {
                $html .= "<td>";
                foreach ($this->links as $key => $link) {
                    $url = danupe()->data()->get($link, 'url') . $row[danupe()->data()->get($link, 'key')];
                    $html .= "<a href='" . htmlspecialchars($url) . "' title='" . $key . "'><i class='" . danupe()->data()->get($link, 'icon') . "'></i></a> ";
                }
                $html .= "</td>";
            }

            $html .= "</tr>";
        }

        $html .= "</table></div>";
        return $html;
    }

    private function buildAjax(): string
    {
        // Guard: ensure we have at least one row to derive headers, otherwise use provided option headers
        $headers = [];
        if (!empty($this->data)) {
            $headers = array_keys($this->data[0]);
        } elseif (!empty($this->options['headers']) && is_array($this->options['headers'])) {
            $headers = $this->options['headers'];
        }

        $pageSizeOptions = $this->options['page_sizes'] ?? [10,25,50,100];
        $searchPlaceholder = $this->options['search_placeholder'] ?? 'Suche...';
        $noDataText = $this->options['no_data'] ?? 'Keine Daten';

        $html = "<div x-data=\"littleBIGtable({url: '/" . $this->url . "'})\" x-init=\"init()\">";

        // Controls (search, page size, pagination, status)
        $html .= "<div class='table-controls flex flex-wrap gap-2 items-center mb-2'>";
        // Search
    $html .= "<input type='text' class='table-search input' placeholder='" . htmlspecialchars($searchPlaceholder) . "' x-model=\"params.search\" @input.debounce.500ms=\"typeof doSearch==='function' && doSearch()\" />";
        // Page size
        $html .= "<select class='table-limit select' x-model=\"params.limit\" @change=\"setLimit()\">";
        foreach ($pageSizeOptions as $opt) {
            $html .= "<option value='" . (int)$opt . "'>" . (int)$opt . "</option>";
        }
        $html .= "</select>";
        // Pagination buttons
        $html .= "<div class='table-pager flex items-center gap-1'>";
        $html .= "<button type='button' class='btn btn-xs' @click=\"goFirstPage()\" :disabled=\"getCurrentPage()==1\">&laquo;</button>";
        $html .= "<button type='button' class='btn btn-xs' @click=\"goPrevPage()\" :disabled=\"getCurrentPage()==1\">&lsaquo;</button>";
        $html .= "<span class='px-1 text-sm' x-text=\"getCurrentPage() + ' / ' + getTotalPages()\"></span>";
        $html .= "<button type='button' class='btn btn-xs' @click=\"goNextPage()\" :disabled=\"getCurrentPage()==getTotalPages()\">&rsaquo;</button>";
        $html .= "<button type='button' class='btn btn-xs' @click=\"goLastPage()\" :disabled=\"getCurrentPage()==getTotalPages()\">&raquo;</button>";
        $html .= "</div>"; // pager
        // Status summary
        $html .= "<div class='table-status text-xs' x-html=\"meta.status\"></div>";
        $html .= "</div>"; // controls

        // Table structure
        $html .= "<div class='flex w-full overflow-x-auto'><table class='table'>";
        if ($headers) {
            $html .= "<thead><tr>";
            foreach ($headers as $header) {
                $safe = htmlspecialchars($header);
                $html .= "<th class='cursor-pointer select-none' @click=\"doSort('$safe')\">$safe <span x-html=\"getSortIcon('$safe')\"></span></th>";
            }
            if ($this->links) {
                $html .= "<th>actions</th>";
            }
            $html .= "</tr></thead>";
        }
        $html .= "<tbody>";
    $emptyColspan = count($headers) + ($this->links ? 1 : 0);
    $html .= "<template x-if=\"!rows.length && !meta.loading\"><tr><td colspan='" . max(1,$emptyColspan) . "' class='text-center text-sm italic'>" . htmlspecialchars($noDataText) . "</td></tr></template>";
        if ($headers) {
            $html .= "<template x-for=\"row in rows\" :key=\"row.id ? row.id : JSON.stringify(row)\"><tr>";
            foreach ($headers as $key) {
                $safeKey = htmlspecialchars($key);
                $html .= "<td x-text=\"row.$safeKey\"></td>";
            }
            if ($this->links) {
                $html .= "<td>";
                foreach ($this->links as $linkKey => $link) {
                    $icon = htmlspecialchars(danupe()->data()->get($link,'icon','fas fa-edit'));
                    $urlBase = htmlspecialchars(danupe()->data()->get($link,'url',''));
                    $rowKey = htmlspecialchars(danupe()->data()->get($link,'key','id'));
                    // Alpine expression builds full url per row
                    $html .= "<a class='inline-block px-1 text-primary hover:underline' :href=\"'$urlBase' + row.$rowKey\" title='" . htmlspecialchars($linkKey) . "'><i class='$icon'></i></a> ";
                }
                $html .= "</td>";
            }
            $html .= "</tr></template>";
        }
        $html .= "</tbody>";
        $html .= "</table></div>";
        $html .= "</div>"; // x-data root
        return $html;
    }
}
